<?php

namespace DVC\AsyncPagination\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\ModuleModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(
    category: 'includes',
    nestedFragments: true,
)]
class AsyncDeferredLoadWrapperController extends AbstractContentElementController
{
    public const TYPE = 'async_deferred_load_wrapper';

    public function __construct(
    ) {
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        $elements = [];

        foreach ($template->get('nested_fragments') as $reference) {
            $nestedContentModel = $reference->getContentModel();

            $elements[] = [
                'attributes' => [
                    'data-element' => 'target',
                    'data-type' => 'content',
                    'data-id' => $nestedContentModel,
                ],
                'reference' => $reference,
            ];
        }

        $template->set('elements', $elements);

        return $template->getResponse();
    }
}
