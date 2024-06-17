<?php

use DVC\AsyncPagination\Controller\ContentElement\AsyncPaginationWrapperController;

$GLOBALS['TL_DCA']['tl_content']['fields']['asyncPaginationWrapperListModule'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['asyncPaginationWrapperListModule'],
    'inputType' => 'select',
    'foreignKey' => 'tl_module.name',
    'eval' => [
        'includeBlankOption' => true,
        'chosen' => true,
        'tl_class' => 'w50',
    ],
    'sql' => [
        'type' => 'integer',
        'unsigned' => true,
        'notnull' => false,
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['asyncPaginationWrapperFilterModule'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_content']['asyncPaginationWrapperFilterModule'],
    'inputType' => 'select',
    'foreignKey' => 'tl_module.name',
    'eval' => [
        'includeBlankOption' => true,
        'chosen' => true,
        'tl_class' => 'w50',
    ],
    'sql' => [
        'type' => 'integer',
        'unsigned' => true,
        'notnull' => false,
    ],
];

$GLOBALS['TL_DCA']['tl_content']['palettes'][AsyncPaginationWrapperController::TYPE] =
    '{type_legend},type;{include_legend},asyncPaginationWrapperListModule,asyncPaginationWrapperFilterModule;{expert_legend:hide},cssID;{invisible_legend:hide},invisible,start,stop'
;
