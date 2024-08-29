<?php

use DVC\AsyncPagination\Controller\ContentElement\AsyncPaginationWrapperController;

$GLOBALS['TL_DCA']['tl_content']['palettes'][AsyncPaginationWrapperController::TYPE] =
    '{type_legend},type;{expert_legend:hide},cssID;{invisible_legend:hide},invisible,start,stop'
;
