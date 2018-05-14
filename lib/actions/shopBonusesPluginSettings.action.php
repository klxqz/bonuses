<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginSettingsAction extends waViewAction {

    protected $templates = array(
        'FrontendProduct' => array('name' => 'Шаблон для вывода в карточке товара', 'tpl_path' => 'plugins/bonuses/templates/FrontendProduct.html'),
        'FrontendCart' => array('name' => 'Шаблон для вывода в корзине', 'tpl_path' => 'plugins/bonuses/templates/FrontendCart.html'),
        'FrontendMy' => array('name' => 'Шаблон для вывода в личном кабинете', 'tpl_path' => 'plugins/bonuses/templates/FrontendMy.html'),
    );

    public function execute() {
        $plugin = shopBonusesPlugin::getThisPlugin();
        $settings = $plugin->getSettings();
        foreach ($this->templates as &$template) {
            $template['full_path'] = wa()->getDataPath($template['tpl_path'], false, 'shop', true);
            if (file_exists($template['full_path'])) {
                $template['change_tpl'] = true;
            } else {
                $template['full_path'] = wa()->getAppPath($template['tpl_path'], 'shop');
                $template['change_tpl'] = false;
            }
            $template['template'] = file_get_contents($template['full_path']);
        }

        $ccm = new waContactCategoryModel();
        $categories = $ccm->getByField('app_id', 'shop', true);
        $this->view->assign('categories', $categories);
        $this->view->assign('settings', $settings);
        $this->view->assign('templates', $this->templates);
    }

}
