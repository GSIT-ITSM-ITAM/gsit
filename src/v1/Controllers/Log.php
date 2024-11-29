<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;

final class Log extends Common
{
  public static function addEntry(
    $model,
    $message,
    $new_value,
    $old_value = null,
    $idSearchOption = 0,
    $set_by_rule = false
  )
  {
    $log = new \App\Models\Log();
    // $log->userid = $GLOBALS['user_id'];
    $user = \App\Models\User::find($GLOBALS['user_id']);
    // Store the name in case the user account deleted later
    if (!is_null($user))
    {
      $log->user_name = $user->completename . ' (' . $GLOBALS['user_id'] . ')';
    }
    if (is_object($model))
    {
      $log->item_type = get_class($model);
      $log->item_id = $model->id;
    }
    else
    {
      $log->item_type = $model;
      $log->item_id = 0;
    }

    if (is_object($old_value))
    {
      $log->old_value = json_encode(['id' => $old_value->item->id, 'name' => $old_value->item->name]);
    }
    else
    {
      $log->old_value = $old_value;
    }

    if (is_object($new_value))
    {
      $log->new_value = json_encode(['id' => $new_value->item->id, 'name' => $new_value->item->name]);
    }
    else
    {
      $log->new_value = $new_value;
    }
    $log->id_search_option = $idSearchOption;
    $log->save();
  }
}
