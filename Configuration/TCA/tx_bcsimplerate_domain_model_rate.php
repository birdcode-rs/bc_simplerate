<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_db.xlf:tx_bcsimplerate_domain_model_rate',
        'label' => 'tablename',
        'label_alt' => 'recordid, rate',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate', 
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'recordid, tablename',
        'iconfile' => 'EXT:bc_simplerate/Resources/Public/Icons/star-rating-icon.svg',
    ],
    'interface' => [
        'showRecordFieldList' => 'rate, recordid, tablename',
    ],
    'types' => [
        '1' => [
            'showitem' => '
                sys_language_uid, l10n_parent, l10n_diffsource, hidden, rate, recordid, tablename,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime
           ',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language'
            ]

        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_bcsimplerate_domain_model_rate',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'default' => 0,
            ],
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => '',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'rate' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_db.xlf:tx_bcsimplerate_domain_model_rate.rate',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'recordid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_db.xlf:tx_bcsimplerate_domain_model_rate.recordid',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'tablename' => [
            'exclude' => true,
            'label' => 'LLL:EXT:bc_simplerate/Resources/Private/Language/locallang_db.xlf:tx_bcsimplerate_domain_model_rate.tablename',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
    ],
];
