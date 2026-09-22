<?php
/**
 * 2025-2026 Haristhay Guest House PMS
 * Online Check-In Administration
 */

class AdminOnlineCheckinController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'htl_online_checkin';
        $this->identifier = 'id_online_checkin';
        $this->context = Context::getContext();

        parent::__construct();

        $this->_new_list_header_design = true;

        $this->fields_list = array(
            'id_online_checkin' => array(
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
            'status' => array(
                'title' => $this->l('Status'),
                'align' => 'center',
            ),
            'submitted_at' => array(
                'title' => $this->l('Submitted'),
                'align' => 'center',
            ),
            'reviewed_at' => array(
                'title' => $this->l('Reviewed'),
                'align' => 'center',
            ),
        );

        $this->addRowAction('view');
    }

    public function getGuestName($idCustomer)
    {
        $customer = new Customer((int) $idCustomer);

        if (Validate::isLoadedObject($customer)) {
            return $customer->firstname . ' ' . $customer->lastname;
        }

        return $this->l('Unknown Guest');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitOnlineCheckinReview')) {
            $this->processReviewAction();
        }

        parent::postProcess();
    }

    protected function processReviewAction()
    {
        $idCheckin = (int) Tools::getValue('id_online_checkin');
        $action = Tools::getValue('review_action');
        $rejectionReason = trim((string) Tools::getValue('rejection_reason'));
        $identityVerified = (bool) Tools::getValue('identity_verified');

        if (!$idCheckin) {
            $this->errors[] = $this->l('Invalid online check-in record.');
            return;
        }

        $allowedActions = array(
            'under_review' => 'under_review',
            'approve' => 'approved',
            'reject' => 'rejected',
        );

        if (!isset($allowedActions[$action])) {
            $this->errors[] = $this->l('Invalid review action.');
            return;
        }

        if ($action === 'reject' && $rejectionReason === '') {
            $this->errors[] = $this->l('A rejection reason is required.');
            return;
        }
        if ($action === 'approve' && !$identityVerified) {
            $this->errors[] = $this->l('You must confirm that the guest identity document has been manually verified before approving.');
            return;
        }

        $checkin = Db::getInstance()->getRow(
            'SELECT `id_online_checkin`, `status`
             FROM `' . _DB_PREFIX_ . 'htl_online_checkin`
             WHERE `id_online_checkin` = ' . (int) $idCheckin
        );

        if (!$checkin) {
            $this->errors[] = $this->l('Online check-in record not found.');
            return;
        }

        if ($checkin['status'] === 'checked_in') {
            $this->errors[] = $this->l('A checked-in record cannot be reviewed again.');
            return;
        }

        $newStatus = $allowedActions[$action];

        $updated = Db::getInstance()->update(
            'htl_online_checkin',
            array(
                'status' => pSQL($newStatus),
                'reviewed_at' => date('Y-m-d H:i:s'),
                'reviewed_by' => (int) $this->context->employee->id,
                'rejection_reason' => $action === 'reject' ? pSQL($rejectionReason, true) : '',
                'date_upd' => date('Y-m-d H:i:s'),
            ),
            'id_online_checkin = ' . (int) $idCheckin
        );

        if (!$updated) {
            $this->errors[] = $this->l('Unable to update the online check-in status.');
            return;
        }

        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminOnlineCheckin')
            . '&' . $this->identifier . '=' . (int) $idCheckin
            . '&conf=4'
        );
    }

    public function renderView()
    {
        $idCheckin = (int) Tools::getValue($this->identifier);

        if (!$idCheckin) {
            return;
        }

        $checkin = Db::getInstance()->getRow(
            'SELECT *
             FROM `' . _DB_PREFIX_ . 'htl_online_checkin`
             WHERE `id_online_checkin` = ' . (int) $idCheckin
        );

        if (!$checkin) {
            $this->errors[] = $this->l('Online check-in record not found.');
            return;
        }

        $booking = new HotelBookingDetail((int) $checkin['id_htl_booking']);
        $customer = new Customer((int) $checkin['id_customer']);

        $documents = Db::getInstance()->executeS(
            'SELECT *
             FROM `' . _DB_PREFIX_ . 'htl_booking_document`
             WHERE `id_htl_booking` = ' . (int) $checkin['id_htl_booking'] . '
             AND `id_online_checkin` = ' . (int) $idCheckin
        );

        $additionalGuests = Db::getInstance()->executeS(
            'SELECT *
             FROM `' . _DB_PREFIX_ . 'htl_online_checkin_guest`
             WHERE `id_online_checkin` = ' . (int) $idCheckin
        );

        $this->context->smarty->assign(array(
            'checkin' => $checkin,
            'booking' => $booking,
            'customer' => $customer,
            'additional_guests' => $additionalGuests,
            'documents' => $documents,
        ));

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'hotelreservationsystem/views/templates/admin/onlinecheckin_view.tpl'
        );
    }
}
