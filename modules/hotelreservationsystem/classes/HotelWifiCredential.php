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

class HotelWifiCredential extends ObjectModel
{
    public $id_wifi_credential;
    public $id_htl_booking;
    public $id_customer;
    public $id_room;
    public $username;
    public $password_hash;
    public $status;
    public $activated_at;
    public $expires_at;
    public $revoked_at;
    public $date_add;
    public $date_upd;

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REVOKED = 'revoked';

    public static $definition = array(
        'table' => 'htl_wifi_credential',
        'primary' => 'id_wifi_credential',
        'fields' => array(
            'id_htl_booking' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'id_customer' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'required' => true,
            ),
            'id_room' => array(
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ),
            'username' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ),
            'password_hash' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ),
            'status' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'required' => true,
            ),
            'activated_at' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ),
            'expires_at' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ),
            'revoked_at' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ),
            'date_add' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => true,
            ),
            'date_upd' => array(
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'required' => true,
            ),
        ),
    );

    /**
     * Generate a secure random Wi-Fi username.
     */
    public static function generateUsername()
    {
        return 'HGH-' . strtoupper(bin2hex(random_bytes(5)));
    }

    /**
     * Generate a secure random Wi-Fi password.
     */
    public static function generatePassword($length = 14)
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $charactersLength = Tools::strlen($characters);
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $password;
    }

    /**
     * Create a pending Wi-Fi credential for a booking.
     *
     * The plaintext password is returned only at creation time.
     */
    public static function createForBooking($idHtlBooking, $idCustomer, $idRoom = null)
{
    $existing = Db::getInstance()->getValue(
        'SELECT `id_wifi_credential`
        FROM `'._DB_PREFIX_.'htl_wifi_credential`
        WHERE `id_htl_booking` = '.(int) $idHtlBooking.'
        AND `status` IN ("'.pSQL(self::STATUS_PENDING).'", "'.pSQL(self::STATUS_ACTIVE).'")
        ORDER BY `id_wifi_credential` DESC'
    );

    if ($existing) {
        return false;
    }

    $plainPassword = self::generatePassword();

        $credential = new self();
        $credential->id_htl_booking = (int) $idHtlBooking;
        $credential->id_customer = (int) $idCustomer;
        $credential->id_room = $idRoom ? (int) $idRoom : null;
        $credential->username = self::generateUsername();
        $credential->password_hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        $credential->status = self::STATUS_PENDING;
        $credential->date_add = date('Y-m-d H:i:s');
        $credential->date_upd = date('Y-m-d H:i:s');

        if (!$credential->add()) {
            return false;
        }

        return array(
            'id_wifi_credential' => (int) $credential->id,
            'username' => $credential->username,
            'password' => $plainPassword,
        );
    }

    /**
     * Activate a pending Wi-Fi credential.
     */
    public function activate($expiresAt)
    {
        if (!$this->id || $this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->status = self::STATUS_ACTIVE;
        $this->activated_at = date('Y-m-d H:i:s');
        $this->expires_at = $expiresAt;
        $this->revoked_at = null;
        $this->date_upd = date('Y-m-d H:i:s');

        return $this->update();
    }

    /**
     * Expire the credential.
     */
    public function expire()
    {
        if (!$this->id) {
            return false;
        }

        $this->status = self::STATUS_EXPIRED;
        $this->date_upd = date('Y-m-d H:i:s');

        return $this->update();
    }

    /**
     * Revoke the credential manually.
     */
    public function revoke()
    {
        if (!$this->id) {
            return false;
        }

        $this->status = self::STATUS_REVOKED;
        $this->revoked_at = date('Y-m-d H:i:s');
        $this->date_upd = date('Y-m-d H:i:s');

        return $this->update();
    }

    /**
     * Authenticate an active Wi-Fi credential.
     */
    public static function authenticate($username, $password)
    {
        $credential = new self();

        $idCredential = (int) Db::getInstance()->getValue(
            'SELECT `id_wifi_credential`
            FROM `'._DB_PREFIX_.'htl_wifi_credential`
            WHERE `username` = "'.pSQL($username).'"
            AND `status` = "'.pSQL(self::STATUS_ACTIVE).'"'
        );

        if (!$idCredential) {
            return false;
        }

        $credential = new self($idCredential);

        if (!Validate::isLoadedObject($credential)) {
            return false;
        }

        if (!empty($credential->expires_at)
            && strtotime($credential->expires_at) <= time()) {
            $credential->expire();
            return false;
        }

        if (!password_verify($password, $credential->password_hash)) {
            return false;
        }

        return $credential;
    }

    /**
     * Get the active Wi-Fi credential for a booking.
     */
    public static function getActiveByBooking($idHtlBooking)
    {
        $idCredential = (int) Db::getInstance()->getValue(
            'SELECT `id_wifi_credential`
            FROM `'._DB_PREFIX_.'htl_wifi_credential`
            WHERE `id_htl_booking` = '.(int) $idHtlBooking.'
            AND `status` = "'.pSQL(self::STATUS_ACTIVE).'"
            ORDER BY `id_wifi_credential` DESC'
        );

        if (!$idCredential) {
            return false;
        }

        $credential = new self($idCredential);

        return Validate::isLoadedObject($credential) ? $credential : false;
    }

    /**
     * Expire all active credentials for a booking.
     */
    public static function expireByBooking($idHtlBooking)
    {
        $credentials = Db::getInstance()->executeS(
            'SELECT `id_wifi_credential`
            FROM `'._DB_PREFIX_.'htl_wifi_credential`
            WHERE `id_htl_booking` = '.(int) $idHtlBooking.'
            AND `status` = "'.pSQL(self::STATUS_ACTIVE).'"'
        );

        if (!$credentials) {
            return true;
        }

        foreach ($credentials as $row) {
            $credential = new self((int) $row['id_wifi_credential']);

            if (Validate::isLoadedObject($credential) && !$credential->expire()) {
                return false;
            }
        }

        return true;
    }
}