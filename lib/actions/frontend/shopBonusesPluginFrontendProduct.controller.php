<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginFrontendProductController extends waJsonController {

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        if (!$app_settings_model->get(shopBonusesPlugin::$plugin_id, 'status')) {
            throw new waException(_w('Unknown page'), 404);
        }

        $product_id = waRequest::post('product_id', 0, waRequest::TYPE_INT);
        $sku_id = waRequest::post('sku_id', 0, waRequest::TYPE_INT);
        $features = waRequest::post('features', array(), waRequest::TYPE_ARRAY_INT);

        $bonus = shopBonuses::getProductBonus($product_id, $sku_id, $features);
        $bonus = shop_currency($bonus);
        $tooltip_text = $app_settings_model->get(shopBonusesPlugin::$plugin_id, 'product_tooltip_text');
        $this->response = array(
            'bonus' => $bonus,
            'tooltip_text' => sprintf($tooltip_text, $bonus),
        );
    }

}
