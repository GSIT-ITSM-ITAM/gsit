<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Softwarelicense extends Common
{
  protected $model = '\App\Models\Softwarelicense';
  protected $rootUrl2 = '/softwarelicenses/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwarelicense();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwarelicense();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Softwarelicense();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubSoftwarelicenses(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('childs')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/licenses');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $mySoftwarelicenses = [];
    foreach ($myItem->childs as $child)
    {
      $name = $child->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/softwarelicenses/', $child->id);

      $entity = '';
      $entity_url = '';
      if ($child->entity !== null)
      {
        $entity = $child->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $child->entity->id);
      }

      $comment = $child->comment;

      $mySoftwarelicenses[] = [
        'name'        => $name,
        'url'         => $url,
        'entity'      => $entity,
        'entity_url'  => $entity_url,
        'comment'     => $comment,
      ];
    }

    // tri ordre alpha
    uasort($mySoftwarelicenses, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('softwarelicenses', $mySoftwarelicenses);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/softwarelicenses.html.twig', (array)$viewData);
  }
}
