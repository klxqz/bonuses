<?php

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
