<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_7_3($module)
{
    $objUpgrade = new UpgradeHotelreservationSystem173($module);

    return $objUpgrade->initUpgrade();
}

class UpgradeHotelreservationSystem173
{
    protected $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function initUpgrade()
    {
        return $this->addOnlineCheckinDocumentLink();
    }

    protected function addOnlineCheckinDocumentLink()
    {
        $db = Db::getInstance();

        $columnExists = $db->executeS(
            'SHOW COLUMNS FROM `'._DB_PREFIX_.'htl_booking_document`
             LIKE "id_online_checkin"'
        );

        if (!empty($columnExists)) {
            return true;
        }

        return (bool) $db->execute(
            'ALTER TABLE `'._DB_PREFIX_.'htl_booking_document`
             ADD `id_online_checkin` int(11) DEFAULT NULL
             AFTER `id_htl_booking`'
        );
    }
}
