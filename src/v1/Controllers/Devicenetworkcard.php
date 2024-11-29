<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Devicenetworkcard extends Common
{
  protected $model = '\App\Models\Devicenetworkcard';
  protected $rootUrl2 = '/devices/devicenetworkcards/';
  protected $choose = 'devicenetworkcards';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicenetworkcard();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicenetworkcard();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicenetworkcard();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubItems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/items');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myItems = [];
    foreach ($myItem->items as $current_item)
    {
      $item3 = new $current_item->item_type();
      $myItem3 = $item3->find($current_item->item_id);
      if ($myItem3 !== null)
      {
        $type_fr = $item3->getTitle();
        $type = $item3->getTable();

        if (array_key_exists($type, $myItems) !== true)
        {
          $myItems[$type] = [
            'type'  => $type,
            'name'  => $type_fr,
            'items' => [],
          ];
        }

        $current_id = $myItem3->id;

        $name = $myItem3->name;
        if ($name == '')
        {
          $name = '(' . $current_id . ')';
        }

        $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $current_id);

        $location = '';
        $location_url = '';
        if ($myItem3->location !== null)
        {
          $location = $myItem3->location->name;
          $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $myItem3->location->id);
        }

        $documents = [];
        if ($myItem3->documents !== null)
        {
          foreach ($myItem3->documents as $document)
          {
            $url_document = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

            $documents[$document->id] = [
              'name'  => $document->name,
              'url'   => $url_document,
            ];
          }
        }

        $mac = $current_item->mac;

        $myItems[$type]['items'][$current_id][$current_item->id] = [
          'name'             => $name,
          'url'              => $url,
          'location'         => $location,
          'location_url'     => $location_url,
          'documents'        => $documents,
          'mac'              => $mac,
        ];
      }
    }

    // tri ordre alpha
    uasort($myItems, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('items', $myItems);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('location', $translator->translatePlural('Location', 'Locations', 2));
    $viewData->addTranslation('documents', $translator->translatePlural('Document', 'Documents', 2));
    $viewData->addTranslation('mac', $translator->translate('MAC address'));

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }
}
