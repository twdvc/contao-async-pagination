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

        $matches = null;
        \preg_match('/data-ajax-reload-element=".*"/', $buffer, $matches);

        if ($matches === null) {
            return $buffer;
        }

        return \str_replace($matches[0], $matches[0] . ' data-module="pagination-reload"', $buffer);
    }

    private function getSupportedElementTypes(): array
    {
        return [
            'gallery',
        ];
    }
}
