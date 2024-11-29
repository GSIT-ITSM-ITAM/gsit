<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Change extends Common
{
  protected $model = '\App\Models\Change';
  protected $rootUrl2 = '/changes/';
  protected $choose = 'changes';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Change();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Change();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Change();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showProblem(Request $request, Response $response, $args)
  {
    global $translator;
    $item = new $this->model();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\ChangeProblem();
    $myItem2 = $item2->where(['change_id' => $args['id']])->get();

    $rootUrl = $this->genereRootUrl($request, '/problem');

    $problems = [];
    foreach ($myItem2 as $problem)
    {
      $item3 = new \App\Models\Problem();
      $myItem3 = $item3->find($problem->problem_id);
      if ($myItem3 !== null)
      {
        $problems[] = [
          'id'          => $myItem3->id,
          'name'        => $myItem3->name,
          'updated_at'  => $myItem3->updated_at,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('feeds', $item->getFeeds($args['id']));
    $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    $viewData->addData('problems', $problems);

    $viewData->addTranslation('attachItem', $translator->translate('Attach to an existant problem'));
    $viewData->addTranslation('selectItem', $translator->translate('Select problem...'));
    $viewData->addTranslation('buttonAttach', $translator->translate('Attach'));
    $viewData->addTranslation('addItem', $translator->translate('Add new problem'));
    $viewData->addTranslation('buttonCreate', $translator->translate('Create'));
    $viewData->addTranslation('attachedItems', $translator->translate('Problems attached'));
    $viewData->addTranslation('updated', $translator->translate('Last update'));
    $viewData->addTranslation('or', $translator->translate('Ou'));

    return $view->render($response, 'subitem/problem.html.twig', (array) $viewData);
  }

  public function postProblem(Request $request, Response $response, $args)
  {
    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'problem') && is_numeric($data->problem))
    {
      $item = new $this->model();
      $myItem = $item::find($args['id']);
      $myItem->problems()->attach((int)$data->problem);

      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage("The ticket has been attached to problem successfully");
    }
    else
    {
      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage('Error to attache ticket to problem', 'error');
    }

    $uri = $request->getUri();
    header('Location: ' . (string) $uri);
    exit();
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

        $current_id = $myItem3->id;

        $name = $myItem3->name;
        if ($name == '')
        {
          $name = '(' . $current_id . ')';
        }

        $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $current_id);

        $entity = '';
        $entity_url = '';
        if ($myItem3->entity !== null)
        {
          $entity = $myItem3->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
        }

        $serial_number = $myItem3->serial;

        $inventaire_number = $myItem3->otherserial;

        $myItems[] = [
          'type'                 => $type_fr,
          'name'                 => $name,
          'url'                  => $url,
          'entity'               => $entity,
          'entity_url'           => $entity_url,
          'serial_number'        => $serial_number,
          'inventaire_number'    => $inventaire_number,
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

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }

  public function showAnalysis(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/analysis');

    $myAnalysis = [];
    $myAnalysis = [
      'impactcontent'       => $myItem->impactcontent,
      'controlistcontent'   => $myItem->controlistcontent,
    ];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDefs = $item->getSpecificFunction('getDefinitionAnalysis');
    $myItemData = [
      'impactcontent'       => $myAnalysis['impactcontent'],
      'controlistcontent'   => $myAnalysis['controlistcontent'],
    ];
    $myItemDataObject = json_decode(json_encode($myItemData));

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));

    $viewData->addTranslation('impactcontent', $translator->translate('Impacts'));
    $viewData->addTranslation('controlistcontent', $translator->translate('Control list'));

    return $view->render($response, 'subitem/analysis.html.twig', (array)$viewData);
  }

  public function showPlans(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/plans');

    $myPlans = [];
    $myPlans = [
      'rolloutplancontent'  => $myItem->rolloutplancontent,
      'backoutplancontent'  => $myItem->backoutplancontent,
      'checklistcontent'    => $myItem->checklistcontent,
    ];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDefs = $item->getSpecificFunction('getDefinitionPlans');
    $myItemData = [
      'rolloutplancontent'  => $myPlans['rolloutplancontent'],
      'backoutplancontent'  => $myPlans['backoutplancontent'],
      'checklistcontent'    => $myPlans['checklistcontent'],
    ];
    $myItemDataObject = json_decode(json_encode($myItemData));

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));
    $viewData->addData('show', $this->choose);
    $viewData->addData('plans', $myPlans);

    $viewData->addTranslation('rolloutplancontent', $translator->translate('Deployment plan'));
    $viewData->addTranslation('backoutplancontent', $translator->translate('Backup plan'));
    $viewData->addTranslation('checklistcontent', $translator->translate('Checklist'));

    return $view->render($response, 'subitem/plans.html.twig', (array)$viewData);
  }
}
