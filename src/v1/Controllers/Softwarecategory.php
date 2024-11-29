<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Softwarecategory extends Common
{
  protected $model = '\App\Models\Softwarecategory';
  protected $rootUrl2 = '/dropdowns/softwarecategories/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwarecategory();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwarecategory();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwarecategory();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubSoftwarecategories(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->model();
    $myItem2 = $item2::where('softwarecategory_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/softwarecategories');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $mySoftwarecategories = [];
    foreach ($myItem2 as $softwarecategory)
    {
      $name = $softwarecategory->name;
      if ($name == '')
      {
        $name = '(' . $softwarecategory->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/softwarecategories/', $softwarecategory->id);

      $comment = $softwarecategory->comment;

      $mySoftwarecategories[] = [
        'name'         => $name,
        'url'          => $url,
        'comment'      => $comment,
      ];
    }

    // tri ordre alpha
    uasort($mySoftwarecategories, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('softwarecategories', $mySoftwarecategories);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/softwarecategories.html.twig', (array)$viewData);
  }
}
