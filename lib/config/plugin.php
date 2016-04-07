<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
return array(
    'name' => 'Бонусы за покупку',
    'description' => 'Начисление бонусов за совершение покупок',
    'img' => 'img/bonuses.png',
    'vendor' => '985310',
    'version' => '2.0.0',
    'rights' => false,
    'frontend' => true,
    'shop_settings' => true,
    'handlers' => array(
        'frontend_product' => 'frontendProduct',
        'frontend_cart' => 'frontendCart',
        'order_action.pay' => 'orderActionPay',
        'order_action.complete' => 'orderActionComplete',
        'order_calculate_discount' => 'orderCalculateDiscount',
        'order_action.create' => 'orderActionCreate',
        'order_action.refund' => 'orderActionRefund',
        'backend_product_edit' => 'backendProductEdit',
        'backend_order_edit' => 'backendOrderEdit',
        'frontend_my_nav' => 'frontendMyNav',
    )
);
