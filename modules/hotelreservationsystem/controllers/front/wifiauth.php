<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/osl-3-0-php
 *
 * DISCLAIMER
 *
 * This file is part of the Haristhay Guest House PMS customization.
 * It retains the original QloApps/Webkul licensing and attribution.
 */

class HotelReservationSystemWifiauthModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function postProcess()
    {
        parent::postProcess();

        if (!Tools::isSubmit('submitWifiAuth')) {
            return;
        }

        $username = trim((string) Tools::getValue('username'));
        $password = (string) Tools::getValue('password');

        if (!$username || !$password) {
            $this->sendAuthenticationResponse(false, 'Username and password are required.');
            return;
        }

        $credential = HotelWifiCredential::authenticate($username, $password);

        if (!$credential) {
            $this->sendAuthenticationResponse(false, 'Invalid or expired Wi-Fi credentials.');
            return;
        }

        $this->sendAuthenticationResponse(true, 'Wi-Fi authentication successful.', array(
            'username' => $credential->username,
            'expires_at' => $credential->expires_at,
        ));
    }

    protected function sendAuthenticationResponse($success, $message, $data = array())
    {
        header('Content-Type: application/json');

        die(Tools::jsonEncode(array(
            'success' => (bool) $success,
            'message' => $message,
            'data' => $data,
        )));
    }

    public function initContent()
    {
        parent::initContent();

        if (Tools::isSubmit('submitWifiAuth')) {
            return;
        }

        $this->context->smarty->assign(array(
            'wifi_auth_action' => $this->context->link->getModuleLink(
                $this->module->name,
                'wifiauth',
                array(),
                true
            ),
        ));

        $this->setTemplate(
            'module:hotelreservationsystem/views/templates/front/wifiauth.tpl'
        );
    }
}