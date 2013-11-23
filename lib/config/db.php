<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
return array(
    'shop_bonuses' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'contact_id' => array('int', 11, 'null' => 0),
        'date' => array('datetime', 'null' => 0),
        'bonus' => array('decimal', '15,4', 'null' => 0),
        ':keys' => array(
            'PRIMARY' => array('id'),
            'contact_id' => 'contact_id',
        ),
    ),
);
