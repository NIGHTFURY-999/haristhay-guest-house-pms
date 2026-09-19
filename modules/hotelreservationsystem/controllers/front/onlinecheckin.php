<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 *
 * @author Haristhay Guest House PMS
 * @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
 */

class HotelReservationSystemOnlineCheckinModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $token = trim((string) Tools::getValue('token'));

        if (!$token || !preg_match('/^[a-f0-9]{64}$/i', $token)) {
            $this->renderError('Invalid check-in link.');
            return;
        }

        $tokenHash = hash('sha256', $token);

        $checkin = Db::getInstance()->getRow(
            'SELECT *
             FROM `'._DB_PREFIX_.'htl_online_checkin`
             WHERE `checkin_token_hash` = "'.pSQL($tokenHash).'"
             LIMIT 1'
        );

        if (!$checkin) {
            $this->renderError('This check-in link is invalid or has expired.');
            return;
        }

        if (!empty($checkin['token_expires_at'])
            && strtotime($checkin['token_expires_at']) < time()) {
            $this->renderError('This check-in link has expired.');
            return;
        }

        $booking = new HotelBookingDetail((int) $checkin['id_htl_booking']);

        if (!Validate::isLoadedObject($booking)) {
            $this->renderError('The booking associated with this check-in link could not be found.');
            return;
        }

        if (Tools::isSubmit('submitOnlineCheckin')) {
            if (in_array(
                $checkin['status'],
                array('submitted', 'under_review', 'approved', 'rejected', 'checked_in'),
                true
            )) {
                $this->context->smarty->assign(
                    'checkin_error',
                    'This online check-in has already been submitted and cannot be submitted again.'
                );
                return;
            }

            $this->processOnlineCheckin($checkin);
        }

        $this->context->smarty->assign(array(
            'checkin' => $checkin,
            'booking' => $booking,
        ));

        $this->setTemplate(
            'module:hotelreservationsystem/views/templates/front/onlinecheckin.tpl'
        );
    }

    protected function processOnlineCheckin($checkin)
    {
        $idCheckin = (int) $checkin['id_online_checkin'];
        $idCustomer = (int) $checkin['id_customer'];
        $customer = new Customer($idCustomer);

        if (!Validate::isLoadedObject($customer)) {
            $this->context->smarty->assign(
                'checkin_error',
                'The guest account associated with this booking could not be found.'
            );
            return;
        }

        $idBooking = (int) $checkin['id_htl_booking'];
        $now = date('Y-m-d H:i:s');

        $fullName = trim((string) Tools::getValue('full_name'));
        $phone = trim((string) Tools::getValue('phone'));
        $email = trim((string) Tools::getValue('email'));
        $dateOfBirth = trim((string) Tools::getValue('date_of_birth'));
        $nationality = trim((string) Tools::getValue('nationality'));
        $address = trim((string) Tools::getValue('address'));
        $city = trim((string) Tools::getValue('city'));
        $state = trim((string) Tools::getValue('state'));
        $postalCode = trim((string) Tools::getValue('postal_code'));

        $idType = trim((string) Tools::getValue('id_type'));
        $idNumber = trim((string) Tools::getValue('id_number'));
        $passportNumber = trim((string) Tools::getValue('passport_number'));
        $passportExpiry = trim((string) Tools::getValue('passport_date_of_expiry'));
        $idPlaceOfIssue = trim((string) Tools::getValue('id_place_of_issue'));
        $idDateOfIssue = trim((string) Tools::getValue('id_date_of_issue'));
        $idDateOfExpiry = trim((string) Tools::getValue('id_date_of_expiry'));


        $purposeOfVisit = trim((string) Tools::getValue('purpose_of_visit'));
        $isForeignGuest = (int) Tools::getValue('is_foreign_guest');
        $visaNumber = trim((string) Tools::getValue('visa_number'));
        $visaValidUntil = trim((string) Tools::getValue('visa_valid_until'));

                if ($isForeignGuest) {
            if (!$passportNumber || !$passportExpiry || !$visaNumber || !$visaValidUntil) {
                $this->context->smarty->assign(
                    'checkin_error',
                    'Foreign guests must provide passport and visa information.'
                );
                return;
            }
        }
        $termsAccepted = (int) Tools::getValue('terms_accepted');
        $guestDeclaration = trim((string) Tools::getValue('guest_declaration'));
        $guestSignature = trim((string) Tools::getValue('guest_signature'));

        if (!$fullName || !$phone || !$email || !$dateOfBirth || !$nationality
            || !$address || !$city || !$idType || !$idNumber || !$purposeOfVisit
            || !$termsAccepted) {
            $this->context->smarty->assign(
                'checkin_error',
                'Please complete all required fields.'
            );
            return;
        }

        if (!Validate::isEmail($email)) {
            $this->context->smarty->assign(
                'checkin_error',
                'Please enter a valid email address.'
            );
            return;
        }

        $idAddress = (int) Customer::getCustomerIdAddress($idCustomer, false);
        $customerAddress = $idAddress ? new Address($idAddress) : new Address();

        $customerAddress->id_customer = $idCustomer;
        $customerAddress->firstname = $customer->firstname;
        $customerAddress->lastname = $customer->lastname;
        $customerAddress->address1 = $address;
        $customerAddress->city = $city;
        $customerAddress->postcode = $postalCode;
        $customerAddress->phone_mobile = $phone;
        $customerAddress->alias = 'Online Check-In';

        if (!$customerAddress->id) {
            if (!$customerAddress->add()) {
                $this->context->smarty->assign(
                    'checkin_error',
                    'Unable to save your address information.'
                );
                return;
            }
        } elseif (!$customerAddress->update()) {
            $this->context->smarty->assign(
                'checkin_error',
                'Unable to update your address information.'
            );
            return;
        }
        $dateFields = array(
            'date_of_birth' => $dateOfBirth,
            'id_date_of_issue' => $idDateOfIssue,
            'id_date_of_expiry' => $idDateOfExpiry,
            'passport_date_of_expiry' => $passportExpiry,
            'visa_valid_until' => $visaValidUntil,
        );

        foreach ($dateFields as $fieldName => $dateValue) {
            if ($dateValue && !Validate::isDate($dateValue)) {
                $this->context->smarty->assign(
                    'checkin_error',
                    'Please enter valid dates.'
                );
                return;
            }
        }
        if (!isset($_FILES['id_document'])
            || empty($_FILES['id_document']['tmp_name'])
            || $_FILES['id_document']['error'] !== UPLOAD_ERR_OK) {
            $this->context->smarty->assign(
                'checkin_error',
                'Please upload a valid identification document.'
            );
            return;
        }

        $maxDocumentSize = 5 * 1024 * 1024;

        if ((int) $_FILES['id_document']['size'] > $maxDocumentSize) {
            $this->context->smarty->assign(
                'checkin_error',
                'The identification document must be 5 MB or smaller.'
            );
            return;
        }

        Db::getInstance()->delete(
            'htl_online_checkin_guest',
            'id_online_checkin = '.(int) $idCheckin
        );
        $additionalGuests = Tools::getValue('additional_guest');

        if (!is_array($additionalGuests)) {
            $additionalGuests = array();
        }

        foreach ($additionalGuests as $guest) {
            if (!is_array($guest)) {
                continue;
            }

            $guestName = trim((string) (isset($guest['full_name']) ? $guest['full_name'] : ''));
            $guestNationality = trim((string) (isset($guest['nationality']) ? $guest['nationality'] : ''));
            $guestDob = trim((string) (isset($guest['date_of_birth']) ? $guest['date_of_birth'] : ''));
            $guestIdType = trim((string) (isset($guest['id_type']) ? $guest['id_type'] : ''));
            $guestIdNumber = trim((string) (isset($guest['id_number']) ? $guest['id_number'] : ''));

            if (!$guestName) {
                continue;
            }

            if ($guestDob && !Validate::isDate($guestDob)) {
                $this->context->smarty->assign(
                    'checkin_error',
                    'Please enter a valid date of birth for each additional guest.'
                );
                return;
            }

            if (!$guestIdType || !$guestIdNumber) {
                $this->context->smarty->assign(
                    'checkin_error',
                    'Please complete the ID details for each additional guest.'
                );
                return;
            }

            $guestNow = date('Y-m-d H:i:s');

            $guestSql = 'INSERT INTO `'._DB_PREFIX_.'htl_online_checkin_guest`
                (`id_online_checkin`, `full_name`, `nationality`, `date_of_birth`, `id_type`, `id_number`, `date_add`)
                VALUES (
                    '.(int) $idCheckin.',
                    "'.pSQL($guestName).'",
                    "'.pSQL($guestNationality).'",
                    '.($guestDob ? '"'.pSQL($guestDob).'"' : 'NULL').',
                    "'.pSQL($guestIdType).'",
                    "'.pSQL($guestIdNumber).'",
                    "'.pSQL($guestNow).'"
                )';

            if (!Db::getInstance()->execute($guestSql)) {
                $this->context->smarty->assign(
                    'checkin_error',
                    'Unable to save additional guest information.'
                );
                return;
            }
        }

        $document = new HotelBookingDocument();
        $document->id_htl_booking = $idBooking;
        $document->title = 'Guest Identification Document';
        $document->setFileInfoForUploadedDocument('id_document');
        $document->setFileType();

        if (!in_array(
            (int) $document->file_type,
            array(
                HotelBookingDocument::FILE_TYPE_IMAGE,
                HotelBookingDocument::FILE_TYPE_PDF,
            )
        )) {
            $this->context->smarty->assign(
                'checkin_error',
                'The uploaded identification document is not a valid image or PDF.'
            );
            return;
        }

        $sql = 'UPDATE `'._DB_PREFIX_.'htl_online_checkin`
                SET
                    `id_type` = "'.pSQL($idType).'",
                    `id_number` = "'.pSQL($idNumber).'",
                    `passport_number` = "'.pSQL($passportNumber).'",
                    `passport_date_of_expiry` = '.($passportExpiry ? '"'.pSQL($passportExpiry).'"' : 'NULL').',
                    `id_place_of_issue` = "'.pSQL($idPlaceOfIssue).'",
                    `id_date_of_issue` = '.($idDateOfIssue ? '"'.pSQL($idDateOfIssue).'"' : 'NULL').',
                    `id_date_of_expiry` = '.($idDateOfExpiry ? '"'.pSQL($idDateOfExpiry).'"' : 'NULL').',
                    `purpose_of_visit` = "'.pSQL($purposeOfVisit).'",
                    `is_foreign_guest` = '.(int) $isForeignGuest.',
                    `visa_number` = "'.pSQL($visaNumber).'",
                    `visa_valid_until` = '.($visaValidUntil ? '"'.pSQL($visaValidUntil).'"' : 'NULL').',
                    `terms_accepted` = '.(int) $termsAccepted.',
                    `guest_declaration` = "'.pSQL($guestDeclaration).'",
                    `guest_signature` = "'.pSQL($guestSignature).'",
                    `status` = "submitted",
                    `submitted_at` = "'.pSQL($now).'",
                    `date_upd` = "'.pSQL($now).'"
                WHERE `id_online_checkin` = '.(int) $idCheckin;

        if (!Db::getInstance()->execute($sql)) {
            $this->context->smarty->assign(
                'checkin_error',
                'Unable to save your check-in information. Please try again.'
            );
            return;
        }

        if (!$document->save()) {
            $this->context->smarty->assign(
                'checkin_error',
                'Unable to save the identification document.'
            );
            return;
        }

        $document->saveDocumentFile();

        $this->context->smarty->assign(
            'checkin_success',
            'Your online check-in has been submitted successfully. The hotel will review your information.'
        );
    }

    protected function renderError($message)
    {
        $this->context->smarty->assign(
            'checkin_error',
            $message
        );

        $this->setTemplate(
            'module:hotelreservationsystem/views/templates/front/onlinecheckin-error.tpl'
        );
    }
}
