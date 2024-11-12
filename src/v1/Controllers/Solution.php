<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;

final class Solution extends Common
{
  protected $model = '\App\Models\Solution';

  public function postItem(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();
    // TODO if not have $data->followup, error


    $item = new \App\Models\Solution();
    $item->item_type = $data->item_type;
    $item->item_id = $data->item_id;
    $item->content = $data->solution;
    $item->user_id = $GLOBALS['user_id'];
    $item->status = 2;

    $item->save();

    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage('The solution has been added successfully');

    header('Location: ' . $data->redirect);
    exit();
  }

  public function postAccept(Request $request, Response $response, $args): Response
  {
    $item = \App\Models\Solution::find($args['solutionid']);
    $item->status = 3;
    $item->date_approval = date('Y-m-d H:i:s');
    $item->user_id_approval = $GLOBALS['user_id'];
    $item->user_name_approval = $GLOBALS['username'];
    $item->save();

    if ($item->item_type == 'App\Models\Ticket')
    {
      $ticket = \App\Models\Ticket::find($item->item_id);
      $ticket->status = 6;
      $ticket->save();
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }

  public function postRefuse(Request $request, Response $response, $args): Response
  {
    $item = \App\Models\Solution::find($args['solutionid']);
    $item->status = 4;
    $item->date_approval = date('Y-m-d H:i:s');
    $item->user_id_approval = $GLOBALS['user_id'];
    $item->user_name_approval = $GLOBALS['username'];
    $item->save();

    if ($item->item_type == 'App\Models\Ticket')
    {
      $ticket = \App\Models\Ticket::find($item->item_id);
      $ticket->status = 2;
      $ticket->save();
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
  }
}
