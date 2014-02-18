<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginFrontendProductController extends waJsonController {

    public function execute() {
        $product_id = waRequest::post('product_id');
        $sku_id = waRequest::post('sku_id');
        $plugin = wa()->getPlugin('bonuses');
        $bonus = $plugin->getProductBonus($product_id, $sku_id);
        $this->response['bonus'] = shop_currency_html($bonus);
    }

}
