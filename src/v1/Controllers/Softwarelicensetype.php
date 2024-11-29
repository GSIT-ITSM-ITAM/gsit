<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Softwarelicensetype extends Common
{
  protected $model = '\App\Models\Softwarelicensetype';
  protected $rootUrl2 = '/dropdowns/softwarelicensetypes/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwarelicensetype();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwarelicensetype();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwarelicensetype();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubLicencetypes(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->model();
    $myItem2 = $item2::where('softwarelicensetype_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/licencetypes');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myLicencetypes = [];
    foreach ($myItem2 as $softwarelicensetype)
    {
      $name = $softwarelicensetype->name;
      if ($name == '')
      {
        $name = '(' . $softwarelicensetype->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/softwarelicensetypes/', $softwarelicensetype->id);

      $entity = '';
      $entity_url = '';
      if ($softwarelicensetype->entity !== null)
      {
        $entity = $softwarelicensetype->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $softwarelicensetype->entity->id);
      }

      $comment = $softwarelicensetype->comment;

      $myLicencetypes[] = [
        'name'         => $name,
        'url'          => $url,
        'entity'       => $entity,
        'entity_url'   => $entity_url,
        'comment'      => $comment,
      ];
    }

    // tri ordre alpha
    uasort($myLicencetypes, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('licensetypes', $myLicencetypes);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/softwarelicensetypes.html.twig', (array)$viewData);
  }
}
