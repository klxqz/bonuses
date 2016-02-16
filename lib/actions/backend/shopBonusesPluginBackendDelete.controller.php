<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendDeleteController extends waJsonController {

    public function execute() {
        $id = waRequest::post('id');
        $bonuses_model = new shopBonusesPluginModel();

        $result = $bonuses_model->getById($id);
        if ($result) {
            $bonuses_model->deleteById($id);
        } else {
            $this->errors = 'Ошибка. Запись не найдена';
        }
    }

}
