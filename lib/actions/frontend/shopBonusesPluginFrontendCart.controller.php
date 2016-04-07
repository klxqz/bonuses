<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginFrontendCartController extends waJsonController {

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        if (!$app_settings_model->get(shopBonusesPlugin::$plugin_id, 'status')) {
            throw new waException(_w('Unknown page'), 404);
        }
        $bonus = shop_currency_html(shopBonuses::getCartBonus());
        $tooltip_text = $app_settings_model->get(shopBonusesPlugin::$plugin_id, 'cart_tooltip_text');
        $this->response = array(
            'bonus' => $bonus,
            'tooltip_text' => sprintf($tooltip_text, $bonus),
        );
    }

}
