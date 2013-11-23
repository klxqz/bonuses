<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginFrontendBonusesController extends waJsonController {

    public function execute() {
        $plugin = wa()->getPlugin('bonuses');
        $cart = new shopCart();
        $total = $cart->total(false);
        $currency = wa('shop')->getConfig()->getCurrency(false);
        $bonus = $plugin->getBonus($total);
        $cart_text = $plugin->getSettings('cart_text');
        $cart_bonuses = sprintf($cart_text, shop_currency($bonus, $currency, $currency));
        $this->response['cart_bonuses'] = $cart_bonuses;
    }

}
