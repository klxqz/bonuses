<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendRecalculateController extends waJsonController {

    public function execute() {
        $bonuses_use = waRequest::post('bonuses_use', 0);
        $customer_id = waRequest::post('customer_id', 0, waRequest::TYPE_INT);

        if (empty($customer_id)) {
            $this->errors = 'Не выбран покупатель';
        } else {
            $plugin = shopBonusesPlugin::getThisPlugin();
            $total_bonus = $plugin->getUnburnedBonus($customer_id);
            $bonuses_use = floatval($bonuses_use);
            $bonuses_use = min($bonuses_use, $total_bonus);

            $def_currency = wa('shop')->getConfig()->getCurrency(true);
            $cur_currency = wa('shop')->getConfig()->getCurrency(false);

            $session = wa()->getStorage();
            $session->write('bonus_customer_id', $customer_id);
            $session->write('bonuses_use', round(shop_currency($bonuses_use, $cur_currency, $def_currency, false), 2));
            $this->response['bonuses_use'] = round($bonuses_use, 2);
        }
    }

}
