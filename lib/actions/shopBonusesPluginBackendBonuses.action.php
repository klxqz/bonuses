<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendBonusesAction extends waViewAction {

    public function execute() {

        $bonuses_model = new shopBonusesPluginModel();
        $bonuses = $bonuses_model->getBonusesList();

        $plugin = wa()->getPlugin('bonuses');
        foreach ($bonuses as &$bonus) {
            $bonus['is_burn'] = $plugin->isBurn($bonus) ? 1 : 0;
        }
        $this->view->assign('bonuses', $bonuses);
        waSystem::popActivePlugin();
    }

}
