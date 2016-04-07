<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPlugin extends shopPlugin {

    public static $plugin_id = array('shop', 'bonuses');

    public function frontendMyNav() {
        if ($this->getSettings('status') && $this->getSettings('frontend_my_nav')) {
            return '<a href="' . wa()->getRouteUrl('/frontend/myBonuses') . '">Бонусы</a>';
        }
    }

    public function backendOrderEdit($order) {
        if ($this->getSettings('status')) {
            $view = wa()->getView();
            $currency = wa('shop')->getConfig()->getCurrency(true);
            $view->assign('currency', $currency);
            $template_path = wa()->getAppPath('plugins/bonuses/templates/BackendOrderEdit.html', 'shop');
            $html = $view->fetch($template_path);
            return $html;
        }
    }

    public function backendProductEdit($product) {
        if ($this->getSettings('status')) {
            $view = wa()->getView();
            $view->assign('product', $product);
            $template_path = wa()->getAppPath('plugins/bonuses/templates/BackendProductEdit.html', 'shop');
            $html = $view->fetch($template_path);
            return array('basics' => $html);
        }
    }

    public function frontendProduct($product) {
        if ($this->getSettings('frontend_product_output')) {
            $html = self::displayFrontendProduct();
            return array($this->getSettings('frontend_product_output') => $html);
        }
    }

    public static function displayFrontendProduct() {
        $app_settings_model = new waAppSettingsModel();
        if (!$app_settings_model->get(self::$plugin_id, 'status')) {
            return false;
        }
        $view = wa()->getView();
        $view->assign('settings', $app_settings_model->get(self::$plugin_id));
        $template_path = wa()->getDataPath('plugins/bonuses/templates/FrontendProduct.html', false, 'shop', true);
        if (!file_exists($template_path)) {
            $template_path = wa()->getAppPath('plugins/bonuses/templates/FrontendProduct.html', 'shop');
        }
        $html = $view->fetch($template_path);
        return $html;
    }

    public function frontendCart() {
        if ($this->getSettings('frontend_cart')) {
            return self::displayFrontendCart();
        }
    }

    public static function displayFrontendCart() {
        $app_settings_model = new waAppSettingsModel();
        if (!$app_settings_model->get(self::$plugin_id, 'status')) {
            return false;
        }

        $total_bonus = shopBonuses::getContactBonus();

        $bonuses_use = wa()->getStorage()->read('bonuses_use');

        $view = wa()->getView();
        $view->assign('settings', $app_settings_model->get(self::$plugin_id));
        $view->assign('total_bonus', shop_currency_html($total_bonus));
        $view->assign('bonuses_use', $bonuses_use);
        $template_path = wa()->getDataPath('plugins/bonuses/templates/FrontendCart.html', false, 'shop', true);
        if (!file_exists($template_path)) {
            $template_path = wa()->getAppPath('plugins/bonuses/templates/FrontendCart.html', 'shop');
        }
        $html = $view->fetch($template_path);
        return $html;
    }

    protected function applyOrderBonus($order_id) {
        $order_model = new shopOrderModel();
        $order = $order_model->getOrder($order_id);
        $bonus = shopBonuses::getOrderBonus($order_id);
        $bonus_formatted = waCurrency::format('%{s}', $bonus, $order['currency']);
        $comment = sprintf_wp('Bonus for the order %s totalling %s', shopHelper::encodeOrderId($order_id), $bonus_formatted);
        shopBonuses::applyBonus($order['contact_id'], $bonus, $order_id, $comment);
    }

    public function orderActionPay($params) {
        if ($this->getSettings('status') && $this->getSettings('order_status') == 'pay') {
            $this->applyOrderBonus($params['order_id']);
        }
    }

    public function orderActionComplete($params) {
        if ($this->getSettings('status') && $this->getSettings('order_status') == 'complete') {
            $this->applyOrderBonus($params['order_id']);
        }
    }

    public function orderActionRefund($params) {
        if ($this->getSettings('status')) {
            $order_id = $params['order_id'];
            $order_model = new shopOrderModel();
            $order = $order_model->getOrder($order_id);
            $bonus = shopBonuses::getOrderBonus($order_id);
            $comment = '';
            shopBonuses::applyBonus($order['contact_id'], -$bonus, $order_id, $comment, shopAffiliateTransactionModel::TYPE_ORDER_CANCEL);
        }
    }

    public function orderCalculateDiscount($params) {
        if ($this->getSettings('status')) {
            $session = wa()->getStorage();
            $backend = (wa()->getEnv() == 'backend');
            if ($backend) {
                $contact_id = $session->read('bonus_customer_id');
            } else {
                $contact_id = wa()->getUser()->getId();
            }
            $total_bonus = shopBonuses::getContactBonus($contact_id);
            if ($total_bonus) {
                $total_bonus = shop_currency($total_bonus, null, null, false);
                $bonus_discont = intval($this->getSettings('bonus_discont'));
                $total = $params['order']['total'];
                if ($bonus_discont > 0 && $bonus_discont < 100) {
                    $bonus_discont_val = $total * $bonus_discont / 100;
                } else {
                    $bonus_discont_val = $total;
                }
                $bonuses_use = $session->read('bonuses_use');
                $bonuses_use = shop_currency($bonuses_use, null, null, false);
                $bonuses_use = min($total_bonus, $bonus_discont_val, $bonuses_use);
                $def_currency = wa('shop')->getConfig()->getCurrency(true);
                $cur_currency = wa('shop')->getConfig()->getCurrency(false);
                $session->write('bonuses_use', shop_currency($bonuses_use, $cur_currency, $def_currency, false));

                $discount = array(
                    'discount' => $bonuses_use,
                    'description' => 'Скидка при использование бонусов',
                );
                return $discount;
            } else {
                $session->remove('bonuses_use');
                $session->remove('bonus_customer_id');
            }
        }
    }

    public function orderActionCreate($params) {
        if ($this->getSettings('status')) {
            $session = wa()->getStorage();
            $bonuses_use = $session->read('bonuses_use');
            if ($bonuses_use) {
                if (wa()->getEnv() == 'backend') {
                    $contact_id = $session->read('bonus_customer_id');
                } else {
                    $contact_id = wa()->getUser()->getId();
                }
                $total_bonus = shopBonuses::getContactBonus($contact_id);
                if ($total_bonus) {
                    $bonuses_use = min($total_bonus, $bonuses_use);
                    $comment = '';
                    shopBonuses::applyBonus($params['contact_id'], -$bonuses_use, $params['order_id'], $comment, shopAffiliateTransactionModel::TYPE_ORDER_CANCEL);
                }
                $session->remove('bonuses_use');
                $session->remove('bonus_customer_id');
            }
        }
    }

}
