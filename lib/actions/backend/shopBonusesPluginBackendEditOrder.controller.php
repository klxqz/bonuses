<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendEditOrderController extends waJsonController {

    public function execute() {
        $this->response = array(
            'total_bonus' => 0,
            'bonus' => 0,
        );
        $plugin = wa()->getPlugin('bonuses');
        $def_currency = wa('shop')->getConfig()->getCurrency(true);
        $currency = waRequest::post('currency');
        $customer = waRequest::post('customer', array());
        $discount = waRequest::post('discount', 0);

        if (!empty($customer['id'])) {
            $total_bonus = $plugin->getUnburnedBonus($customer['id']);
            $this->response['total_bonus'] = shop_currency($total_bonus, $def_currency, $currency);
        }


        $items = waRequest::post('items', array());
        $bonus_service = $plugin->getSettings('bonus_service');
        $bonus = 0;

        foreach ($items as $item) {
            if (!empty($item['product_id']) && !empty($item['sku_id'])) {
                $bonus += $plugin->getProductBonus($item['product_id'], $item['sku_id']) * $item['quantity'];
                if ($bonus_service && !empty($item['services'])) {
                    foreach ($item['services'] as $service) {
                        $bonus += $plugin->getBonus($service['price']);
                    }
                }
            }
        }

        $bonus -= $plugin->getBonus($discount);
        if ($bonus < 0) {
            $bonus = 0;
        }


        $this->response['bonus'] = round(shop_currency($bonus, $def_currency, $currency, false), 2);
    }

}
