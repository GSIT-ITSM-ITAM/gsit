<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Businesscriticity extends Common
{
  protected $model = '\App\Models\Businesscriticity';
  protected $rootUrl2 = '/dropdowns/businesscriticities/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Businesscriticity();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Businesscriticity();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Businesscriticity();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubBusinesscriticities(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->model();
    $myItem2 = $item2::where('businesscriticity_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/businesscriticities');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myBusinesscriticities = [];
    foreach ($myItem2 as $businesscriticity)
    {
      $name = $businesscriticity->name;
      if ($name == '')
      {
        $name = '(' . $businesscriticity->id . ')';
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/businesscriticities/', $businesscriticity->id);

      $entity = '';
      $entity_url = '';
      if ($businesscriticity->entity !== null)
      {
        $entity = $businesscriticity->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $businesscriticity->entity->id);
      }

      $comment = $businesscriticity->comment;

      $myBusinesscriticities[] = [
        'name'         => $name,
        'url'          => $url,
        'entity'       => $entity,
        'entity_url'   => $entity_url,
        'comment'      => $comment,
      ];
    }

    // tri ordre alpha
    uasort($myBusinesscriticities, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('businesscriticities', $myBusinesscriticities);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/businesscriticities.html.twig', (array)$viewData);
  }
}
