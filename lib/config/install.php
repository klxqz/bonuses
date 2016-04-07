<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
$plugin_id = array('shop', 'bonuses');
$app_settings_model = new waAppSettingsModel();
$app_settings_model->set($plugin_id, 'status', '1');
$app_settings_model->set($plugin_id, 'percent', '2');
$app_settings_model->set($plugin_id, 'round_func', 'round');
$app_settings_model->set($plugin_id, 'precision', '2');
$app_settings_model->set($plugin_id, 'burn_bonus', '1');
$app_settings_model->set($plugin_id, 'burn_days', '30');
$app_settings_model->set($plugin_id, 'bonus_discont', '50');
$app_settings_model->set($plugin_id, 'order_status', 'complete');
$app_settings_model->set($plugin_id, 'bonus_service', '0');

$app_settings_model->set($plugin_id, 'product_form_selector', '#cart-form');
$app_settings_model->set($plugin_id, 'product_tooltip', '1');
$app_settings_model->set($plugin_id, 'product_tooltip_title', 'Бонусы за покупку');
$app_settings_model->set($plugin_id, 'product_tooltip_text', 'За покупку данного товара Вам будет начислено %s бонусов');
$app_settings_model->set($plugin_id, 'frontend_product_output', 'cart');
$app_settings_model->set($plugin_id, 'cart_tooltip', '1');
$app_settings_model->set($plugin_id, 'cart_tooltip_title', 'Бонусы за покупку');
$app_settings_model->set($plugin_id, 'cart_tooltip_text', 'За оформление данного заказа Вам будет начислено %s бонусов');
$app_settings_model->set($plugin_id, 'frontend_cart', '1');
$app_settings_model->set($plugin_id, 'frontend_my_template', 'my.affiliate.html');
$app_settings_model->set($plugin_id, 'frontend_my_nav', '1');
$app_settings_model->set($plugin_id, 'notification_accrual', '1');
$app_settings_model->set($plugin_id, 'notification_accrual_text', 'Вам начислено %s бонусов');
$app_settings_model->set($plugin_id, 'notification_burn', '1');
$app_settings_model->set($plugin_id, 'notification_burn_days', '5');
$app_settings_model->set($plugin_id, 'notification_burn_text', 'Успейте потратить %s бонусов. До сгорания осталось %d дней.');


$model = new waModel();
try {
    $sql = 'SELECT `bonuses_val` FROM `shop_product` WHERE 0';
    $model->query($sql);
} catch (waDbException $ex) {
    $sql = 'ALTER TABLE  `shop_product` ADD  `bonuses_val` decimal(15,4) NOT NULL AFTER  `id`';
    $model->query($sql);
}

try {
    $sql = 'SELECT `bonuses_type` FROM `shop_product` WHERE 0';
    $model->query($sql);
} catch (waDbException $ex) {
    $sql = "ALTER TABLE  `shop_product` ADD  `bonuses_type` ENUM( 'percent', 'absolute' ) NOT NULL AFTER  `id`";
    $model->query($sql);
}

try {
    $sql = 'SELECT `bonuses_use` FROM `shop_product` WHERE 0';
    $model->query($sql);
} catch (waDbException $ex) {
    $sql = "ALTER TABLE `shop_product` ADD `bonuses_use` TINYINT( 1 ) NOT NULL AFTER `id`";
    $model->query($sql);
}
