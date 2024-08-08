<?php

namespace DVC\AsyncPagination\Controller;

use Contao\ArticleModel;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\ModuleModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;
// use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/_dvc/ajax/{moduleType}/{typeOrId}',
    name: AjaxEndpointController::class,
    // requirements: ['typeOrId' => '\d+'],
    defaults:[
        '_scope' => 'frontend',
        '_token_check' => false,
    ]
)]
#[AsController]
class AjaxEndpointController
{
    public function __construct(
        private readonly ContaoFramework $framework,
    ) {
    }

    public function __invoke(Request $request, string $moduleType, int|string $typeOrId): JsonResponse
    {
        $this->framework->initialize();

        switch ($moduleType) {
            case 'module':
                // \Contao\Input::setGet('category', 'aktuelles');

                $html = $this->renderModule($typeOrId);
                break;
                // $id = intval($id);
                // $model = ModuleModel::findByPk($id);
                // if($model !== null) {
                //     $result = $this->getFrontendModule($model->id);
                // }
                // break;
            case 'content':
                $html = $this->renderContent($typeOrId);
                break;
            //     $id = intval($id);
            //     $model = ContentModel::findByPk($id);
            //     if($model !== null) {
            //         $result = $this->getContentElement($model->id);
            //     }
                break;
            // case 'article':
            //         $id = intval($id);
            //         $model = ArticleModel::findByPk($id);
            //         if($model !== null) {
            //             $result = $this->getArticle($model->id);
            //         }
            //         break;

            default:
                $html = null;
                break;
        }

        // $result = $this->replaceInsertTags($result, false);

        if ($html === null) {
            throw new NotFoundHttpException();
        }

        $html = $this->cleanHtml($html);

    	return new JsonResponse(['html' => $html]);
    }

    private function renderModule(int|string $typeOrId, array $data = []): ?string
    {
        $model = $this->getModel(ModuleModel::class, $typeOrId, $data);

        if ($model === null) {
            return null;
        }

        return $this->framework->getAdapter(Controller::class)->getFrontendModule($model);
    }

    private function renderContent(int|string $typeOrId, array $data = []): ?string
    {
        $model = $this->getModel(ContentModel::class, $typeOrId, $data);

        if ($model === null) {
            return null;
        }

        return $this->framework->getAdapter(Controller::class)->getContentElement($model);
    }

    private function getModel(string $class, int|string $typeOrId, array $data = []): ContentModel|ModuleModel|null
    {
        if (is_numeric($typeOrId)) {
            /** @var Adapter<ContentModel|ModuleModel> $adapter */
            $adapter = $this->framework->getAdapter($class);
            $model = $adapter->findById($typeOrId);
        } else {
            $model = $this->framework->createInstance($class);
            $model->type = $typeOrId;
        }

        foreach ($data as $k => $v) {
            if (null !== $v && !\is_scalar($v)) {
                $v = serialize($v);
            }

            $model->$k = $v;
        }

        $model?->preventSaving(false);

        return $model;
    }

    private function cleanHtml(string $html): string
    {
        $html = str_replace('<!-- indexer::stop -->', '', $html);
        $html = str_replace('<!-- indexer::continue -->', '', $html);

        return trim($html);
    }
}
