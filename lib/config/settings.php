<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
return array(
    'status' => array(
        'title' => 'Статус',
        'description' => '',
        'value' => '1',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            '0' => 'Выключен',
            '1' => 'Включен',
        )
    ),
    'percent' => array(
        'title' => 'Процент(%)',
        'description' => 'Количество начисляемых бонусов ровняется проценту от товара',
        'value' => '2',
        'control_type' => waHtmlControl::INPUT
    ),
    'round_func' => array(
        'title' => 'Функция округления',
        'description' => 'round - округление с указанной точностью; ceil - округление в большую сторону; floor - округление в меньшую сторону',
        'value' => 'round',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            'round' => 'round',
            'ceil' => 'ceil',
            'floor' => 'floor',
        )
    ),
    'precision' => array(
        'title' => 'Округление',
        'description' => 'Количество занаков после запятой при округление',
        'value' => '2',
        'control_type' => waHtmlControl::INPUT
    ),
    'frontend_product' => array(
        'title' => 'Стандартный вывод бонусов на странице товара',
        'description' => 'Вы можете отключить стандартный вывод',
        'value' => '1',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            '0' => 'Выключен',
            '1' => 'Включен',
        )
    ),
    'frontend_category' => array(
        'title' => 'Стандартный вывод бонусов на странице категории',
        'description' => 'Вы можете отключить стандартный вывод',
        'value' => '1',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            '0' => 'Выключен',
            '1' => 'Включен',
        )
    ),
    'cart_text' => array(
        'title' => 'Текс в корзине',
        'description' => 'Текс отображаемый в корзине. Вместо %s -подставляется число бонусов.<br/>Возможно использование HTML',
        'value' => 'За эту покупку вам будет начислено бонусов:  %s',
        'control_type' => waHtmlControl::TEXTAREA,
    ),
    'frontend_cart' => array(
        'title' => 'Стандартный вывод бонусов в корзине',
        'description' => 'Вы можете отключить стандартный вывод',
        'value' => '1',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            '0' => 'Выключен',
            '1' => 'Включен',
        )
    ),
    'my_text' => array(
        'title' => 'Текс в личном кабинете',
        'description' => 'Текс отображаемый в личном кабинете. Вместо %s -подставляется число бонусов',
        'value' => 'У вас бонусов:  %s',
        'control_type' => waHtmlControl::INPUT,
    ),
    'frontend_my' => array(
        'title' => 'Стандартный вывод бонусов в личном кабинете',
        'description' => 'Вы можете отключить стандартный вывод',
        'value' => '1',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            '0' => 'Выключен',
            '1' => 'Включен',
        )
    ),
    'burn_bonus' => array(
        'title' => 'Сгорание бонусов',
        'description' => 'Накопленные бонусы сгорают после N дней, если в течение этих дней покупатель не успел их потратить',
        'value' => '1',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            '0' => 'Выключен',
            '1' => 'Включен',
        )
    ),
    'burn_days' => array(
        'title' => 'Количество дней',
        'description' => 'Количество дней, в течение которых можно воспользоваться бонусами, после - бонусы сгорают',
        'value' => '30',
        'control_type' => waHtmlControl::INPUT,
    ),
    'bonus_discont' => array(
        'title' => 'Бонусная скидка',
        'description' => 'Процент от заказа, который покупатель может оплатить бонусами. 0 - покупатель может оплатить весь заказ бонусами',
        'value' => '50',
        'control_type' => waHtmlControl::INPUT,
    ),
);
