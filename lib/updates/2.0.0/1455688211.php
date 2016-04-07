<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
try {
    $files = array(
        'plugins/bonuses/lib/actions/backend/shopBonusesPluginBackendDelete.controller.php',
        'plugins/bonuses/lib/models/shopBonusesPlugin.model.php',
        'plugins/bonuses/templates/layouts/Backend.html',
        'plugins/bonuses/lib/actions/backend/shopBonusesPluginBackendNewBonus.controller.php',
        'plugins/bonuses/templates/actions/backend/Backend.html',
        'plugins/bonuses/templates/actions/backend/BackendAddbonuses.html',
        'plugins/bonuses/js/lazy.load.js',
        'plugins/bonuses/lib/actions/backend/shopBonusesPluginBackendContactForm.controller.php',
        'plugins/bonuses/lib/actions/backend/shopBonusesPluginBackendSave.controller.php',
        'plugins/bonuses/lib/actions/backend/shopBonusesPluginBackendAddbonuses.action.php',
        'plugins/bonuses/lib/actions/backend/shopBonusesPluginBackend.action.php',
        'plugins/bonuses/lib/config/db.php',
        'plugins/bonuses/lib/actions/backend/shopBonusesPluginBackendDeleteSelected.controller.php',
        'plugins/bonuses/js/bonuses.js',
        'plugins/bonuses/lib/layouts/shopBonusesPluginBackend.layout.php',
        'plugins/bonuses/lib/actions/backend/shopBonusesPluginBackendBonuses.action.php',
        'plugins/bonuses/templates/actions/backend/BackendBonuses.html',
    );

    foreach ($files as $file) {
        waFiles::delete(wa()->getAppPath($file, 'shop'), true);
    }
} catch (Exception $e) {
    
}


$model = new waModel();

$plugin_id = array('shop', 'bonuses');
$app_settings_model = new waAppSettingsModel();
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


try {
    $sql = 'SELECT `bonuses_notified` FROM `shop_affiliate_transaction` WHERE 0';
    $model->query($sql);
} catch (waDbException $ex) {
    $sql = "ALTER TABLE `shop_affiliate_transaction` ADD `bonuses_notified` TINYINT( 1 ) NOT NULL DEFAULT '0'";
    $model->query($sql);
}

try {
    $burn_days = $app_settings_model->get($plugin_id, 'burn_days');
    $sql = 'SELECT * FROM `shop_bonuses` WHERE `date` > DATE_ADD( NOW() , INTERVAL -' . $model->escape($burn_days) . ' DAY )';
    $bonuses = $model->query($sql)->fetchAll();
    foreach ($bonuses as $bonus) {
        shopBonuses::applyBonus($bonus['contact_id'], $bonus['bonus'], null, 'Бонусы за покупку');
    }

    $sql = "DROP TABLE IF EXISTS `shop_bonuses`";
    $model->query($sql);
} catch (waDbException $ex) {
    
}

try {
    $sql = "ALTER TABLE `shop_product` CHANGE `bonus_val` `bonuses_val` DECIMAL( 15, 4 ) NOT NULL";
    $model->query($sql);
} catch (waDbException $ex) {
    
}

try {
    $sql = "ALTER TABLE `shop_product` CHANGE `bonus_type` `bonuses_type` ENUM( 'percent', 'absolute' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
    $model->query($sql);
} catch (waDbException $ex) {
    
}

try {
    $sql = "ALTER TABLE `shop_product` CHANGE `use_bonus` `bonuses_use` TINYINT( 1 ) NOT NULL";
    $model->query($sql);
} catch (waDbException $ex) {
    
}
