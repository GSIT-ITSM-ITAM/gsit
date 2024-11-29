<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Domainrelation extends Common
{
  protected $model = '\App\Models\Domainrelation';
  protected $rootUrl2 = '/dropdowns/domainrelations/';
  protected $choose = 'domainrelations';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Domainrelation();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Domainrelation();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Domainrelation();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubDomains(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/domains');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDomains = [];
    foreach ($myItem->domains as $domain)
    {
      $name = $domain->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/domains/', $domain->id);

      $entity = '';
      $entity_url = '';
      if ($domain->entity !== null)
      {
        $entity = $domain->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $domain->entity->id);
      }

      $group = '';
      $group_url = '';
      if ($domain->groupstech !== null)
      {
        $group = $domain->groupstech->completename;
        $group_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $domain->groupstech->id);
      }

      $user = '';
      $user_url = '';
      if ($domain->userstech !== null)
      {
        $user = $this->genereUserName(
          $domain->userstech->name,
          $domain->userstech->lastname,
          $domain->userstech->firstname
        );
        $user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $domain->userstech->id);
      }

      $type = '';
      $type_url = '';
      if ($domain->type !== null)
      {
        $type = $domain->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/domaintypes/', $domain->type->id);
      }

      $create_at = $domain->created_at;

      $alert_expiration = false;
      $date_expiration = $domain->date_expiration;
      if ($date_expiration == null)
      {
        $date_expiration = $translator->translate("N'expire pas");
      }
      else
      {
        if ($date_expiration < date('Y-m-d H:i:s'))
        {
          $alert_expiration = true;
        }
      }

      $myDomains[] = [
        'name'                => $name,
        'url'                 => $url,
        'entity'              => $entity,
        'entity_url'          => $entity_url,
        'group'               => $group,
        'group_url'           => $group_url,
        'user'                => $user,
        'user_url'            => $user_url,
        'type'                => $type,
        'type_url'            => $type_url,
        'date_create'         => $create_at,
        'date_exp'            => $date_expiration,
        'alert_expiration'    => $alert_expiration,
      ];
    }

    // tri ordre alpha
    uasort($myDomains, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('domains', $myDomains);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('group', $translator->translate('Group in charge'));
    $viewData->addTranslation('user', $translator->translate('Technician in charge'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('date_create', $translator->translate('Creation date'));
    $viewData->addTranslation('date_exp', $translator->translate('Expiration date'));

    return $view->render($response, 'subitem/domains.html.twig', (array)$viewData);
  }
}
