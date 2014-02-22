<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginFrontendRecalculateController extends waJsonController {

    public function execute() {
        $def_currency = wa('shop')->getConfig()->getCurrency(true);
        $cur_currency = wa('shop')->getConfig()->getCurrency(false);

        $use_bonus = waRequest::post('use_bonus', 0);

        $session = wa()->getStorage();
        $session->write('use_bonus', shop_currency($use_bonus, $cur_currency, $def_currency, false));
    }

}
