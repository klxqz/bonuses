<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendNewBonusController extends waJsonController {

    public function execute() {
        $contact_id = (int) waRequest::post('contact_id');
        $bonus = waRequest::post('bonus');
        $date = waRequest::post('date');
        $bonuses_model = new shopBonusesPluginModel();
        if (!$contact_id) {
            $this->errors = 'Ошибка. Пользователь не найден.';
        }

        if ($sb = $bonuses_model->getByField('contact_id', $contact_id)) {
            $data = array(
                'date' => $date,
                'bonus' => $bonus,
            );
            $bonuses_model->updateById($sb['id'], $data);
        } else {
            $data = array(
                'contact_id' => $contact_id,
                'date' => $date,
                'bonus' => $bonus,
            );
            $bonuses_model->insert($data);
        }
    }

}
