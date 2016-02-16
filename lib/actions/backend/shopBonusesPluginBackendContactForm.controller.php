<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendContactFormController extends waJsonController {

    public function execute() {
        $id = (int) waRequest::post('id');
        if ($id) {
            $bonuses_model = new shopBonusesPluginModel();
            $form = shopHelper::getCustomerForm($id);
            $this->response['html_form'] = $form->html();
            $sb = $bonuses_model->getByField('contact_id', $id);
            if ($sb) {
                $this->response['bonus'] = round(shop_currency($sb['bonus'], null, null, false), 2);
                $this->response['date'] = $sb['date'];
            } else {
                $this->response['bonus'] = '';
                $this->response['date'] = waDateTime::date('Y-m-d H:i:s');
            }
        }
    }

}
