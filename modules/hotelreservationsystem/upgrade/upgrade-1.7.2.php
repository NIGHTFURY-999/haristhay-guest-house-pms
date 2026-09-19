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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_7_2($module)
{
    $objUpgrade = new UpgradeHotelreservationSystem172($module);
    return $objUpgrade->initUpgrade();
}

class UpgradeHotelreservationSystem172
{
    public function __construct($module)
    {
        $this->module = $module;
    }

    public function initUpgrade()
    {
        return $this->createWifiCredentialTable();
    }

    public function createWifiCredentialTable()
    {
        $db = Db::getInstance();

        $query = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_wifi_credential` (
            `id_wifi_credential` int(11) NOT NULL AUTO_INCREMENT,
            `id_htl_booking` int(11) NOT NULL,
            `id_customer` int(11) NOT NULL,
            `id_room` int(11) DEFAULT NULL,
            `username` varchar(128) NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `status` varchar(32) NOT NULL DEFAULT 'pending',
            `activated_at` datetime DEFAULT NULL,
            `expires_at` datetime DEFAULT NULL,
            `revoked_at` datetime DEFAULT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_wifi_credential`),
            UNIQUE KEY `uniq_username` (`username`),
            KEY `idx_id_htl_booking` (`id_htl_booking`),
            KEY `idx_id_customer` (`id_customer`),
            KEY `idx_id_room` (`id_room`),
            KEY `idx_status` (`status`),
            KEY `idx_expires_at` (`expires_at`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

        return (bool) $db->execute($query);
    }
}