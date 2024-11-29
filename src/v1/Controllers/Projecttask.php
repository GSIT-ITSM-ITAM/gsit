<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Projecttask extends Common
{
  protected $model = '\App\Models\Projecttask';
  protected $rootUrl2 = '/projecttasks/';
  protected $choose = 'projecttasks';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Projecttask();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Projecttask();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Projecttask();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubProjecttasks(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Projecttask();
    $myItem2 = $item2::where('projecttask_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/projecttasks');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjecttasks = [];
    foreach ($myItem2 as $current_task)
    {
      $name = $current_task->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/projecttasks/', $current_task->id);

      $type = '';
      $type_url = '';
      if ($current_task->type !== null)
      {
        $type = $current_task->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/projecttasktypes/', $current_task->type->id);
      }

      $status = '';
      $status_color = '';
      if ($current_task->state !== null)
      {
        $status = $current_task->state->name;
        $status_color = $current_task->state->color;
      }

      $percent_done = $current_task->percent_done;
      $percent_done = $this->getValueWithUnit($percent_done, '%', 0);

      $planned_start_date = $current_task->plan_start_date;

      $planned_end_date = $current_task->plan_end_date;

      $planned_duration = $this->timestampToString($current_task->planned_duration, false);

      $effective_duration = $this->timestampToString($current_task->effective_duration, false);

      $father = '';
      $father_url = '';
      if ($current_task->parent !== null)
      {
        $father = $current_task->parent->name;
        $father_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/projecttasks/', $current_task->parent->id);
      }

      $myProjecttasks[] = [
        'name'                  => $name,
        'url'                   => $url,
        'type'                  => $type,
        'type_url'              => $type_url,
        'status'                => $status,
        'status_color'          => $status_color,
        'percent_done'          => $percent_done,
        'planned_start_date'    => $planned_start_date,
        'planned_end_date'      => $planned_end_date,
        'planned_duration'      => $planned_duration,
        'effective_duration'    => $effective_duration,
        'father'                => $father,
        'father_url'            => $father_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('projecttasks', $myProjecttasks);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('percent_done', $translator->translate('Percent done'));
    $viewData->addTranslation('planned_start_date', $translator->translate('Planned start date'));
    $viewData->addTranslation('planned_end_date', $translator->translate('Planned end date'));
    $viewData->addTranslation('planned_duration', $translator->translate('Planned duration'));
    $viewData->addTranslation('effective_duration', $translator->translate('Effective duration'));
    $viewData->addTranslation('father', $translator->translate('Father'));

    return $view->render($response, 'subitem/projecttasks.html.twig', (array)$viewData);
  }

  public function showSubProjecttaskteams(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Projecttaskteam();
    $myItem2 = $item2::where('projecttask_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/projecttaskteams');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjecttaskteams = [];
    foreach ($myItem2 as $current_item)
    {
      $item3 = new $current_item->item_type();
      $myItem3 = $item3->find($current_item->item_id);

      if ($myItem3 !== null)
      {
        $type_fr = $item3->getTitle();
        $type = $item3->getTable();

        $name = $myItem3->name;
        if (isset($myItem3->firstname))
        {
          $name = $name . ' ' . $myItem3->firstname;
        }
        if ($name == '')
        {
          $name = '(' . $myItem3->id . ')';
        }

        $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

        $myProjecttaskteams[] = [
          'member'   => $name,
          'url'      => $url,
          'type'     => $type_fr,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('projecttaskteams', $myProjecttaskteams);

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('member', $translator->translatePlural('Member', 'Members', 2));

    return $view->render($response, 'subitem/projecttaskteams.html.twig', (array)$viewData);
  }
}
