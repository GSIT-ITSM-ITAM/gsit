<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Documentcategory extends Common
{
  protected $model = '\App\Models\Documentcategory';
  protected $rootUrl2 = '/dropdowns/documentcategories/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Documentcategory();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Documentcategory();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Documentcategory();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubDocumentcategories(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->model();
    $myItem2 = $item2::where('documentcategory_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/categories');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDocumentcategories = [];
    foreach ($myItem2 as $documentcategory)
    {
      $name = $documentcategory->name;
      if ($name == '')
      {
        $name = '(' . $documentcategory->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/documentcategories/', $documentcategory->id);

      $comment = $documentcategory->comment;

      $myDocumentcategories[] = [
        'name'         => $name,
        'url'          => $url,
        'comment'      => $comment,
      ];
    }

    // tri ordre alpha
    uasort($myDocumentcategories, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('documentcategories', $myDocumentcategories);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/documentcategories.html.twig', (array)$viewData);
  }
}
