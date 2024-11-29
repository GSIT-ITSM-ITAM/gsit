<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Pdu extends Common
{
  protected $model = '\App\Models\Pdu';
  protected $rootUrl2 = '/pdus/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Pdu();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Pdu();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Pdu();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubPlugs(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/plugs');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myPlugs = [];
    foreach ($myItem->plugs as $current_plug)
    {
      $name = $current_plug->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/plugs/', $current_plug->id);

      $number_plugs = $current_plug->pivot->number_plugs;

      $myPlugs[] = [
        'name'            => $name,
        'url'             => $url,
        'number_plugs'    => $number_plugs,
      ];
    }

    // tri ordre alpha
    uasort($myPlugs, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('plugs', $myPlugs);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('number', $translator->translate('Number'));

    return $view->render($response, 'subitem/plugs.html.twig', (array)$viewData);
  }
}
