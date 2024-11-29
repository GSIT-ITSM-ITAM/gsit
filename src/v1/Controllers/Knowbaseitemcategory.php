<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Knowbaseitemcategory extends Common
{
  protected $model = '\App\Models\Knowbaseitemcategory';
  protected $rootUrl2 = '/dropdowns/knowbaseitemcategories/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Knowbaseitemcategory();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Knowbaseitemcategory();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Knowbaseitemcategory();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubKnowbaseitemcategories(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->model();
    $myItem2 = $item2::where('knowbaseitemcategory_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/knowbaseitemcategories');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myBusinesscriticities = [];
    foreach ($myItem2 as $knowbaseitemcategory)
    {
      $name = $knowbaseitemcategory->name;
      if ($name == '')
      {
        $name = '(' . $knowbaseitemcategory->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/knowbaseitemcategories/', $knowbaseitemcategory->id);

      $entity = '';
      $entity_url = '';
      if ($knowbaseitemcategory->entity !== null)
      {
        $entity = $knowbaseitemcategory->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $knowbaseitemcategory->entity->id);
      }

      $comment = $knowbaseitemcategory->comment;

      $myBusinesscriticities[] = [
        'name'         => $name,
        'url'          => $url,
        'entity'       => $entity,
        'entity_url'   => $entity_url,
        'comment'      => $comment,
      ];
    }

    // tri ordre alpha
    uasort($myBusinesscriticities, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('knowbaseitemcategories', $myBusinesscriticities);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/knowbaseitemcategories.html.twig', (array)$viewData);
  }
}
