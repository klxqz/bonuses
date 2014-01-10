<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendDeleteSelectedController extends waJsonController {

    public function execute() {
        $bonuses_ids = (array)waRequest::post('bonuses_ids');
        $bonuses_model = new shopBonusesPluginModel();
        $bonuses_model->deleteIds($bonuses_ids);
    }

}
