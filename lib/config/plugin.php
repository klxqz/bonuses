<?php

return array(
    'name' => 'Бонусы за покупку',
    'description' => 'Начисление бонусов за совершение покупок',
    'img' => 'img/bonuses.png',
    'vendor' => '985310',
    'version' => '1.0.0',
    'rights' => false,
    'frontend' => true,
    'handlers' => array(
        'frontend_product' => 'frontendProduct',
        'frontend_category' => 'frontendCategory',
        'frontend_cart' => 'frontendCart',
        'order_action.complete' => 'orderActionComplete',
        'frontend_my' => 'frontendMy',
        'order_calculate_discount' => 'orderCalculateDiscount',
        'order_action.create' => 'orderActionCreate',
    )
);
