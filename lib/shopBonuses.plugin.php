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

    public function backendProductEdit($product) {
        $html = '<div class="field">
                    <div class="name">Использовать индивидуальные настройки бонусов для товара</div>
                    <div class="value no-shift">
                        <select name="product[use_bonus]">
                            <option ' . ($product->use_bonus == '1' ? 'selected="selected"' : '') . ' value="1">Да</option>
                            <option ' . ($product->use_bonus == '0' ? 'selected="selected"' : '') . ' value="0">Нет</option>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <div class="name">Размер бонусов при покупке данного товара</div>
                    <div class="value no-shift">
                        <input type="text" name="product[bonus_val]" value="' . $product->bonus_val . '" class="bold numerical" style="width:100px;">
                    </div>
                </div>
                <div class="field">
                    <div class="name">Тип бонусов</div>
                    <div class="value no-shift">
                        <select name="product[bonus_type]">
                            <option ' . ($product->bonus_type == 'percent' ? 'selected="selected"' : '') . ' value="percent">Процент</option>
                            <option ' . ($product->bonus_type == 'absolute' ? 'selected="selected"' : '') . ' value="absolute">Абсолют</option>
                        </select>
                    </div>
                </div>';
        return array('basics' => $html);
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

    public function getProductBonus($product_id, $sku_id = null) {
        $product_model = new shopProductModel();
        $product = $product_model->getById($product_id);
        $sku_model = new shopProductSkusModel();

        if ($product['use_bonus']) {
            if ($product['bonus_type'] == 'absolute') {
                return $product['bonus_val'];
            } elseif ($product['bonus_type'] == 'percent') {
                if (!$sku_id) {
                    return $this->getBonus($product['price'], $product['bonus_val']);
                } else {
                    $sku = $sku_model->getById($sku_id);
                    return $this->getBonus($sku['primary_price'], $product['bonus_val']);
                }
            }
        } else {
            if (!$sku_id) {
                return $this->getBonus($product['price']);
            } else {
                $sku = $sku_model->getById($sku_id);
                return $this->getBonus($sku['primary_price']);
            }
        }
    }

    public static function getBonusByProduct($product) {
        $bonus = 0;
        $plugin = self::getThisPlugin();
        if ($product['use_bonus']) {
            if ($product['bonus_type'] == 'absolute') {
                $bonus = $product['bonus_val'];
            } elseif ($product['bonus_type'] == 'percent') {
                $bonus = $plugin->getBonus($product['price'], $product['bonus_val']);
            }
        } else {
            $bonus = $plugin->getBonus($product['price']);
        }
        waSystem::popActivePlugin();
        return $bonus;
    }

    public static function displayFrontendProduct($product) {
        $html = '';
        $plugin = self::getThisPlugin();
        if ($plugin->getSettings('status')) {
            $bonus = $plugin->getProductBonus($product['id']);
            $view = wa()->getView();
            $view->assign('bonus', shop_currency_html($bonus));
            $template_path = wa()->getDataPath('plugins/bonuses/templates/FrontendProduct.html', false, 'shop', true);
            if (!file_exists($template_path)) {
                $template_path = wa()->getAppPath('plugins/bonuses/templates/FrontendProduct.html', 'shop');
            }
            $html = $view->fetch($template_path);
        }
        waSystem::popActivePlugin();
        return $html;
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
            $def_currency = wa('shop')->getConfig()->getCurrency(true);
            $cur_currency = wa('shop')->getConfig()->getCurrency(false);
            $session = wa()->getStorage();
            $use_bonus = $session->read('use_bonus');
            $contact_id = wa()->getUser()->getId();
            $total_bonus = $plugin->getUnburnedBonus($contact_id);

            
            $cart = new shopCart();
            $total_order = shop_currency($cart->total(false), $cur_currency, $def_currency, false);
            $bonus_discont = intval($plugin->getSettings('bonus_discont'));
            if ($bonus_discont > 0 && $bonus_discont < 100) {
                $bonus_discont_val = $total_order * $bonus_discont / 100;
            } else {
                $bonus_discont_val = $total_order;
            }
            $use_bonus = min($bonus_discont_val, $total_bonus, $use_bonus);
            $use_bonus = round(shop_currency($use_bonus, null, null, false), 2);
            
            $total_order_bonus = 0;
            $items = $cart->items(false);
            foreach ($items as $item) {
                $total_order_bonus += $plugin->getProductBonus($item['product_id'], $item['sku_id']) * $item['quantity'];
            }
            $discount = shop_currency($cart->discount(), $cur_currency, $def_currency, false);
            $total_order_bonus -= $plugin->getBonus($discount);
            
            $view = wa()->getView();
            $view->assign('total_bonus', shop_currency_html($total_bonus));
            $view->assign('use_bonus', $use_bonus);
            $view->assign('cart_bonuses', shop_currency_html($total_order_bonus));
            $template_path = wa()->getDataPath('plugins/bonuses/templates/FrontendCart.html', false, 'shop', true);
            if (!file_exists($template_path)) {
                $template_path = wa()->getAppPath('plugins/bonuses/templates/FrontendCart.html', 'shop');
            }
            $html = $view->fetch($template_path);
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

    protected function addBonusesOrder($order_id) {
        $def_currency = wa('shop')->getConfig()->getCurrency(true);
        $order_model = new shopOrderModel();
        $order = $order_model->getOrder($order_id);
        $bonus = 0;
        foreach ($order['items'] as $item) {
            $bonus += $this->getProductBonus($item['product_id'], $item['sku_id']) * $item['quantity'];
        }
        $discount = shop_currency($order['discount'], $order['currency'], $def_currency, false);
        $bonus -= $this->getBonus($discount);
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

    public function orderActionPay($params) {
        if ($this->getSettings('status') && $this->getSettings('order_status') == 'pay') {
            $this->addBonusesOrder($params['order_id']);
        }
    }

    public function orderActionComplete($params) {
        if ($this->getSettings('status') && $this->getSettings('order_status') == 'complete') {
            $this->addBonusesOrder($params['order_id']);
        }
    }

    public function orderActionRefund($params) {
        if ($this->getSettings('status')) {
            $def_currency = wa('shop')->getConfig()->getCurrency(true);
            $order_model = new shopOrderModel();
            $order = $order_model->getOrder($params['order_id']);
            $bonus = 0;
            foreach ($order['items'] as $item) {
                $bonus += $this->getProductBonus($item['product_id'], $item['sku_id']) * $item['quantity'];
            }
            $discount = shop_currency($order['discount'], $order['currency'], $def_currency, false);
            $bonus -= $this->getBonus($discount);

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
            $contact_id = wa()->getUser()->getId();
            $total_bonus = $this->getUnburnedBonus($contact_id);
            if ($total_bonus) {
                $total_bonus = shop_currency($total_bonus, null, null, false);
                $bonus_discont = intval($this->getSettings('bonus_discont'));
                $total = $params['order']['total'];
                if ($bonus_discont > 0 && $bonus_discont < 100) {
                    $bonus_discont_val = $total * $bonus_discont / 100;
                } else {
                    $bonus_discont_val = $total;
                }
                $session = wa()->getStorage();
                $use_bonus = $session->read('use_bonus');
                $use_bonus = shop_currency($use_bonus, null, null, false);
                $use_bonus = min($total_bonus, $bonus_discont_val, $use_bonus);
                $def_currency = wa('shop')->getConfig()->getCurrency(true);
                $cur_currency = wa('shop')->getConfig()->getCurrency(false);
                $session->write('use_bonus', shop_currency($use_bonus, $cur_currency, $def_currency, false));
                return $use_bonus;
            } else {
                $session->remove('use_bonus');
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

    public function getBonus($price, $percent = null) {
        if ($this->getSettings('status')) {

            if (!$percent) {
                $percent = $this->getSettings('percent');
            }

            $bonus = $price * $percent / 100.0;

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
