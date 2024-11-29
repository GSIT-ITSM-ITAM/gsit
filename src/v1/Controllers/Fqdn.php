<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Fqdn extends Common
{
  protected $model = '\App\Models\Fqdn';
  protected $rootUrl2 = '/dropdowns/fqdns/';
  protected $choose = 'fqdns';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Fqdn();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Fqdn();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Fqdn();
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

      $computername = '';
      $computername_url = '';
      $networkname = \App\Models\Networkname::find($current_item->networkname_id);
      if ($networkname !== null)
      {
        $computername = $networkname->name . '.' . $myItem->fqdn;
        $computername_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/networkname/',
          $current_item->networkname_id
        );
      }

      $comment = $current_item->comment;

      $myNetworkAlias[] = [
        'name'                => $name,
        'url'                 => $url,
        'computername'        => $computername,
        'computername_url'    => $computername_url,
        'comment'             => $comment,
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

    $viewData->addTranslation('networkalias', $translator->translatePlural('Network alias', 'Network aliases', 1));
    $viewData->addTranslation('computername', $translator->translate("Computer's name"));
    $viewData->addTranslation('comment', $translator->translate('Comments'));

    return $view->render($response, 'subitem/networkalias.html.twig', (array)$viewData);
  }
}
