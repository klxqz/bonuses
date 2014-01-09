<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginBackendAction extends waViewAction {

    public function execute() {
        $this->setLayout(new shopBonusesPluginBackendLayout());
    }

}
