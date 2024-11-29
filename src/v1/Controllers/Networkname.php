<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Networkname extends Common
{
  protected $model = '\App\Models\Networkname';
  protected $rootUrl2 = '/dropdowns/networknames/';
  protected $choose = 'networknames';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Networkname();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Networkname();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Networkname();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubNetworkalias(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/networkalias');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myNetworkAlias = [];
    foreach ($myItem->alias as $current_item)
    {
      $name = $current_item->name;

      $url = '';
      // $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/networkalias/', $current_item->id);    // TODO

      $domain = '';
      $domain_url = '';
      if ($myItem->fqdn !== null)
      {
        $domain = $myItem->fqdn->name;
        $domain_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/fqdns/', $myItem->fqdn->id);
      }

      $entity = '';
      $entity_url = '';
      if ($current_item->entity !== null)
      {
        $entity = $current_item->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $current_item->entity->id);
      }

      $comment = $current_item->comment;

      $myNetworkAlias[] = [
        'name'            => $name,
        'url'             => $url,
        'domain'          => $domain,
        'domain_url'      => $domain_url,
        'entity'          => $entity,
        'entity_url'      => $entity_url,
      ];
    }

    // tri ordre alpha
    uasort($myNetworkAlias, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('networkalias', $myNetworkAlias);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translatePlural('Name', 'Names', 1));
    $viewData->addTranslation('domain', $translator->translatePlural('Internet domain', 'Internet domains', 1));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));

    return $view->render($response, 'subitem/networkalias.html.twig', (array)$viewData);
  }
}
