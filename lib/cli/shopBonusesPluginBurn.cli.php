<?php

class shopBonusesPluginBurnCli extends waCliController {

    public function execute() {
        shopBonuses::burnBonuses();
    }

}
