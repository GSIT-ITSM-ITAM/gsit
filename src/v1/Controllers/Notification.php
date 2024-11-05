<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;

final class Notification extends Common
{
  protected $model = '\App\Models\Notification';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Notification();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Notification();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Notification();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public static function prepareNotification($item, $event)
  {
    $item->refresh();
    $events = \App\Models\Definitions\Notification::getEvents();
    $notif = new self();

    // Search if event exists for this type of item
    $className = get_class($item);

    if (isset($events[$className]))
    {
      if (isset($events[$className][$event]))
      {
        // Search in DB if a notification exists
        $notifications = \App\Models\Notification::
            with('templates')
          ->where('item_type', $className)
          ->where('event', $event)
          ->get();
        foreach ($notifications as $notification)
        {
          // We create a notification and push it into the queuednotification
          foreach ($notification->templates as $template)
          {
            // Get translation
            $translation = \App\Models\Notificationtemplatetranslation::
                where('notificationtemplate_id', $template->id)
              ->first();
            if (!is_null($translation))
            {
              $queued = new \App\Models\Queuednotification();
              // $queued->entity_id
              $queued->item_type = 'MailCollector';
              $queued->notificationtemplate_id = $template->id;
              $queued->name = $notif->render($translation->subject, $item) . ' | ' . $translation->id;
              $queued->sender = 'xx@xx.xx';
              $queued->sendername = '';
              $queued->recipient = 'xx@xx.xx';
              $queued->recipientname = '';
              $queued->replyto = 'xx@xx.xx';
              $queued->replytoname = '';
              $queued->headers = '';
              $queued->body_html = $notif->render($translation->content_html, $item);
              $queued->body_text = $notif->render($translation->content_text, $item);
              $queued->mode = $template->pivot->mode;
              $queued->save();
            }
          }
        }
      }
    }
  }


  public function render($text, $item)
  {
    $loader = new \Twig\Loader\ArrayLoader([
      'notif.html' => $text,
    ]);
    $twig = new \Twig\Environment($loader, []);
    $template = $twig->load('notif.html');

    $data = $this->generateDataForNotification($item);
    // Manage lang
    $langData = $this->generateLangdataForNotification($item);
    $data['lang'] = $langData;

    $text = $template->render($data);
    return $text;
  }

  private function generateDataForNotification($item, $data = [], $fields = null)
  {
    $nestedFields = [];
    // Special case for nested, for example have field 'user'completename'
    if (!is_null($fields))
    {
      foreach ($fields as $field)
      {
        if (strstr($field, '.'))
        {
          $spl = explode('.', $field);
          $first = array_shift($spl);
          if (!isset($nestedFields[$first]))
          {
            $nestedFields[$first] = [];
          }
          $nestedFields[$first][] = implode('.', $spl);
        }
      }
    }
    $definitions = $item->getDefinitions(true);
    foreach ($definitions as $definition)
    {
      // Special case, for example have user.completename
      if (
          !is_null($fields) &&
          !in_array($definition['name'], $fields) &&
          !isset($nestedFields[$definition['name']])
      )
      {
        continue;
      }
      if (isset($definition['relationfields']))
      {
        $relationFields = [];
        if (isset($nestedFields[$definition['name']]))
        {
          $relationFields = $nestedFields[$definition['name']];
        } else {
          $relationFields = $definition['relationfields'];
        }

        // it's relationship
        if (isset($definition['dbname']))
        {
          // one to one/many relation
          if (is_null($item->{$definition['name']}))
          {
            $emptyItem = new $definition['itemtype']();
            $data[$definition['name']] = $this->generateDataForNotification(
              $emptyItem,
              [],
              $relationFields
            );
              // $data[$definition['name']] = [];
          } else {
            $data[$definition['name']] = $this->generateDataForNotification(
              $item->{$definition['name']},
              [],
              $relationFields
            );
          }
        } else {
          // many to may relation
          $data[$definition['name']] = [];
          foreach ($item->{$definition['name']}()->get() as $rItem)
          {
            $tempData = $this->generateDataForNotification($rItem, [], $relationFields);
            $data[$definition['name']][] = $tempData;
          }
        }
      } else {
        // it's fields
        if (is_null($item->{$definition['name']}))
        {
          $data[$definition['name']] = null;
        }
        elseif (isset($definition['values']))
        {
          if (empty($item->{$definition['name']}))
          {
            $data[$definition['name']] = null;
          } else {
            $data[$definition['name']] = $definition['values'][$item->{$definition['name']}]['title'];
          }
        }
        elseif ($definition['type'] == 'datetime' && is_object($item->{$definition['name']}))
        {
          $data[$definition['name']] = $item->{$definition['name']}->toDateTimeString();
        } else {
          $data[$definition['name']] = $item->{$definition['name']};
        }
      }
    }
    return $data;
  }

  private function generateLangdataForNotification($item, $data = [], $fields = null)
  {
    $nestedFields = [];
    // Special case for nested, for example have field 'user'completename'
    if (!is_null($fields))
    {
      foreach ($fields as $field)
      {
        if (strstr($field, '.'))
        {
          $spl = explode('.', $field);
          $first = array_shift($spl);
          if (!isset($nestedFields[$first]))
          {
            $nestedFields[$first] = [];
          }
          $nestedFields[$first][] = implode('.', $spl);
        }
      }
    }

    $definitions = $item->getDefinitions(true);
    foreach ($definitions as $definition)
    {
      // Special case, for example have user.completename
      if (
          !is_null($fields) &&
          !in_array($definition['name'], $fields) &&
          !isset($nestedFields[$definition['name']])
      )
      {
        continue;
      }

      if (isset($definition['relationfields']))
      {
        $relationFields = [];
        if (isset($nestedFields[$definition['name']]))
        {
          $relationFields = $nestedFields[$definition['name']];
        } else {
          $relationFields = $definition['relationfields'];
        }

        $emptyItem = new $definition['itemtype']();
        $data[$definition['name']] = $this->generateLangdataForNotification(
          $emptyItem,
          [],
          $relationFields
        );
      } else {
        $data[$definition['name']] = $definition['title'];
      }
    }
    return $data;
  }
}
