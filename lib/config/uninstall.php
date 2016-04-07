<?php

$model = new waModel();

try {
    $model->query("SELECT `bonuses_val` FROM `shop_product` WHERE 0");
    $model->exec("ALTER TABLE `shop_product` DROP `bonuses_val`");
} catch (waDbException $e) {
    
}

try {
    $model->query("SELECT `bonuses_type` FROM `shop_product` WHERE 0");
    $model->exec("ALTER TABLE `shop_product` DROP `bonuses_type`");
} catch (waDbException $e) {
    
}

try {
    $model->query("SELECT `bonuses_use` FROM `shop_product` WHERE 0");
    $model->exec("ALTER TABLE `shop_product` DROP `bonuses_use`");
} catch (waDbException $e) {
    
}