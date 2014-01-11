<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPlugin extends shopPlugin {

    protected static $plugin;

    public function __construct($info) {
        parent::__construct($info);
        if (!self::$plugin) {
            self::$plugin = &$this;
        }
    }

    protected static function getThisPlugin() {
        if (self::$plugin) {
            return self::$plugin;
        } else {
            return wa()->getPlugin('bonuses');
        }
    }

    public function backendMenu() {
        if ($this->getSettings('status')) {
            $html = '<li ' . (waRequest::get('plugin') == $this->id ? 'class="selected"' : 'class="no-tab"') . '>
                        <a href="?plugin=bonuses">Бонусы</a>
                    </li>';
            return array('core_li' => $html);
        }
    }

    public function frontendProduct($product) {
        if ($this->getSettings('frontend_product')) {
            $html = self::displayFrontendProduct($product);
            return array('cart' => $html);
        }
    }

    public static function displayFrontendProduct($product) {
        $html = '';
        $plugin = self::getThisPlugin();
        if ($plugin->getSettings('status')) {
            $currency = wa('shop')->getConfig()->getCurrency(false);
            $currency_sign = wa()->getConfig()->getCurrencies($currency);
            $bonus = $plugin->getBonus($product['price']);
            $view = wa()->getView();
            $view->assign('bonus', $bonus);
            $view->assign('currency_sign', $currency_sign[$currency]['sign']);
            $view->assign('percent', $plugin->getSettings('percent'));
            $view->assign('precision', $plugin->getSettings('precision'));
            $view->assign('round_func', $plugin->getSettings('round_func'));
            $template_path = wa()->getDataPath('plugins/bonuses/templates/FrontendProduct.html', false, 'shop', true);
            if (!file_exists($template_path)) {
                $template_path = wa()->getAppPath('plugins/bonuses/templates/FrontendProduct.html', 'shop');
            }
            $html = $view->fetch($template_path);
        }
        waSystem::popActivePlugin();
        return $html;
    }

    public static function getBonusByPrice($price = 0) {
        $bonus = '';
        $plugin = self::getThisPlugin();
        if ($plugin->getSettings('status')) {
            $bonus = $plugin->getBonus($price);
            $currency = wa('shop')->getConfig()->getCurrency(false);
            $bonus = shop_currency($bonus);
        }
        waSystem::popActivePlugin();
        return $bonus;
    }

    public function frontendCategory($category) {
        if ($this->getSettings('status') && $this->getSettings('frontend_category')) {
            $currency = wa('shop')->getConfig()->getCurrency(false);
            $currency_sign = wa()->getConfig()->getCurrencies($currency);
            $view = wa()->getView();
            $view->assign('currency_sign', $currency_sign[$currency]['sign']);
            $view->assign('percent', $this->getSettings('percent'));
            $view->assign('precision', $this->getSettings('precision'));
            $view->assign('round_func', $this->getSettings('round_func'));
            $html = $view->fetch('plugins/bonuses/templates/FrontendCategory.html');
            return $html;
        }
    }

    public function frontendCart() {
        if ($this->getSettings('frontend_cart')) {
            return self::displayFrontendCart();
        }
    }

    public static function displayFrontendCart() {
        $html = '';
        $plugin = self::getThisPlugin();
        if ($plugin->getSettings('status')) {
            $cart = new shopCart();
            $total = $cart->total(true);
            $currency = wa('shop')->getConfig()->getCurrency(false);
            $bonus = $plugin->getBonus($total);
            $cart_bonuses = shop_currency($bonus, $currency, $currency);
            $view = wa()->getView();
            $view->assign('cart_bonuses', $cart_bonuses);
            $template_path = wa()->getDataPath('plugins/bonuses/templates/FrontendCart.html', false, 'shop', true);
            if (!file_exists($template_path)) {
                $template_path = wa()->getAppPath('plugins/bonuses/templates/FrontendCart.html', 'shop');
            }
            $html = $view->fetch($template_path);
            return $html;
        }
        waSystem::popActivePlugin();
        return $html;
    }

    public function frontendMy() {
        if ($this->getSettings('frontend_my')) {
            return self::displayFrontendMy();
        }
    }

    public static function displayFrontendMy() {
        $html = '';
        $plugin = self::getThisPlugin();
        if ($plugin->getSettings('status')) {
            $contact_id = wa()->getUser()->getId();
            $bonus = $plugin->getUnburnedBonus($contact_id);
            $bonus = shop_currency($bonus);
            $view = wa()->getView();
            $view->assign('bonus', $bonus);
            $template_path = wa()->getDataPath('plugins/bonuses/templates/FrontendMy.html', false, 'shop', true);
            if (!file_exists($template_path)) {
                $template_path = wa()->getAppPath('plugins/bonuses/templates/FrontendMy.html', 'shop');
            }
            $html = $view->fetch($template_path);
        }
        waSystem::popActivePlugin();
        return $html;
    }

    public function orderActionComplete($params) {
        if ($this->getSettings('status')) {
            $order_model = new shopOrderModel();
            $order = $order_model->getById($params['order_id']);
            $total = $order['total'] - $order['shipping'];
            $bonus = $this->getBonus($total);
            $def_currency = wa('shop')->getConfig()->getCurrency(true);
            $bonus = shop_currency($bonus, $order['currency'], $def_currency, false);
            $bonus_model = new shopBonusesPluginModel();
            if ($sb = $bonus_model->getByField('contact_id', $order['contact_id'])) {
                $exist_bonus = $this->getUnburnedBonus($order['contact_id']);
                $data = array(
                    'date' => waDateTime::date('Y-m-d H:i:s'),
                    'bonus' => $bonus + $exist_bonus,
                );
                $bonus_model->updateById($sb['id'], $data);
            } else {
                $data = array(
                    'contact_id' => $order['contact_id'],
                    'date' => waDateTime::date('Y-m-d H:i:s'),
                    'bonus' => $bonus,
                );
                $bonus_model->insert($data);
            }
        }
    }

    public function orderActionRefund($params) {
        if ($this->getSettings('status')) {
            $order_model = new shopOrderModel();
            $order = $order_model->getById($params['order_id']);
            $total = $order['total'] - $order['shipping'];
            $bonus = $this->getBonus($total);

            $bonus_model = new shopBonusesPluginModel();
            if ($sb = $bonus_model->getByField('contact_id', $order['contact_id'])) {
                $exist_bonus = $this->getUnburnedBonus($order['contact_id']);
                $data = array(
                    'bonus' => ($exist_bonus - $bonus > 0 ? $exist_bonus - $bonus : 0),
                );
                $bonus_model->updateById($sb['id'], $data);
            }
        }
    }

    public function orderCalculateDiscount($params) {
        if ($this->getSettings('status')) {
            $total = $params['order']['total'];
            $contact_id = wa()->getUser()->getId();
            if ($bonus = $this->getUnburnedBonus($contact_id)) {
                $bonus = shop_currency($bonus, null, null, false);
                $bonus_discont = intval($this->getSettings('bonus_discont'));
                if ($bonus_discont > 0 && $bonus_discont < 100) {
                    $bonus_discont_val = $total * $bonus_discont / 100;
                } else {
                    $bonus_discont_val = $total;
                }
                $use_bonus = min($bonus_discont_val, $bonus);
                $session = wa()->getStorage();

                $def_currency = wa('shop')->getConfig()->getCurrency(true);
                $cur_currency = wa('shop')->getConfig()->getCurrency(false);
                $session->write('use_bonus', shop_currency($use_bonus, $cur_currency, $def_currency, false));
                return $use_bonus;
            }
        }
    }

    public function orderActionCreate() {
        if ($this->getSettings('status')) {
            $session = wa()->getStorage();
            $use_bonus = $session->read('use_bonus');
            if ($use_bonus) {
                $contact_id = wa()->getUser()->getId();
                $bonus_model = new shopBonusesPluginModel();
                $sb = $bonus_model->getByField('contact_id', $contact_id);
                $bonus_model->updateById($sb['id'], array('bonus' => $sb['bonus'] - $use_bonus));
            }
        }
    }

    public function getUnburnedBonus($contact_id) {
        $bonus_model = new shopBonusesPluginModel();
        $sb = $bonus_model->getByField('contact_id', $contact_id);
        if ($sb) {
            if ($this->isBurn($sb)) {
                $bonus_model->updateById($sb['id'], array('bonus' => 0));
                return 0;
            } else {
                return $sb['bonus'];
            }
        } else {
            return false;
        }
    }

    public function getBonus($price) {
        if ($this->getSettings('status')) {
            $bonus = $price * $this->getSettings('percent') / 100.0;
            $round_func = $this->getSettings('round_func');
            switch ($round_func) {
                case 'round':
                    $bonus = round($bonus, $this->getSettings('precision'));
                    break;
                case 'ceil':
                    $bonus = ceil($bonus);
                    break;
                case 'floor':
                    $bonus = floor($bonus);
                    break;
            }

            return $bonus;
        } else {
            return false;
        }
    }

    public function isBurn($sb) {
        $now = waDateTime::date('Y-m-d H:i:s');
        $sec = strtotime($now) - strtotime($sb['date']);
        $days = $sec / (3600 * 24);
        if ($this->getSettings('burn_bonus') && $days > intval($this->getSettings('burn_days'))) {
            return true;
        } else {
            return false;
        }
    }

}
