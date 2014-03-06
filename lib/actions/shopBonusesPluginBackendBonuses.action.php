<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendBonusesAction extends waViewAction {

    public function execute() {

        $bonuses_model = new shopBonusesPluginModel();
        $offset = waRequest::get('offset', 0, waRequest::TYPE_INT);
        $total_count = waRequest::get('total_count', null, waRequest::TYPE_INT);
        $lazy = waRequest::get('lazy', false, waRequest::TYPE_INT);
        $limit = 30;
        $bonuses = $bonuses_model->getBonusesList(array(
            'offset' => $offset,
            'limit' => $limit,
        ));

        $plugin = wa()->getPlugin('bonuses');
        foreach ($bonuses as &$bonus) {
            $bonus['is_burn'] = $plugin->isBurn($bonus) ? 1 : 0;
        }
        $this->view->assign(array(
            'bonuses' => $bonuses,
            'offset' => $offset,
            'limit' => $limit,
            'count' => count($bonuses),
            'total_count' => $total_count ? $total_count : $bonuses_model->count(),
            'lazy' => $lazy,
        ));
        waSystem::popActivePlugin();
    }

}
