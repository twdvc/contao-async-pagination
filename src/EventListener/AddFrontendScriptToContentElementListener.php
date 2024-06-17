<?php

namespace DVC\AsyncPagination\EventListener;

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use DVC\AsyncPagination\Controller\ContentElement\AsyncPaginationWrapperController;

#[AsHook('getContentElement')]
class AddFrontendScriptToContentElementListener
{
    private const ASSET_PATH = 'bundles/asyncpagination/async-pagination.js';
    private const ASSET_VERSION = '1';

    public function __invoke(ContentModel $contentModel, string $buffer): string
    {
        if (\in_array($contentModel->type, $this->getSupportedElementTypes())) {
            $GLOBALS['TL_HEAD'][] = \sprintf('<script src="%s?v=%s" defer></script>', self::ASSET_PATH, self::ASSET_VERSION);
        }

        return $buffer;
    }

    private function getSupportedElementTypes(): array
    {
        return [
            'gallery',
            AsyncPaginationWrapperController::TYPE,
        ];
    }
}
