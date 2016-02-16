<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginFrontendCartController extends waJsonController {

    public function execute() {
        $plugin = wa()->getPlugin('bonuses');
        $cart = new shopCart();
        $items = $cart->items(false);
        $bonus_service = $plugin->getSettings('bonus_service');
        $bonus = 0;
        foreach ($items as $item) {
            if (!empty($item['product'])) {
                $bonus += $plugin->getProductBonus($item['product_id'], $item['sku_id']) * $item['quantity'];
            }
            if($bonus_service && !empty($item['service'])) {
                $bonus += $plugin->getBonus($item['service']['price']);
            }
        }
        $cart_bonuses = shop_currency_html($bonus);
        $this->response['cart_bonuses'] = $cart_bonuses;
    }

}
