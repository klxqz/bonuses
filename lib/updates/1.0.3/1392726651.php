<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
$plugin_id = array('shop', 'bonuses');
$app_settings_model = new waAppSettingsModel();
$app_settings_model->set($plugin_id, 'order_status', 'complete');
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

