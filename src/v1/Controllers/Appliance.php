<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Appliance extends Common
{
  protected $model = '\App\Models\Appliance';
  protected $rootUrl2 = '/appliances/';
  protected $choose = 'appliances';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Appliance();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Appliance();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Appliance();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubItems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Applianceitem();
    $myItem2 = $item2::where('appliance_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/items');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myItems = [];
    foreach ($myItem2 as $appliance_item)
    {
      $item3 = new $appliance_item->item_type();
      $myItem3 = $item3->find($appliance_item->item_id);
      if ($myItem3 !== null)
      {
        $type_fr = $item3->getTitle();
        $type = $item3->getTable();

        $name = $myItem3->name;
        if ($name == '')
        {
          $name = '(' . $myItem3->id . ')';
        }

        $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

        $serial_number = $myItem3->serial;

        $inventaire_number = $myItem3->otherserial;

        $relations = [];
        $item4 = new \App\Models\Applianceitemrelation();
        $myItem4 = $item4::where('appliance_item_id', $appliance_item->id)->get();
        foreach ($myItem4 as $appliance_item_relation)
        {
          $item5 = new $appliance_item_relation->item_type();
          $myItem5 = $item5->find($appliance_item_relation->item_id);
          if ($myItem5 !== null)
          {
            $relation_type_fr = $item5->getTitle();
            $relation_type = $item5->getTable();

            $relation_name = $myItem5->name;
            if ($relation_name == '')
            {
              $relation_name = '(' . $myItem5->id . ')';
            }

            $relation_url = $this->genereRootUrl2Link($rootUrl2, '/' . $relation_type . '/', $myItem5->id);

            $relations[] = [
              'type'        => $relation_type_fr,
              'name'        => $relation_name,
              'url'         => $relation_url,
            ];
          }
        }

        // tri ordre alpha
        uasort($relations, function ($a, $b)
        {
          return strtolower($a['name']) > strtolower($b['name']);
        });
        uasort($relations, function ($a, $b)
        {
          return strtolower($a['type']) > strtolower($b['type']);
        });

        $myItems[] = [
          'type'                 => $type_fr,
          'name'                 => $name,
          'url'                  => $url,
          'serial_number'        => $serial_number,
          'inventaire_number'    => $inventaire_number,
          'relations'            => $relations,
        ];
      }
    }

    // tri ordre alpha
    uasort($myItems, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });
    uasort($myItems, function ($a, $b)
    {
      return strtolower($a['type']) > strtolower($b['type']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('items', $myItems);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('type', $translator->translate('Item type'));
    $viewData->addTranslation('name', $translator->translatePlural('Item', 'Items', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));
    $viewData->addTranslation(
      'relations',
      $translator->translatePlural('appliance' . "\004" . 'Relation', 'appliance' . "\004" . 'Relations', 2)
    );

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }
}
