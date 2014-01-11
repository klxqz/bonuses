<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginSettingsAction extends waViewAction {

    protected $templates = array(
        'FrontendProduct' => array('name' => 'Шаблон для вывода в карточке товара', 'tpl_path' => 'plugins/bonuses/templates/FrontendProduct.html'),
        'FrontendCategory' => array('name' => 'Шаблон для вывода в категории товара', 'tpl_path' => 'plugins/bonuses/templates/FrontendCategory.html'),
        'FrontendCart' => array('name' => 'Шаблон для вывода в корзине', 'tpl_path' => 'plugins/bonuses/templates/FrontendCart.html'),
        'FrontendMy' => array('name' => 'Шаблон для вывода в личном кабинете', 'tpl_path' => 'plugins/bonuses/templates/FrontendMy.html'),
    );
    protected $plugin_id = array('shop', 'bonuses');

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        $settings = $app_settings_model->get($this->plugin_id);
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

        $this->view->assign('settings', $settings);
        $this->view->assign('templates', $this->templates);
    }

}
