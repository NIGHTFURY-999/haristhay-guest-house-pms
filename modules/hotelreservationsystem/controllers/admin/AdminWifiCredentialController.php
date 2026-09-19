<?php
/**
 * 2025-2026 Haristhay Guest House PMS
 * Wi-Fi Credential Administration
 */

class AdminWifiCredentialController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_wifi_credential';
        $this->identifier = 'id_wifi_credential';
        $this->context = Context::getContext();

        parent::__construct();

        $this->_new_list_header_design = true;

        $this->fields_list = array(
            'id_wifi_credential' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'id_htl_booking' => array(
                'title' => $this->l('Booking'),
                'align' => 'center',
            ),
            'id_customer' => array(
                'title' => $this->l('Guest'),
                'callback' => 'getGuestName',
            ),
            'id_room' => array(
                'title' => $this->l('Room'),
                'callback' => 'getRoomNumber',
                'align' => 'center',
            ),
            'username' => array(
                'title' => $this->l('Wi-Fi Username'),
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
            ),
            'activated_at' => array(
                'title' => $this->l('Activated'),
                'align' => 'center',
            ),
            'expires_at' => array(
                'title' => $this->l('Expires'),
                'align' => 'center',
            ),
        );

        $this->addRowAction('view');
        $this->addRowAction('revoke');
    }

    public function getGuestName($idCustomer)
    {
        $customer = new Customer((int) $idCustomer);

        if (Validate::isLoadedObject($customer)) {
            return $customer->firstname . ' ' . $customer->lastname;
        }

        return $this->l('Unknown Guest');
    }

    public function getRoomNumber($idRoom)
    {
        if (!$idRoom) {
            return '-';
        }

        $room = new HotelRoomInformation((int) $idRoom);

        if (Validate::isLoadedObject($room) && !empty($room->room_num)) {
            return $room->room_num;
        }

        return '-';
    }

    public function displayRevokeLink($token, $id, $name = null)
    {
        if (!$id) {
            return '';
        }

        $credential = new HotelWifiCredential((int) $id);

        if (!Validate::isLoadedObject($credential)) {
            return '';
        }

        if ($credential->status !== HotelWifiCredential::STATUS_ACTIVE) {
            return '';
        }

        $href = self::$currentIndex
            . '&' . $this->identifier . '=' . (int) $id
            . '&action=revoke'
            . '&token=' . Tools::getAdminTokenLite('AdminWifiCredential');

        return '<a href="'
            . htmlspecialchars($href, ENT_QUOTES, 'UTF-8')
            . '" class="btn btn-default" '
            . 'onclick="return confirm(\''
            . htmlspecialchars(
                $this->l('Are you sure you want to revoke this Wi-Fi credential?'),
                ENT_QUOTES,
                'UTF-8'
            )
            . '\');">'
            . '<i class="icon-remove"></i> '
            . $this->l('Revoke')
            . '</a>';
    }

    public function postProcess()
    {
        if (Tools::getValue('action') === 'revoke') {
            $this->processRevokeAction();
        }

        parent::postProcess();
    }

    protected function processRevokeAction()
    {
        $idCredential = (int) Tools::getValue($this->identifier);

        if (!$idCredential) {
            $this->errors[] = $this->l('Invalid Wi-Fi credential.');
            return;
        }

        $credential = new HotelWifiCredential($idCredential);

        if (!Validate::isLoadedObject($credential)) {
            $this->errors[] = $this->l('Wi-Fi credential not found.');
            return;
        }

        if ($credential->status !== HotelWifiCredential::STATUS_ACTIVE) {
            $this->errors[] = $this->l('Only active Wi-Fi credentials can be revoked.');
            return;
        }

        if (!$credential->revoke()) {
            $this->errors[] = $this->l('Unable to revoke the Wi-Fi credential.');
            return;
        }

        Tools::redirectAdmin(
            self::$currentIndex
            . '&token=' . Tools::getAdminTokenLite('AdminWifiCredential')
            . '&conf=4'
        );
    }

    public function renderView()
    {
        $idCredential = (int) Tools::getValue($this->identifier);

        if (!$idCredential) {
            return;
        }

        $credential = new HotelWifiCredential($idCredential);

        if (!Validate::isLoadedObject($credential)) {
            $this->errors[] = $this->l('Wi-Fi credential not found.');
            return;
        }

        $booking = new HotelBookingDetail((int) $credential->id_htl_booking);
        $customer = new Customer((int) $credential->id_customer);

        $this->context->smarty->assign(array(
            'credential' => $credential,
            'booking' => $booking,
            'customer' => $customer,
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'hotelreservationsystem/views/templates/admin/wifi_credential_view.tpl'
        );
    }
}