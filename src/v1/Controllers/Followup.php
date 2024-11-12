<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Followup extends Common
{
  protected $model = '\App\Models\Followup';

  public function postItem(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();
    // TODO if not have $data->followup, error


    $item = new \App\Models\Followup();
    $item->item_type = $data->item_type;
    $item->item_id = $data->item_id;
    $item->content = $data->followup;
    // $item->is_private = $data->is_private;
    $item->user_id = $GLOBALS['user_id'];

    $item->save();

    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage('The followup has been added successfully');

    header('Location: ' . $data->redirect);
    exit();
  }
}
