<?php

namespace DVC\AsyncPagination\EventListener;

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook('getContentElement')]
class AddWrapperToContentElementListener
{
    public function __invoke(ContentModel $contentModel, string $buffer): string
    {
        if (!\in_array($contentModel->type, $this->getSupportedElementTypes())) {
            return $buffer;
        }

        if ($contentModel?->allowAjaxReload != true) {
            return $buffer;
        }

        return \sprintf('<div data-module="pagination-reload">%s</div>', $buffer);
    }

    private function getSupportedElementTypes(): array
    {
        return [
            'gallery',
        ];
    }
}
