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
