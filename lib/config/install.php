<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
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
$app_settings_model->set($plugin_id, 'frontend_product', '1');
$app_settings_model->set($plugin_id, 'frontend_category', '1');
$app_settings_model->set($plugin_id, 'frontend_cart', '1');
$app_settings_model->set($plugin_id, 'frontend_my', '1');


$model = new waModel();
try {
    $sql = 'SELECT `bonus_val` FROM `shop_product` WHERE 0';
    $model->query($sql);
} catch (waDbException $ex) {
    $sql = 'ALTER TABLE  `shop_product` ADD  `bonus_val` decimal(15,4) NOT NULL AFTER  `id`';
    $model->query($sql);
}

try {
    $sql = 'SELECT `bonus_type` FROM `shop_product` WHERE 0';
    $model->query($sql);
} catch (waDbException $ex) {
    $sql = "ALTER TABLE  `shop_product` ADD  `bonus_type` ENUM( 'percent', 'absolute' ) NOT NULL AFTER  `id`";
    $model->query($sql);
}
