<?php

class shopBonusesPluginFrontendMyBonusesAction extends shopFrontendAction {

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        if (!$app_settings_model->get(shopBonusesPlugin::$plugin_id, 'status')) {
            throw new waException(_w('Unknown page'), 404);
        }
        $settings = $app_settings_model->get(shopBonusesPlugin::$plugin_id);

        $scm = new shopCustomerModel();
        $customer = $scm->getById(wa()->getUser()->getId());

        $atm = new shopAffiliateTransactionModel();
        $affiliate_history = $atm->getByContact(wa()->getUser()->getId());

        $url_tmpl = wa()->getRouteUrl('/frontend/myOrder', array('id' => '%ID%'));
        foreach ($affiliate_history as &$row) {
            if ($row['order_contact_id'] == $row['contact_id']) {
                $row['order_url'] = str_replace('%ID%', $row['order_id'], $url_tmpl);
            }
        }

        $this->view->assign('customer', $customer);
        $this->view->assign('affiliate_history', $affiliate_history);

        // Set up layout and template from theme
        $this->setThemeTemplate($settings['frontend_my_template']);

        if (!waRequest::isXMLHttpRequest()) {
            $this->setLayout(new shopFrontendLayout());
            $this->getResponse()->setTitle(_w('Affiliate program'));
            $this->view->assign('breadcrumbs', self::getBreadcrumbs());
            $this->layout->assign('nofollow', true);
        }

        /**
         *
         * @event frontend_my_affiliate
         * @return array[string]string $return[%plugin_id%] html output
         */
        $this->view->assign('frontend_my_affiliate', wa()->event('frontend_my_affiliate'));
    }

    public static function getBreadcrumbs() {
        return array(
            array(
                'name' => _w('My account'),
                'url' => wa()->getRouteUrl('/frontend/my'),
            ),
        );
    }

}
