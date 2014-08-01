<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginFrontendCartController extends waJsonController {

    public function execute() {
        $plugin = wa()->getPlugin('bonuses');
        $cart = new shopCart();
        $items = $cart->items(false);
        $bonus = 0;
        foreach ($items as $item) {
            if (!empty($item['product'])) {
                $bonus += $plugin->getProductBonus($item['product_id'], $item['sku_id']) * $item['quantity'];
            }
        }
        $cart_bonuses = shop_currency_html($bonus);
        $this->response['cart_bonuses'] = $cart_bonuses;
    }

}
