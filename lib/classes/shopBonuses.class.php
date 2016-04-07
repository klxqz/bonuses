<?php

class shopBonuses {

    public static function getContactBonus($contact_id = null) {
        if (!$contact_id) {
            $contact_id = wa()->getUser()->getId();
        }
        $scm = new shopCustomerModel();
        $customer = $scm->getById($contact_id);
        if (!empty($customer['affiliate_bonus'])) {
            return $customer['affiliate_bonus'];
        } else {
            return 0;
        }
    }

    public static function getOrderBonus($order_id) {
        $app_settings_model = new waAppSettingsModel();
        $order_model = new shopOrderModel();
        $order = $order_model->getOrder($order_id);
        $bonus = 0;
        foreach ($order['items'] as $item) {
            if ($item['type'] == 'product') {
                $bonus += self::getProductBonus($item['product_id'], $item['sku_id']) * $item['quantity'];
            }
            if ($app_settings_model->get(shopBonusesPlugin::$plugin_id, 'bonus_service') && $item['type'] == 'service') {
                $bonus += self::getBonus($item['price']) * $item['quantity'];
            }
        }
        if (!empty($order['discount'])) {
            $bonus -= self::getBonus($order['discount']);
        }
        return $bonus;
    }

    public static function getCartBonus() {
        $app_settings_model = new waAppSettingsModel();
        $cart = new shopCart();
        $items = $cart->items(false);
        $bonus = 0;
        foreach ($items as $item) {
            if ($item['type'] == 'product') {
                $bonus += self::getProductBonus($item['product_id'], $item['sku_id']) * $item['quantity'];
            }
            if ($app_settings_model->get(shopBonusesPlugin::$plugin_id, 'bonus_service') && $item['type'] == 'service') {
                $bonus += self::getBonus($item['price']) * $item['quantity'];
            }
        }

        $discount = $cart->discount();
        if ($discount) {
            $bonus -= self::getBonus($discount);
        }

        return $bonus;
    }

    public static function applyBonus($contact_id, $amount, $order_id = null, $comment = null, $type = null) {
        $app_settings_model = new waAppSettingsModel();

        $atm = new shopAffiliateTransactionModel();
        $atm->applyBonus($contact_id, $amount, $order_id, $comment, $type);

        if ($amount > 0 && $order_id && $app_settings_model->get(shopBonusesPlugin::$plugin_id, 'notification_accrual')) {
            $order_model = new shopOrderModel();
            $order = $order_model->getOrder($order_id);
            $general = wa('shop')->getConfig()->getGeneralSettings();

            if (!empty($order['contact']['email']) && !empty($general['email']) && !empty($general['name'])) {
                $email = $order['contact']['email'];
                $notification = $app_settings_model->get(shopBonusesPlugin::$plugin_id, 'notification_accrual_text');
                $notification = sprintf($notification, shop_currency($amount));
                $title = sprintf("Начисление бонусов за заказ %s", shopHelper::encodeOrderId($order_id));
                $message = new waMailMessage($title, $notification);
                $message->setTo($email);
                $message->setFrom($general['email'], $general['name']);
                $message->send();
            }
        }
    }

    public static function getProductBonus($product_id, $sku_id = null, $features = array()) {
        $product_model = new shopProductModel();
        $product = $product_model->getById($product_id);
        $sku_model = new shopProductSkusModel();
        $product_features_model = new shopProductFeaturesModel();

        if (!$sku_id && $features) {
            $sku_id = $product_features_model->getSkuByFeatures($product_id, $features);
        }

        if ($product['bonuses_use']) {
            if ($product['bonuses_type'] == 'absolute') {
                return $product['bonuses_val'];
            } elseif ($product['bonuses_type'] == 'percent') {
                if (!$sku_id) {
                    $event_params = array(
                        'products' => array(
                            $product['id'] => &$product
                        )
                    );
                    wa('shop')->event('frontend_products', $event_params);
                    return self::getBonus($product['price'], $product['bonuses_val']);
                } else {
                    $sku = $sku_model->getById($sku_id);
                    $event_params = array(
                        'products' => array(
                            $product['id'] => &$product
                        ),
                        'skus' => array(
                            $sku['id'] => &$sku
                        )
                    );
                    wa('shop')->event('frontend_products', $event_params);
                    return self::getBonus($sku['price'], $product['bonuses_val']);
                }
            }
        } else {
            if (!$sku_id) {
                $event_params = array(
                    'products' => array(
                        $product['id'] => &$product
                    )
                );
                wa('shop')->event('frontend_products', $event_params);
                return self::getBonus($product['price']);
            } else {
                $sku = $sku_model->getById($sku_id);
                $event_params = array(
                    'products' => array(
                        $product['id'] => &$product
                    ),
                    'skus' => array(
                        $sku['id'] => &$sku
                    )
                );
                return self::getBonus($sku['price']);
            }
        }
    }

