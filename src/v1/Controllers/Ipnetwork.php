<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Ipnetwork extends Common
{
  protected $model = '\App\Models\Ipnetwork';
  protected $rootUrl2 = '/dropdowns/ipnetwork/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Ipnetwork();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Ipnetwork();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Ipnetwork();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubVlans(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/vlans');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myVlans = [];
    foreach ($myItem->vlans as $vlan)
    {
      $name = $vlan->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/vlans/', $vlan->id);

      $entity = '';
      $entity_url = '';
      if ($vlan->entity !== null)
      {
        $entity = $vlan->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $vlan->entity->id);
      }

      $tagid = $vlan->tag;

      $myVlans[] = [
        'name'          => $name,
        'url'           => $url,
        'entity'        => $entity,
        'entity_url'    => $entity_url,
        'tagid'         => $tagid,
      ];
    }

    // tri ordre alpha
    uasort($myVlans, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('vlans', $myVlans);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('tagid', $translator->translate('ID TAG'));

    return $view->render($response, 'subitem/vlans.html.twig', (array)$viewData);
  }
}
