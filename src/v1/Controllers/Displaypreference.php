<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class Displaypreference extends Common
{
  protected $model = '\App\Models\Displaypreference';

  public function manageColumnsOfModel(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getQueryParams();
    $view = Twig::fromRequest($request);
    $type = 'user';

    if (!property_exists($data, 'm'))
    {
      throw new \Exception('Wrong request', 400);
    }
    $model = $data->m;
    if (!class_exists('\\' . $model))
    {
      throw new \Exception('Wrong request', 400);
    }

    $item = new $model();
    $definitions = $item->getDefinitions(true);
    $defIds = [];
    foreach ($definitions as $definition)
    {
      $defIds[$definition['id']] = $definition['title'];
    }

    $preferences = \App\Models\Displaypreference::
        where('itemtype', $model)
      ->where('user_id', $GLOBALS['user_id'])
      ->orderBy('rank', 'asc')
      ->get();
    if (count($preferences) == 0)
    {
      $type = 'global';
      $preferences = \App\Models\Displaypreference::
          where('itemtype', $model)
        ->where('user_id', 0)
        ->orderBy('rank', 'asc')
        ->get();
    }
    $columns = [];
    foreach ($preferences as $pref)
    {
      if (!isset($defIds[$pref->num]))
      {
        continue;
      }
      $columns[] = [
        'prefid' => $pref->num,
        'title'  => $defIds[$pref->num],
        'id'     => $pref->id,
      ];
      unset($defIds[$pref->num]);
    }

    $right = $this->canRightUpdate();
    if ($type == 'user')
    {
      $right = true;
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata(new \App\Models\Displaypreference(), $request);
    $viewData->addData('columns', $columns);
    $viewData->addData('definitions', $defIds);
    $viewData->addData('type', $type);
    $viewData->addData('dropdown', $defIds);
    $viewData->addData('canupdate', $right);
    $viewData->addData('m', $data->m);

    return $view->render($response, 'columns.html.twig', (array)$viewData);
  }

  public function postColumnOfModel(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();
    $datap = (object) $request->getQueryParams();

    if (property_exists($data, 'type') && property_exists($data, 'id') && property_exists($datap, 'm'))
    {
      $dpref = new \App\Models\Displaypreference();
      if ($data->type == 'user')
      {
        $dpref->user_id = $GLOBALS['user_id'];
      }
      else
      {
        $dpref->user_id = 0;
      }
      $dpref->itemtype = $datap->m;
      $dpref->num = $data->id;

      $dprefmax = \App\Models\Displaypreference::
          where('itemtype', $datap->m)
        ->where('user_id', $dpref->user_id)
        ->orderBy('rank', 'desc')
        ->first();
      if (is_null($dprefmax))
      {
        $dpref->rank = 1;
      }
      else
      {
        $dpref->rank = $dprefmax->rank + 1;
      }
      $dpref->save();
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  public function deleteColumn(Request $request, Response $response, $args): Response
  {
    $dpref = \App\Models\Displaypreference::find($args['id']);
    if (!is_null($dpref))
    {
      $dpref->delete();
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  public function viewUpColumn(Request $request, Response $response, $args): Response
  {
    $dpref = \App\Models\Displaypreference::find($args['id']);
    if (!is_null($dpref))
    {
      $this->upColumn($dpref);
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  public function viewDownColumn(Request $request, Response $response, $args): Response
  {
    $dpref = \App\Models\Displaypreference::find($args['id']);
    if (!is_null($dpref))
    {
      $this->downColumn($dpref);
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  public function viewCreateUserColumn(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getQueryParams();

    if (!property_exists($data, 'm'))
    {
      throw new \Exception('Wrong request', 400);
    }
    $model = $data->m;
    if (!class_exists('\\' . $model))
    {
      throw new \Exception('Wrong request', 400);
    }
    $items = \App\Models\Displaypreference::
        where('itemtype', $model)
      ->where('user_id', 0)
      ->orderBy('rank', 'asc')
      ->get();
    foreach ($items as $item)
    {
      \App\Models\Displaypreference::create([
        'itemtype' => $model,
        'num' => $item->num,
        'rank' => $item->rank,
        'user_id' => $GLOBALS['user_id'],
      ]);
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  public function viewDeleteUserColumn(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getQueryParams();

    if (!property_exists($data, 'm'))
    {
      throw new \Exception('Wrong request', 400);
    }
    $model = $data->m;
    if (!class_exists('\\' . $model))
    {
      throw new \Exception('Wrong request', 400);
    }

    $items = \App\Models\Displaypreference::
        where('itemtype', $model)
      ->where('user_id', $GLOBALS['user_id'])
      ->get();

    foreach ($items as $item)
    {
      $item->delete();
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  private function upColumn(\App\Models\Displaypreference $item)
  {
    $previous = \App\Models\Displaypreference::
        where('itemtype', $item->itemtype)
      ->where('rank', '<', $item->rank)
      ->where('user_id', $item->user_id)
      ->orderBy('rank', 'desc')
      ->first();

    $currentRank = $item->rank;
    $item->rank = $previous->rank;
    $item->save();
    $previous->rank = $currentRank;
    $previous->save();
  }

  private function downColumn(\App\Models\Displaypreference $item)
  {
    $next = \App\Models\Displaypreference::
        where('itemtype', $item->itemtype)
      ->where('rank', '>', $item->rank)
      ->where('user_id', $item->user_id)
      ->orderBy('rank', 'asc')
      ->first();

    $currentRank = $item->rank;
    $item->rank = $next->rank;
    $item->save();
    $next->rank = $currentRank;
    $next->save();
  }
}
