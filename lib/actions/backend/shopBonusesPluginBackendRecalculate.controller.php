<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendRecalculateController extends waJsonController {

    public function execute() {
        $use_bonus = waRequest::post('use_bonus', 0);
        $customer_id = waRequest::post('customer_id', 0, waRequest::TYPE_INT);

        if (empty($customer_id)) {
            $this->errors = 'Не выбран покупатель';
        } else {
            $plugin = shopBonusesPlugin::getThisPlugin();
            $total_bonus = $plugin->getUnburnedBonus($customer_id);
            $use_bonus = floatval($use_bonus);
            $use_bonus = min($use_bonus, $total_bonus);

            $def_currency = wa('shop')->getConfig()->getCurrency(true);
            $cur_currency = wa('shop')->getConfig()->getCurrency(false);

            $session = wa()->getStorage();
            $session->write('bonus_customer_id', $customer_id);
            $session->write('use_bonus', round(shop_currency($use_bonus, $cur_currency, $def_currency, false), 2));
            $this->response['use_bonus'] = round($use_bonus, 2);
        }
    }

}
