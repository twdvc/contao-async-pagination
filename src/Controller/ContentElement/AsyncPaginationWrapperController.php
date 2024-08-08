<?php

namespace DVC\AsyncPagination\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\ModuleModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(
    category: 'includes',
    nestedFragments: ['allowedTypes' => ['module', 'gallery']]
)]
class AsyncPaginationWrapperController extends AbstractContentElementController
{
    public const TYPE = 'async_pagination_wrapper';

    public function __construct(
        private readonly ContaoFramework $framework,
    ) {
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response
    {
        $elements = [];

        foreach ($template->get('nested_fragments') as $reference) {
            $nestedContentModel = $reference->getContentModel();
            $nestedModuleModel = null;
            // echo var_dump($reference, $nestedContentModel);
            // echo '<br>';
            // echo '<br>';

            if (!$nestedContentModel instanceof ContentModel) {
                $nestedContentModel = $this->framework->getAdapter(ContentModel::class)->findById($nestedContentModel);
            }

            if ($nestedContentModel->type == 'module') {
                $nestedModuleModel = $this->framework->getAdapter(ModuleModel::class)->findById($nestedContentModel->module);
            }

            // if ($nestedModuleModel === null) {
            //     continue;
            // }

            // $elementIdentifier = self::cleanIdentifier($nestedModuleModel->type);

            // $elements[] = [
            //     'attributes' => [
            //         'data-element' => self::cleanIdentifier($nestedModuleModel->type),
            //         'data-id' => $nestedModuleModel->id,
            //         'data-type' => self::getTypeForModel($nestedContentModel),
            //     ],
            //     'reference' => $reference,
            // ];

            // $elementIdentifier = self::cleanIdentifier($nestedContentModel->type);

            $elements[] = [
                'attributes' => [
                    'data-element' => self::getIdentifierForModel($nestedModuleModel ?? $nestedContentModel),
                    'data-type' => self::getTypeForModel($nestedModuleModel ?? $nestedContentModel),
                    'data-id' => $nestedModuleModel?->id ?? $nestedContentModel->id,
                ],
                'reference' => $reference,
            ];
        }

        $template->set('elements', $elements);

        return $template->getResponse();
    }

    private static function getTypeForModel(ContentModel|ModuleModel $model): string
    {
        // echo var_dump($model);
        // echo '<br>';
        // echo '<br>';

        if ($model instanceof ModuleModel) {
            return 'module';
        }

        return 'content';
    }

    private static function getIdentifierForModel(ContentModel|ModuleModel $model): string
    {
        if ($model instanceof ModuleModel) {
            if ($model->perPage > 0) {
                return 'paginated';
            }

            // return 'module';
        }

        // return 'content';
        $modulePrefixesToRemove = ['news'];

        return str_replace($modulePrefixesToRemove, '', $model->type);
    }

    // private static function cleanIdentifier(string $identifer): string
    // {
    //     $modulePrefixesToRemove = ['news'];

    //     return str_replace($modulePrefixesToRemove, '', $identifer);
    // }
}
