<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendSaveController extends waJsonController {

    public function execute() {
        $id = waRequest::post('id');
        $bonus = waRequest::post('bonus');
        $date = waRequest::post('date');
        $bonuses_model = new shopBonusesPluginModel();

        $result = $bonuses_model->getById($id);
        if ($result) {
            $bonuses_model->updateById($id, array('bonus' => $bonus, 'date' => $date));
            $this->response['bonus'] = shop_currency($bonus);
        } else {
            $this->errors = 'Ошибка. Запись не найдена';
        }
    }

}
