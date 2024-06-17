<?php

namespace DVC\AsyncPagination\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(category: 'includes')]
class AsyncPaginationWrapperController extends AbstractContentElementController
{
    public const TYPE = 'async_pagination_wrapper';

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        $template->class = \trim(\str_replace('ce_' . self::TYPE, '', $template->class));

        $template->listModuleId = $model->asyncPaginationWrapperListModule;
        $template->filterModuleId = $model->asyncPaginationWrapperFilterModule;

        return $template->getResponse();
    }
}
