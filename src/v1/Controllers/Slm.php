<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Slm extends Common
{
  protected $model = '\App\Models\Slm';
  protected $rootUrl2 = '/slms/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Slm();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Slm();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Slm();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubSlas(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/slas');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $mySlas = [];
    foreach ($myItem->slas as $current_sla)
    {
      $name = $current_sla->name;

      $url = '';
      // $url = $this->genereRootUrl2Link($rootUrl2, '/slas/', $current_sla->id);     // TODO

      $type = $this->getTtrTto()[$current_sla->type];

      $max_duration = $this->getValueWithUnit($current_sla->number_time, $current_sla->definition_time, 0);

      $calendar_name = '';
      $calendar_url = '';
      if ($myItem->calendar !== null)
      {
        $calendar_name = $myItem->calendar->name;
        $calendar_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/calendars/', $myItem->calendar->id);
      }

      $mySlas[] = [
        'name'                => $name,
        'url'                 => $url,
        'type'                => $type,
        'max_duration'        => $max_duration,
        'calendar_name'       => $calendar_name,
        'calendar_url'        => $calendar_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('slaola', $mySlas);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('max_duration', $translator->translate('Maximum time'));
    $viewData->addTranslation('calendar', $translator->translatePlural('Calendar', 'Calendars', 1));

    return $view->render($response, 'subitem/slaola.html.twig', (array)$viewData);
  }

  public function showSubOlas(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/olas');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myOlas = [];
    foreach ($myItem->olas as $current_ola)
    {
      $name = $current_ola->name;

      $url = '';
      // $url = $this->genereRootUrl2Link($rootUrl2, '/olas/', $current_ola->id);   // TODO

      $type = $this->getTtrTto()[$current_ola->type];

      $max_duration = $this->getValueWithUnit($current_ola->number_time, $current_ola->definition_time, 0);

      $calendar_name = '';
      $calendar_url = '';
      if ($myItem->calendar !== null)
      {
        $calendar_name = $myItem->calendar->name;
        $calendar_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/calendars/', $myItem->calendar->id);
      }

      $myOlas[] = [
        'name'                => $name,
        'url'                 => $url,
        'type'                => $type,
        'max_duration'        => $max_duration,
        'calendar_name'       => $calendar_name,
        'calendar_url'        => $calendar_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('slaola', $myOlas);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('max_duration', $translator->translate('Maximum time'));
    $viewData->addTranslation('calendar', $translator->translatePlural('Calendar', 'Calendars', 1));

    return $view->render($response, 'subitem/slaola.html.twig', (array)$viewData);
  }

  public function getTtrTto()
  {
    global $translator;

    return [
      $this->TTR => [
        'title' => $translator->translate('Time to resolve'),
      ],
      $this->TTO => [
        'title' => $translator->translate('Time to own'),
      ],
    ];
  }
}
