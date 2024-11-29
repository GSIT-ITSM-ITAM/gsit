<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Datacenter extends Common
{
  protected $model = '\App\Models\Datacenter';
  protected $rootUrl2 = '/datacenters/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Datacenter();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Datacenter();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Datacenter();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubDcrooms(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/dcrooms');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDcrooms = [];
    foreach ($myItem->dcrooms as $current_dcroom)
    {
      $name = $current_dcroom->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dcrooms/', $current_dcroom->id);

      $myDcrooms[] = [
        'name'       => $name,
        'url'        => $url,
      ];
    }

    // tri ordre alpha
    uasort($myDcrooms, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('dcrooms', $myDcrooms);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/dcrooms.html.twig', (array)$viewData);
  }
}
