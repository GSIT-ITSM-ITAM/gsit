<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Link extends Common
{
  protected $model = '\App\Models\Link';
  protected $rootUrl2 = '/links/';
  protected $choose = 'links';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Link();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Link();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Link();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubAssociatedItemType(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\LinkItemtype();
    $myItem2 = $item2::where('link_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/associateditemtypes');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myAssociatedItemType = [];
    foreach ($myItem2 as $current_item)
    {
      $item3 = new $current_item->item_type();
      $type = $item3->getTitle();

      $myAssociatedItemType[$type] = [
        'type'    => $type,
      ];
    }

    // tri ordre alpha
    uasort($myAssociatedItemType, function ($a, $b)
    {
      return strtolower($a['type']) > strtolower($b['type']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('items', $myAssociatedItemType);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }
}
