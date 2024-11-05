<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Crontask extends Common
{
  protected $model = '\App\Models\Crontask';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Crontask();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Crontask();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Crontask();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }


  public function showSubExecutions(Request $request, Response $response, $args): Response
  {
    $myItem = \App\Models\Crontask::with('crontaskexecutions')->find($args['id']);
    $view = Twig::fromRequest($request);
    $rootUrl = $this->getUrlWithoutQuery($request);

    $dataExecutions = [];

    foreach ($myItem->crontaskexecutions as $execution)
    {
      $state = '';
      if ($execution->state == \App\Models\Crontaskexecution::STATE_RUN)
      {
        $state = '<i class="hourglass start blue icon"></i>Running';
      }
      if ($execution->state == \App\Models\Crontaskexecution::STATE_START)
      {
        $state = '<i class="play blue icon"></i>Starting';
      }
      if ($execution->state == \App\Models\Crontaskexecution::STATE_STOP)
      {
        $state = '<i class="check circle outline green icon"></i>Ended';
      }
      if ($execution->state == \App\Models\Crontaskexecution::STATE_ERROR)
      {
        $state = '<i class="times circle outline red icon"></i>In error';
      }

      $dataExecutions[] = [
        'state'       => $state,
        'created_at'  => $execution->created_at,
        'duration'    => $execution->execution_duration,
        'link'        => $rootUrl . '/' . $execution->id,
      ];
    }

    $rootUrl = rtrim($rootUrl, '/executions');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($myItem->getRelatedPages($rootUrl));

    $viewData->addData('executions', $dataExecutions);

    return $view->render($response, 'subitem/crontaskexecutions.html.twig', (array)$viewData);
  }

  public function showSubExecutionlogs(Request $request, Response $response, $args): Response
  {
    $myItem = \App\Models\Crontask::with('crontaskexecutions')->find($args['id']);
    $view = Twig::fromRequest($request);
    $rootUrl = $this->getUrlWithoutQuery($request);

    $myLogs = [];
    $logs = \App\Models\Crontaskexecutionlog::where('crontaskexecution_id', $args['executionid'])->get();
    foreach ($logs as $log)
    {
      $myLogs[] = [
        'date'    => $log->created_at,
        'volume'  => $log->volume,
        'content' => $log->content,
      ];
    }

    $rootUrl = rtrim($rootUrl, '/' . $args['executionid']);
    $rootUrl = rtrim($rootUrl, '/executions');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($myItem->getRelatedPages($rootUrl));

    $viewData->addData('logs', $myLogs);

    return $view->render($response, 'subitem/crontaskexecutionlogs.html.twig', (array)$viewData);
  }
}
