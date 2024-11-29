<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Consumableitem extends Common
{
  protected $model = '\App\Models\Consumableitem';
  protected $rootUrl2 = '/consumableitems/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Consumableitem();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Consumableitem();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Consumableitem();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubConsumables(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('consumables')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/consumables');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myConsumables_new = [];
    $myConsumables_use = [];
    $total = 0;
    $total_new = 0;
    $total_use = 0;
    foreach ($myItem->consumables as $consumable)
    {
      $status = '';

      $date_in = $consumable->date_in;

      $url = '';

      $date_out = $consumable->date_out;
      if ($date_out !== null)
      {
        $status = $translator->translatePlural('consumable' . "\004" . 'Used', 'consumable' . "\004" . 'Used', 1);
        $total_use = $total_use + 1;
      }
      else
      {
        $status = $translator->translatePlural('consumable' . "\004" . 'New', 'consumable' . "\004" . 'New', 1);
        $total_new = $total_new + 1;
      }

      $given_to = '';
      if (($consumable->item_type != '') && ($consumable->item_id != 0))
      {
        $item3 = new $consumable->item_type();
        $myItem3 = $item3->find($consumable->item_id);
        if ($myItem3 !== null)
        {
          $type = $item3->getTable();

          $given_to = $myItem3->name;

          $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);
        }
      }

      $total = $total + 1;

      if ($consumable->date_out == null)
      {
        $myConsumables_new[] = [
          'status'       => $status,
          'date_in'      => $date_in,
        ];
      }
      else
      {
        $myConsumables_use[] = [
          'status'       => $status,
          'url'          => $url,
          'date_in'      => $date_in,
          'date_out'     => $date_out,
          'given_to'     => $given_to,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('consumables_new', $myConsumables_new);
    $viewData->addData('consumables_use', $myConsumables_use);
    $viewData->addData('total', $total);
    $viewData->addData('total_new', $total_new);
    $viewData->addData('total_use', $total_use);

    $viewData->addTranslation('status', $translator->translate('item' . "\004" . 'State'));
    $viewData->addTranslation('date_in', $translator->translate('Add date'));
    $viewData->addTranslation('date_out', $translator->translate('Use date'));
    $viewData->addTranslation('given_to', $translator->translate('Given to'));
    $viewData->addTranslation('no_consumable', $translator->translate('No consumable'));
    $viewData->addTranslation('consumables_use', $translator->translate('Used consumables'));
    $viewData->addTranslation('total', $translator->translate('Total'));
    $viewData->addTranslation(
      'total_new',
      $translator->translatePlural('consumable' . "\004" . 'New', 'consumable' . "\004" . 'New', 1)
    );
    $viewData->addTranslation(
      'total_use',
      $translator->translatePlural('consumable' . "\004" . 'Used', 'consumable' . "\004" . 'Used', 1)
    );

    return $view->render($response, 'subitem/consumables.html.twig', (array)$viewData);
  }
}