    public static function getBonus($price, $percent = null) {
        $app_settings_model = new waAppSettingsModel();
        if (!$percent) {
            $percent = $app_settings_model->get(shopBonusesPlugin::$plugin_id, 'percent');
        }
        $bonus = $price * $percent / 100.0;
        $round_func = $app_settings_model->get(shopBonusesPlugin::$plugin_id, 'round_func');
        switch ($round_func) {
            case 'round':
                $bonus = round($bonus, $app_settings_model->get(shopBonusesPlugin::$plugin_id, 'precision'));
                break;
            case 'ceil':
                $bonus = ceil($bonus);
                break;
            case 'floor':
                $bonus = floor($bonus);
                break;
        }
        return $bonus;
    }

    public static function burnBonuses() {
        $app_settings_model = new waAppSettingsModel();
        $burn_days = $app_settings_model->get(shopBonusesPlugin::$plugin_id, 'burn_days');

        $now = waDateTime::date('Y-m-d H:i:s');
        $burn_date = strtotime($now) - $burn_days * 24 * 3600;

        $atm = new shopAffiliateTransactionModel();
        $bonuses = $atm->where("create_datetime <= '" . date('Y-m-d H:i:s', $burn_date) . "' AND `balance` > 0")->fetchAll();

        foreach ($bonuses as $bonus) {
            $comment = 'Сгорание бонусов';
            self::applyBonus($bonus['contact_id'], -$bonus['amount'], $bonus['order_id'], $comment, shopAffiliateTransactionModel::TYPE_ORDER_CANCEL);
        }

        if ($app_settings_model->get(shopBonusesPlugin::$plugin_id, 'notification_burn')) {
            $notification_burn_days = $app_settings_model->get(shopBonusesPlugin::$plugin_id, 'notification_burn_days');
            $notification_day = $burn_days - $notification_burn_days;
            $bonuses = $atm->where("DATE_ADD(`create_datetime` , INTERVAL " . $atm->escape($notification_day) . " DAY ) <= '" . $now . "' AND `balance` > 0 AND `bonuses_notified` = 0")->fetchAll();
            $general = wa('shop')->getConfig()->getGeneralSettings();
            $order_model = new shopOrderModel();
            foreach ($bonuses as $bonus) {
                $order_id = $bonus['order_id'];
                $order = $order_model->getOrder($order_id);
                if (!empty($order['contact']['email']) && !empty($general['email']) && !empty($general['name'])) {
                    $burn_day = strtotime($bonus['create_datetime']) + $burn_days * 24 * 3600;
                    $last_days = ceil(($burn_day - strtotime($now)) / (24 * 3600));
                    $email = $order['contact']['email'];
                    $notification = $app_settings_model->get(shopBonusesPlugin::$plugin_id, 'notification_burn_text');
                    $notification = sprintf($notification, shop_currency($bonus['amount']), $last_days);
                    $title = sprintf("Сгорание бонусов", shopHelper::encodeOrderId($order_id));
                    $message = new waMailMessage($title, $notification);
                    $message->setTo($email);
                    $message->setFrom($general['email'], $general['name']);
                    $message->send();
                    $atm->updateById($bonus['id'], array('bonuses_notified' => 1));
                }
            }
        }
    }

}
