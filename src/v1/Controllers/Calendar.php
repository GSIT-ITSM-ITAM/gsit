<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Calendar extends Common
{
  protected $model = '\App\Models\Calendar';
  protected $rootUrl2 = '/dropdowns/calendars/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Calendar();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Calendar();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Calendar();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubTimeranges(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/timeranges');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myTimeranges = [];
    foreach ($myItem->timeranges as $timeranges)
    {
      $day = $timeranges->day;
      $name = $this->getDaysOfWeekArray()[$day];
      $begin = $timeranges->begin;
      $end = $timeranges->end;

      $myTimeranges[] = [
        'day'      => $day,
        'name'     => $name,
        'begin'    => $begin,
        'end'      => $end,
      ];
    }

    // tri ordre alpha
    uasort($myTimeranges, function ($a, $b)
    {
      if ($a['day'] == 0)
      {
        $a['day'] = 7;
      }
      if ($b['day'] == 0)
      {
        $b['day'] = 7;
      }

      return $a['day'] > $b['day'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('timeranges', $myTimeranges);

    $viewData->addTranslation('name', $translator->translatePlural('Day', 'Days', 1));
    $viewData->addTranslation('begin', $translator->translate('Start'));
    $viewData->addTranslation('end', $translator->translate('End'));

    return $view->render($response, 'subitem/timeranges.html.twig', (array)$viewData);
  }

  public function showSubHolidays(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/holidays');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myHolidays = [];
    foreach ($myItem->holidays as $holiday)
    {
      $name = $holiday->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/holidays/', $holiday->id);

      $begin = $holiday->begin_date;

      $end = $holiday->end_date;

      $recurrent = $holiday->recurrent;
      if ($recurrent == 1)
      {
        $recurrent_val = $translator->translate('Yes');
      }
      else
      {
        $recurrent_val = $translator->translate('No');
      }

      $myHolidays[] = [
        'name'            => $name,
        'url'             => $url,
        'begin'           => $begin,
        'end'             => $end,
        'recurrent'       => $recurrent,
        'recurrent_val'   => $recurrent_val,
      ];
    }

    // tri ordre alpha
    uasort($myHolidays, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('holidays', $myHolidays);

    $viewData->addTranslation('name', $translator->translatePlural('Day', 'Days', 1));
    $viewData->addTranslation('begin', $translator->translate('Start'));
    $viewData->addTranslation('end', $translator->translate('End'));
    $viewData->addTranslation('recurrent', $translator->translate('Recurrent'));

    return $view->render($response, 'subitem/holidays.html.twig', (array)$viewData);
  }
}
