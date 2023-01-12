<?php
defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/* --------------------------------------------------
	Extend existing tables
-------------------------------------------------- */

$tempColumns = [
    'tx_odsosm_marker' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:ods_osm/Resources/Private/Language/locallang_db.xlf:tt_address_group.tx_odsosm_marker',
        'config' => [
            'type' => 'group',
            'internal_type' => 'db',
            'allowed' => 'tx_odsosm_marker',
            'size' => 1,
            'minitems' => 0,
            'maxitems' => 1,
            'default' => 0,
        ]
    ],
];

ExtensionManagementUtility::addTCAcolumns('sys_category', $tempColumns);
ExtensionManagementUtility::addToAllTCAtypes('sys_category', 'tx_odsosm_marker');
