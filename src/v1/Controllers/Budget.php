<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Budget extends Common
{
  protected $model = '\App\Models\Budget';
  protected $rootUrl2 = '/budgets/';
  protected $choose = 'budgets';
  protected $associateditems_model = '\App\Models\Infocom';
  protected $associateditems_model_id = 'budget_id';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Budget();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Budget();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Budget();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubBudgetMain(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->associateditems_model();
    $myItem2 = $item2::where($this->associateditems_model_id, $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/budgetmain');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myBudgetMain = [];
    $myBudgetMainType = [];
    $total_spent = 0;
    $total_remaining = 0;
    $total_budget = $myItem->value;
    foreach ($myItem2 as $current_attacheditem)
    {
      $item3 = new $current_attacheditem->item_type();
      $myItem3 = $item3->find($current_attacheditem->item_id);
      if ($myItem3 !== null)
      {
        if ($myItem3->entity !== null)
        {
          $entity_id = $myItem3->entity->id;
          $entity = $myItem3->entity->completename;
          $entity_url = '';
          if (array_key_exists($entity_id, $myBudgetMain) !== true)
          {
            $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);

            $myBudgetMain[$entity_id] = [
              'name'        => $entity,
              'entity_url'  => $entity_url,
              'items'       => [],
              'total'       => 0,
            ];
          }

          $type_fr = $item3->getTitle();
          $type = $item3->getTable();

          if (array_key_exists($type, $myBudgetMainType) !== true)
          {
            $myBudgetMainType[$type] = [
              'name'  => $type_fr,
              'total' => 0,
            ];
          }

          if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
          {
            $myBudgetMain[$entity_id]['items'][$type] = [
              'total' => 0,
            ];
          }

          $value = $this->showCosts($current_attacheditem->value);

          $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
          $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

          $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
          $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
        }
      }
    }

    $item2 = new \App\Models\Contractcost();
    $myItem2 = $item2::where('budget_id', $args['id'])->get();
    foreach ($myItem2 as $current_attacheditem)
    {
      $item3 = new \App\Models\Contract();
      $myItem3 = $item3->find($current_attacheditem->contract_id);
      if ($myItem3 !== null)
      {
        $entity_id = $myItem3->entity->id;
        $entity = $myItem3->entity->completename;
        if (array_key_exists($entity_id, $myBudgetMain) !== true)
        {
          $myBudgetMain[$entity_id] = [
            'name'    => $entity,
            'items'   => [],
            'total'   => 0,
          ];
        }

        $type_fr = $translator->translatePlural('Contract', 'Contract', 1);
        $type = 'contracts';

        if (array_key_exists($type, $myBudgetMainType) !== true)
        {
          $myBudgetMainType[$type] = [
            'name'  => $type_fr,
            'total' => 0,
          ];
        }

        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }

        $value = $this->showCosts($current_attacheditem->cost);

        $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
        $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

        $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
        $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
      }
    }

    $item2 = new \App\Models\Ticketcost();
    $myItem2 = $item2::where('budget_id', $args['id'])->get();
    foreach ($myItem2 as $current_attacheditem)
    {
      $item3 = new \App\Models\Ticket();
      $myItem3 = $item3->find($current_attacheditem->ticket_id);
      if ($myItem3 !== null)
      {
        $entity_id = $myItem3->entity->id;
        $entity = $myItem3->entity->completename;
        if (array_key_exists($entity_id, $myBudgetMain) !== true)
        {
          $myBudgetMain[$entity_id] = [
            'name'    => $entity,
            'items'   => [],
            'total'   => 0,
          ];
        }

        $type_fr = $translator->translatePlural('Ticket', 'Tickets', 1);
        $type = 'tickets';

        if (array_key_exists($type, $myBudgetMainType) !== true)
        {
          $myBudgetMainType[$type] = [
            'name'  => $type_fr,
            'total' => 0,
          ];
        }

        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }

        $value = $this->computeTotalCost(
          $current_attacheditem->actiontime,
          $current_attacheditem->cost_time,
          $current_attacheditem->cost_fixed,
          $current_attacheditem->cost_material
        );

        $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
        $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

        $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
        $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
      }
    }

    $item2 = new \App\Models\Problemcost();
    $myItem2 = $item2::where('budget_id', $args['id'])->get();
    foreach ($myItem2 as $current_attacheditem)
    {
      $item3 = new \App\Models\Problem();
      $myItem3 = $item3->find($current_attacheditem->problem_id);
      if ($myItem3 !== null)
      {
        $entity_id = $myItem3->entity->id;
        $entity = $myItem3->entity->completename;
        if (array_key_exists($entity_id, $myBudgetMain) !== true)
        {
          $myBudgetMain[$entity_id] = [
            'name'    => $entity,
            'items'   => [],
            'total'   => 0,
          ];
        }

        $type_fr = $translator->translatePlural('Problem', 'Problems', 1);
        $type = 'problems';

        if (array_key_exists($type, $myBudgetMainType) !== true)
        {
          $myBudgetMainType[$type] = [
            'name'  => $type_fr,
            'total' => 0,
          ];
        }

        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }

        $value = $this->computeTotalCost(
          $current_attacheditem->actiontime,
          $current_attacheditem->cost_time,
          $current_attacheditem->cost_fixed,
          $current_attacheditem->cost_material
        );

        $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
        $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

        $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
        $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
      }
    }

    $item2 = new \App\Models\Changecost();
    $myItem2 = $item2::where('budget_id', $args['id'])->get();
    foreach ($myItem2 as $current_attacheditem)
    {
      $item3 = new \App\Models\Change();
      $myItem3 = $item3->find($current_attacheditem->change_id);
      if ($myItem3 !== null)
      {
        $entity_id = $myItem3->entity->id;
        $entity = $myItem3->entity->completename;
        if (array_key_exists($entity_id, $myBudgetMain) !== true)
        {
          $myBudgetMain[$entity_id] = [
            'name'    => $entity,
            'items'   => [],
            'total'   => 0,
          ];
        }

        $type_fr = $translator->translatePlural('Change', 'Changes', 1);
        $type = 'changes';

        if (array_key_exists($type, $myBudgetMainType) !== true)
        {
          $myBudgetMainType[$type] = [
            'name'  => $type_fr,
            'total' => 0,
          ];
        }

        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }

        $value = $this->computeTotalCost(
          $current_attacheditem->actiontime,
          $current_attacheditem->cost_time,
          $current_attacheditem->cost_fixed,
          $current_attacheditem->cost_material
        );

        $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
        $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

        $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
        $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
      }
    }

    $item2 = new \App\Models\Projectcost();
    $myItem2 = $item2::where('budget_id', $args['id'])->get();
    foreach ($myItem2 as $current_attacheditem)
    {
      $item3 = new \App\Models\Project();
      $myItem3 = $item3->find($current_attacheditem->project_id);
      if ($myItem3 !== null)
      {
        $entity_id = $myItem3->entity->id;
        $entity = $myItem3->entity->completename;
        if (array_key_exists($entity_id, $myBudgetMain) !== true)
        {
          $myBudgetMain[$entity_id] = [
            'name'    => $entity,
            'items'   => [],
            'total'   => 0,
          ];
        }

        $type_fr = $translator->translatePlural('Project', 'Projects', 1);
        $type = 'projects';

        if (array_key_exists($type, $myBudgetMainType) !== true)
        {
          $myBudgetMainType[$type] = [
            'name'  => $type_fr,
            'total' => 0,
          ];
        }

        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }

        $value = $this->showCosts($current_attacheditem->cost);

        $myBudgetMain[$entity_id]['total'] = $myBudgetMain[$entity_id]['total'] + $value;
        $myBudgetMainType[$type]['total'] = $myBudgetMainType[$type]['total'] + $value;

        $itemtypetotal = $myBudgetMain[$entity_id]['items'][$type]['total'] + $value;
        $myBudgetMain[$entity_id]['items'][$type]['total'] = $itemtypetotal;
      }
    }

    // tri par ordre alpha
    uasort($myBudgetMainType, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    foreach (array_keys($myBudgetMainType) as $type)
    {
      foreach (array_keys($myBudgetMain) as $entity_id)
      {
        if (array_key_exists($type, $myBudgetMain[$entity_id]['items']) !== true)
        {
          $myBudgetMain[$entity_id]['items'][$type] = [
            'total' => 0,
          ];
        }
        else
        {
          $show_cost = $myBudgetMain[$entity_id]['items'][$type]['total'];
          $myBudgetMain[$entity_id]['items'][$type]['total'] = $this->showCosts($show_cost);
        }
      }

      $myBudgetMainType[$type]['total'] = $this->showCosts($myBudgetMainType[$type]['total']);

      $total_spent = $total_spent + $myBudgetMainType[$type]['total'];

      if (stristr($type, 'consumable'))
      {
        $myBudgetMainType[$type]['name'] = $myBudgetMainType[$type]['name'] . ' (' . $type . ')';
      }
    }

    foreach (array_keys($myBudgetMain) as $entity_id)
    {
      $myBudgetMain[$entity_id]['total'] = $this->showCosts($myBudgetMain[$entity_id]['total']);
    }

    $total_remaining = $total_budget - $total_spent;
    $alert_budget = false;
    if ($total_remaining < 0)
    {
      $alert_budget = true;
    }

    $total_spent = $this->showCosts($total_spent);
    $total_remaining = $this->showCosts($total_remaining);

    $colspan = count($myBudgetMainType) + 1;

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('budgetmain', $myBudgetMain);
    $viewData->addData('budgetmaintype', $myBudgetMainType);
    $viewData->addData('total_spent', $total_spent);
    $viewData->addData('total_remaining', $total_remaining);
    $viewData->addData('alert_budget', $alert_budget);
    $viewData->addData('colspan', $colspan);

    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('total', $translator->translate('Total'));
    $viewData->addTranslation('total_spent', $translator->translate('Total spent on the budget'));
    $viewData->addTranslation('total_remaining', $translator->translate('Total remaining on the budget'));

    return $view->render($response, 'subitem/budgetmain.html.twig', (array)$viewData);
  }
}
