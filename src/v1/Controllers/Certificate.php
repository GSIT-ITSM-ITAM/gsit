<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Certificate extends Common
{
  protected $model = '\App\Models\Certificate';
  protected $rootUrl2 = '/certificates/';
  protected $choose = 'certificates';
  protected $associateditems_model = '\App\Models\Certificateitem';
  protected $associateditems_model_id = 'certificate_id';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Certificate();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Certificate();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Certificate();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubDomains(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Domain();
    $myItem2 = $item2::with('certificates')->get();

    $rootUrl = $this->genereRootUrl($request, '/domains');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDomains = [];
    foreach ($myItem2 as $domain)
    {
      $add_domain = false;
      if ($domain->certificates !== null)
      {
        foreach ($domain->certificates as $certificate)
        {
          if ($args['id'] == $certificate->id)
          {
            $add_domain = true;
            break;
          }
        }
      }

      if ($add_domain)
      {
        $entity = '';
        $entity_url = '';
        if ($domain->entity !== null)
        {
          $entity = $domain->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $domain->entity->id);
        }

        $groupstech = '';
        $groupstech_url = '';
        if ($domain->groupstech !== null)
        {
          $groupstech = $domain->groupstech->completename;
          $groupstech_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $domain->groupstech->id);
        }

        $userstech = '';
        $userstech_url = '';
        if ($domain->userstech !== null)
        {
          $userstech = $this->genereUserName(
            $domain->userstech->name,
            $domain->userstech->lastname,
            $domain->userstech->firstname
          );
          $userstech_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $domain->userstech->id);
        }

        $type = '';
        $type_url = '';
        if ($domain->type !== null)
        {
          $type = $domain->type->name;
          $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/domaintypes/', $domain->type->id);
        }

        $relation = '';
        $relation_url = '';

        $item3 = new \App\Models\DomainItem();
        $myItem3 = $item3::with('relation')->where('domain_id', $domain->id)->get();
        foreach ($myItem3 as $domainitem)
        {
          if (($args['id'] == $domainitem->item_id) && ('\\' . $domainitem->item_type == $this->model))
          {
            $domainrelation = \App\Models\Domainrelation::find($domainitem->domainrelation_id);
            if ($domainrelation !== null)
            {
              $relation = $domainrelation->name;
              $relation_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/domainrelations/', $domainrelation->id);
            }
          }
        }

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
          'name'              => $domain->name,
          'entity'            => $entity,
          'entity_url'        => $entity_url,
          'group'             => $groupstech,
          'group_url'         => $groupstech_url,
          'user'              => $userstech,
          'user_url'          => $userstech_url,
          'type'              => $type,
          'type_url'          => $type_url,
          'relation'          => $relation,
          'relation_url'      => $relation_url,
          'date_create'       => $domain->created_at,
          'date_exp'          => $date_expiration,
          'alert_expiration'  => $alert_expiration,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('domains', $myDomains);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('group', $translator->translate('Group in charge'));
    $viewData->addTranslation('user', $translator->translate('Technician in charge'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('relation', $translator->translatePlural('Domain relation', 'Domains relations', 1));
    $viewData->addTranslation('date_create', $translator->translate('Creation date'));
    $viewData->addTranslation('date_exp', $translator->translate('Expiration date'));

    return $view->render($response, 'subitem/domains.html.twig', (array)$viewData);
  }
}
