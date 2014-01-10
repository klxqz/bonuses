<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendAddbonusesAction extends waViewAction {

    public function execute() {
        
        $bonuses_model = new shopBonusesPluginModel();
        $bonuses = $bonuses_model->getBonusesList();
        $this->view->assign('bonuses', $bonuses);
    }

}
