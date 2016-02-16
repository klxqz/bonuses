<?php

try {
    $files = array(
        'plugins/bonuses/lib/actions/shopBonusesPluginFrontendCart.controller.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginFrontendRecalculate.controller.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginBackendDelete.controller.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginBackendAddbonuses.action.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginFrontendProduct.controller.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginBackendDeleteSelected.controller.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginBackendSave.controller.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginBackendBonuses.action.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginBackendNewBonus.controller.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginBackend.action.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginBackendSaveSettings.controller.php',
        'plugins/bonuses/lib/actions/shopBonusesPluginBackendContactForm.controller.php',
    );

    foreach ($files as $file) {
        waFiles::delete(wa()->getAppPath($file, 'shop'), true);
    }
} catch (Exception $e) {
    
}