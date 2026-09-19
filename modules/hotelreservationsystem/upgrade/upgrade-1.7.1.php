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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_7_1($module)
{
    $objUpgrade = new UpgradeHotelreservationSystem171($module);
    return $objUpgrade->initUpgrade();
}

class UpgradeHotelreservationSystem171
{
    public function __construct($module)
    {
        $this->module = $module;
    }

    public function initUpgrade()
    {
        return $this->createOnlineCheckinTables();
    }

    public function createOnlineCheckinTables()
    {
        $db = Db::getInstance();
        $res = true;

        /*
         * Create online check-in tables if they do not already exist.
         */
        $queries = array(
            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_online_checkin` (
                `id_online_checkin` int(11) NOT NULL AUTO_INCREMENT,
                `id_htl_booking` int(11) NOT NULL,
                `id_customer` int(11) NOT NULL,
                `status` varchar(32) NOT NULL DEFAULT 'pending',
                `id_type` varchar(64) DEFAULT NULL,
                `id_number` varchar(128) DEFAULT NULL,
                `passport_number` varchar(128) DEFAULT NULL,
                `passport_date_of_expiry` date DEFAULT NULL,
                `checkin_token_hash` varchar(64) DEFAULT NULL,
                `token_expires_at` datetime DEFAULT NULL,
                `id_place_of_issue` varchar(128) DEFAULT NULL,
                `id_date_of_issue` date DEFAULT NULL,
                `id_date_of_expiry` date DEFAULT NULL,
                `purpose_of_visit` varchar(255) DEFAULT NULL,
                `is_foreign_guest` tinyint(1) NOT NULL DEFAULT '0',
                `visa_number` varchar(128) DEFAULT NULL,
                `visa_valid_until` date DEFAULT NULL,
                `terms_accepted` tinyint(1) NOT NULL DEFAULT '0',
                `guest_declaration` text,
                `guest_signature` text,
                `submitted_at` datetime DEFAULT NULL,
                `reviewed_at` datetime DEFAULT NULL,
                `reviewed_by` int(11) DEFAULT NULL,
                `rejection_reason` text,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_online_checkin`),
                KEY `idx_id_htl_booking` (`id_htl_booking`),
                KEY `idx_id_customer` (`id_customer`),
                KEY `idx_status` (`status`),
                KEY `idx_reviewed_by` (`reviewed_by`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",

            "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."htl_online_checkin_guest` (
                `id_online_checkin_guest` int(11) NOT NULL AUTO_INCREMENT,
                `id_online_checkin` int(11) NOT NULL,
                `full_name` varchar(255) NOT NULL,
                `nationality` varchar(128) DEFAULT NULL,
                `date_of_birth` date DEFAULT NULL,
                `id_type` varchar(64) DEFAULT NULL,
                `id_number` varchar(128) DEFAULT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_online_checkin_guest`),
                KEY `idx_id_online_checkin` (`id_online_checkin`)
            ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;"
        );

        foreach ($queries as $query) {
            if (!$db->execute($query)) {
                return false;
            }
        }

        /*
         * Add fields introduced after the first 1.7.1 schema.
         */
        $currentFields = $db->executeS(
            'SHOW FIELDS FROM `'._DB_PREFIX_.'htl_online_checkin`'
        );

        foreach ($currentFields as $key => $field) {
            $currentFields[$key] = $field['Field'];
        }

        $missingFields = array(
            'passport_date_of_expiry' => 'ALTER TABLE `'._DB_PREFIX_.'htl_online_checkin`
                ADD `passport_date_of_expiry` date DEFAULT NULL',

            'checkin_token_hash' => 'ALTER TABLE `'._DB_PREFIX_.'htl_online_checkin`
                ADD `checkin_token_hash` varchar(64) DEFAULT NULL',

            'token_expires_at' => 'ALTER TABLE `'._DB_PREFIX_.'htl_online_checkin`
                ADD `token_expires_at` datetime DEFAULT NULL'
        );

        foreach ($missingFields as $field => $query) {
            if (!in_array($field, $currentFields)) {
                $res &= $db->execute($query);
            }
        }

        /*
         * Link existing booking documents to online check-in records.
         * These columns are nullable so existing booking documents remain valid.
         */
        $documentFields = $db->executeS(
            'SHOW FIELDS FROM `'._DB_PREFIX_.'htl_booking_document`'
        );

        $documentColumns = array();

        foreach ($documentFields as $field) {
            $documentColumns[] = $field['Field'];
        }

        $documentMissingFields = array(
            'id_online_checkin' => 'ALTER TABLE `'._DB_PREFIX_.'htl_booking_document`
                ADD `id_online_checkin` int(11) NULL AFTER `id_htl_booking`',

            'id_online_checkin_guest' => 'ALTER TABLE `'._DB_PREFIX_.'htl_booking_document`
                ADD `id_online_checkin_guest` int(11) NULL AFTER `id_online_checkin`'
        );

        foreach ($documentMissingFields as $field => $query) {
            if (!in_array($field, $documentColumns)) {
                $res &= $db->execute($query);
            }
        }

        return (bool) $res;
    }
}
