<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginFrontendRecalculateController extends waJsonController {

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        if (!$app_settings_model->get(shopBonusesPlugin::$plugin_id, 'status')) {
            throw new waException(_w('Unknown page'), 404);
        }

        $def_currency = wa('shop')->getConfig()->getCurrency(true);
        $cur_currency = wa('shop')->getConfig()->getCurrency(false);
        $bonuses_use = waRequest::post('bonuses_use', 0);
        $bonuses_use = shop_currency($bonuses_use, $cur_currency, $def_currency, false);
        wa()->getStorage()->write('bonuses_use', $bonuses_use);
    }

}
