<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Common
{
  protected $model = '';
  protected $rootUrl2 = '';

  protected function getUrlWithoutQuery(Request $request)
  {
    $uri = $request->getUri();
    $query = $uri->getQuery();
    $url = (string) $uri;
    if (!empty($query))
    {
      $url = str_replace('?' . $query, '', $url);
    }
    return $url;
  }

  protected function commonGetAll(Request $request, Response $response, $args, $item): Response
  {
    $params = $request->getQueryParams();
    $page = 1;
    $view = Twig::fromRequest($request);

    $search = new \App\v1\Controllers\Search();
    $url = $this->getUrlWithoutQuery($request);
    if (isset($params['page']) && is_numeric($params['page']))
    {
      $page = (int) $params['page'];
    }

    $fields = $search->getData($item, $url, $page, $params);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);
    $viewData->addHeaderTitle('GSIT - ' . $item->getTitle(2));

    $viewData->addData('fields', $fields);

    $viewData->addData('definition', $item->getDefinitions());

    return $view->render($response, 'search.html.twig', (array)$viewData);
  }

  protected function commonShowItem(Request $request, Response $response, $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);
    $session = new \SlimSession\Helper();

    $myItem = $item->find($args['id']);

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($this->getUrlWithoutQuery($request)));

    $viewData->addData('fields', $item->getFormData($myItem));

    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));

    // Information TOP
    $informations = $this->getInformationTop($myItem, $request);
    foreach ($informations as $info)
    {
      $viewData->addInformation('top', $info['key'], $info['value'], $info['link']);
    }

    // Information BOTTOM
    $informations = $this->getInformationBottom($myItem, $request);
    foreach ($informations as $info)
    {
      $viewData->addInformation('bottom', $info['key'], $info['value'], $info['link']);
    }


    if ($session->exists('message'))
    {
      $viewData->addMessage($session->message);
      $session->delete('message');
    }

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }

  public function commonUpdateItem(Request $request, Response $response, $args, $item): Response
  {
    $data = (object) $request->getParsedBody();
    // $myItem = $item->find($args['id']);

    // // rewrite data with right database name (for dropdown mainly)
    // $definitions = $item->getDefinitions();
    // foreach ($definitions as $def)
    // {
    //   echo "<br>";
    //   if (property_exists($data, $def['name']))
    //   {
    //     if (in_array($def['type'], ['input', 'textarea', 'dropdown']))
    //     {
    //       if ($myItem->{$def['name']} != $data->{$def['name']})
    //       {
    //         $myItem->{$def['name']} = $data->{$def['name']};
    //       }
    //     }
    //     elseif ($def['type'] == 'dropdown_remote')
    //     {
    //       if (isset($def['multiple']))
    //       {
    //         $values = $data->{$def['name']};
    //         if (!is_array($values))
    //         {
    //           if (empty($values))
    //           {
    //             $values = [];
    //           } else {
    //             $values = explode(',', $values);
    //           }
    //         }
    //         // save
    //         $myItem->{$def['name']}()->syncWithPivotValues($values, $def['pivot']);
    //       }
    //       elseif ($myItem->{$def['dbname']} != $data->{$def['name']})
    //       {
    //         $myItem->{$def['dbname']} = $data->{$def['name']};
    //       }
    //     }
    //   }
    // }

    // // update
    // $myItem->save();
    $this->saveItem($data, $args['id']);

    // manage logs => manage it into model

    // post update

    // add message to session
    $session = new \SlimSession\Helper();
    $session->message = "The item has been updated correctly";

    $uri = $request->getUri();
    header('Location: ' . (string) $uri);
    exit();
  }

  protected function commonShowITILItem(Request $request, Response $response, $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    $session = new \SlimSession\Helper();

    // Load the item
    // $item->loadId($args['id']);
    $myItem = $item->find($args['id']);

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($this->getUrlWithoutQuery($request)));
    $viewData->addHeaderColor($myItem->getColor());

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('feeds', $item->getFeeds($args['id']));
    if (is_null($myItem->content))
    {
      $viewData->addData('content', null);
    } else {
      $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    }

    $viewData->addTranslation('description', $translator->translate('Description'));
    $viewData->addTranslation('feeds', $translator->translate('Feeds'));
    $viewData->addTranslation('followup', $translator->translatePlural('Followup', 'Followups', 1));
    $viewData->addTranslation('solution', $translator->translatePlural('Solution', 'Solutions', 1));
    $viewData->addTranslation('template', $translator->translatePlural('Template', 'Templates', 1));
    $viewData->addTranslation('private', $translator->translate('Private'));
    $viewData->addTranslation('sourcefollow', $translator->translate('Source of followup'));
    $viewData->addTranslation('category', $translator->translatePlural('Category', 'Categories', 1));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('duration', $translator->translate('Duration'));
    $viewData->addTranslation('seconds', $translator->translatePlural('Second', 'Seconds', 2));
    $viewData->addTranslation('minutes', $translator->translatePlural('Minute', 'Minutes', 2));
    $viewData->addTranslation('hours', $translator->translatePlural('Hour', 'Hours', 2));
    $viewData->addTranslation('user', $translator->translatePlural('User', 'Users', 1));
    $viewData->addTranslation('group', $translator->translatePlural('Group', 'Groups', 1));
    $viewData->addTranslation('addfollowup', $translator->translate('Add followup'));
    $viewData->addTranslation('timespent', $translator->translate('Time spent'));
    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));
    $viewData->addTranslation('yes', $translator->translate('Yes'));
    $viewData->addTranslation('no', $translator->translate('No'));

    if ($session->exists('message'))
    {
      $viewData->addMessage($session->message);
      $session->delete('message');
    }

    return $view->render($response, 'ITILForm.html.twig', (array)$viewData);
  }

  public function showNewItem(Request $request, Response $response, $args): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    $item = new $this->model();

    $session = new \SlimSession\Helper();

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addData('fields', $item->getFormData($item));
    $viewData->addData('content', '');

    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));

    if ($session->exists('message'))
    {
      $viewData->addMessage($session->message);
      $session->delete('message');
    }

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }

  public function commonShowITILNewItem(Request $request, Response $response, $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    $session = new \SlimSession\Helper();

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addData('fields', $item->getFormData($item));
    $viewData->addData('feeds', []);
    $viewData->addData('content', '');

    $viewData->addTranslation('description', $translator->translate('Description'));
    $viewData->addTranslation('feeds', $translator->translate('Feeds'));
    $viewData->addTranslation('followup', $translator->translatePlural('Followup', 'Followups', 1));
    $viewData->addTranslation('solution', $translator->translatePlural('Solution', 'Solutions', 1));
    $viewData->addTranslation('template', $translator->translatePlural('Template', 'Templates', 1));
    $viewData->addTranslation('private', $translator->translate('Private'));
    $viewData->addTranslation('sourcefollow', $translator->translate('Source of followup'));
    $viewData->addTranslation('category', $translator->translatePlural('Category', 'Categories', 1));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('duration', $translator->translate('Duration'));
    $viewData->addTranslation('seconds', $translator->translatePlural('Second', 'Seconds', 2));
    $viewData->addTranslation('minutes', $translator->translatePlural('Minute', 'Minutes', 2));
    $viewData->addTranslation('hours', $translator->translatePlural('Hour', 'Hours', 2));
    $viewData->addTranslation('user', $translator->translatePlural('User', 'Users', 1));
    $viewData->addTranslation('group', $translator->translatePlural('Group', 'Groups', 1));
    $viewData->addTranslation('addfollowup', $translator->translate('Add followup'));
    $viewData->addTranslation('timespent', $translator->translate('Time spent'));
    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));
    $viewData->addTranslation('yes', $translator->translate('Yes'));
    $viewData->addTranslation('no', $translator->translate('No'));

    if ($session->exists('message'))
    {
      $viewData->addMessage($session->message);
      // $session->delete('message');
    }

    return $view->render($response, 'ITILForm.html.twig', (array)$viewData);
  }

  public function showSubHistory(Request $request, Response $response, $args)
  {
    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $session = new \SlimSession\Helper();

    // Load the item
    $myItem = $item->find($args['id']);

    $logs = [];
    if ($myItem != null) {
      $logs = \App\Models\Log::
          where('item_type', ltrim($this->model, '\\'))
        ->where('item_id', $myItem->id)
        ->orderBy('id', 'desc')
        ->get();
    }


    $fieldsTitle = [];
    foreach ($definitions as $def)
    {
      $fieldsTitle[$def['id']] = $def['title'];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/history');

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    // $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    $viewData->addData('history', $logs);
    $viewData->addData('titles', $fieldsTitle);

    if ($session->exists('message'))
    {
      $viewData['message'] = $session->message;
      $session->delete('message');
    }

    return $view->render($response, 'subitem/history.html.twig', (array)$viewData);
  }

  public function newItem(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();
    $id = $this->saveItem($data);

    if (property_exists($data, 'save') && $data->save == 'view')
    {
      $uri = $request->getUri();
      return $response
        ->withHeader('Location', str_replace('/new', '/' . $id, (string) $uri))
        ->withStatus(302);
      exit;
    }

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);
  }

  public function saveItem($data, $id = null)
  {
    // Manage fields like dropdown where name not same in database
    $fieldsDef = [];
    $booleans = [];
    $item = new $this->model();
    $definitions = $item->getDefinitions();
    foreach ($definitions as $definition)
    {
      if (isset($definition['fillable']) && $definition['fillable'] && isset($definition['dbname']))
      {
        $fieldsDef[$definition['name']] = $definition['dbname'];
      }
      if ($definition['type'] == 'boolean')
      {
        $booleans[$definition['name']] = true;
      }
    }

    if (is_null($id))
    {
      foreach ((array) $data as $key => $value)
      {
        if (isset($booleans[$key]))
        {
          if ($value == 'on')
          {
            $data->{$key} = true;
          } else {
            $data->{$key} = false;
          }
        }
      }

      $item = $this->model::create((array) $data);
    } else {
      // update
      $item = $this->model::find($id);
      if (is_null($item))
      {
        // Error
        return $id;
      }

      $aData = (array) $data;
      foreach ($aData as $key => $value)
      {
        if (isset($fieldsDef[$key]))
        {
          $aData[$fieldsDef[$key]] = $value;
        }
        if (isset($booleans[$key]))
        {
          if ($value == 'on')
          {
            $aData[$key] = true;
          } else {
            $aData[$key] = false;
          }
        }
      }
      $item->update($aData);
    }

    // manage multiple
    foreach ($definitions as $def)
    {
      if (isset($def['multiple']))
      {
        $key = $def['name'];
        $pivot = [];
        if (isset($def['pivot']))
        {
          $pivot = $def['pivot'];
        }
        if (!property_exists($data, $key))
        {
          continue;
        }
        if (!is_array($data->{$key}))
        {
          if (empty($data->{$key}))
          {
            $data->{$key} = [];
          } else {
            $data->{$key} = explode(',', $data->{$key});
          }
        }

        $dbItems = [];
        foreach ($item->$key as $relationItem)
        {
          $dbItems[] = $relationItem->id;
        }
        // To delete
        $toDelete = array_diff($dbItems, $data->{$key});
        foreach ($toDelete as $groupId)
        {
          $item->$key()->detach($groupId, $pivot);
        }

        // To add
        $toAdd = array_diff($data->{$key}, $dbItems);
        foreach ($toAdd as $groupId)
        {
          // $item->$key()->attach($groupId, $pivot);
        }
      }
    }

    // notification
    if (is_null($id))
    {
      \App\v1\Controllers\Notification::prepareNotification($item, 'new');
    } else {
      \App\v1\Controllers\Notification::prepareNotification($item, 'update');
    }
    exit;
    return $item->id;
  }

  protected function getInformationTop($item, $request)
  {
    return [];
  }

  protected function getInformationBottom($item, $request)
  {
    return [];
  }



  public function showSubNotes(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('notes')->find($args['id']);

    $myNotes = [];
    foreach ($myItem->notes as $note)
    {
      $user = '';
      if ($note->user !== null)
      {
        $user = $note->user->name;
      }
      $user_lastupdater = '';
      if ($note->userlastupdater !== null)
      {
        $user_lastupdater = $note->userlastupdater->name;
      }

      $create = sprintf($translator->translate('Create by %1$s on %2$s'), $user, $note->created_at);
      $update = sprintf($translator->translate('Last update by %1$s on %2$s'), $user_lastupdater, $note->updated_at);

      $myNotes[] = [
        'content' => str_ireplace("\n", "<br/>", $note->content),
        'create' => $create,
        'update' => $update,
        'updated_at' => $note->updated_at,
      ];
    }

    // tri de la + récente à la + ancienne
    usort($myNotes, function ($a, $b)
    {
      return $a['updated_at'] < $b['updated_at'];
    });

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/notes');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('notes', $myNotes);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/notes.html.twig', (array)$viewData);
  }

  public function showSubDomains(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('domains')->find($args['id']);

    $myDomains = [];
    foreach ($myItem->domains as $domain)
    {
      $entity = '';
      if ($domain->entity !== null)
      {
        $entity = $domain->entity->name;
      }
      $groupstech = '';
      if ($domain->groupstech !== null)
      {
        $groupstech = $domain->groupstech->name;
      }
      $userstech = '';
      if ($domain->userstech !== null)
      {
        $userstech = $domain->userstech->name;
      }
      $type = '';
      if ($domain->type !== null)
      {
        $type = $domain->type->name;
      }
      $domainrelation = \App\Models\Domainrelation::find($domain->pivot->domainrelation_id);
      $relation = '';
      if ($domainrelation !== null)
      {
        $relation = $domainrelation->name;
      }
      $date_expiration = $domain->date_expiration;
      if ($date_expiration == null)
      {
        $date_expiration = $translator->translate("N'expire pas");
      }

      $myDomains[] = [
        'name'          => $domain->name,
        'entity'        => $entity,
        'group'         => $groupstech,
        'user'          => $userstech,
        'type'          => $type,
        'relation'      => $relation,
        'date_create'   => $domain->created_at,
        'date_exp'      => $date_expiration,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/domains');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('domains', $myDomains);

    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('group', $translator->translate('Group in charge'));
    $viewData->addTranslation('user', $translator->translate('Technician in charge'));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('relation', $translator->translatePlural('Domain relation', 'Domains relations', 1));
    $viewData->addTranslation('date_create', $translator->translate('Creation date'));
    $viewData->addTranslation('date_exp', $translator->translate('Expiration date'));

    return $view->render($response, 'subitem/domains.html.twig', (array)$viewData);
  }

  public function showSubAppliances(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('appliances')->find($args['id']);

    $myAppliances = [];
    foreach ($myItem->appliances as $appliance)
    {
      $myAppliances[] = [
        'name' => $appliance->name,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/appliances');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('appliances', $myAppliances);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/appliances.html.twig', (array)$viewData);
  }

  public function showSubCertificates(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('certificates')->find($args['id']);

    $myCertificates = [];
    foreach ($myItem->certificates as $certificate)
    {
      $type = '';
      if ($certificate->type !== null)
      {
        $type = $certificate->type->name;
      }
      $entity = '';
      if ($certificate->entity !== null)
      {
        $entity = $certificate->entity->name;
      }

      $date_expiration = $certificate->date_expiration;
      if ($date_expiration == null)
      {
        $date_expiration = $translator->translate("N'expire pas");
      }
      $state = '';
      if ($certificate->state !== null)
      {
        $state = $certificate->state->name;
      }


      $myCertificates[] = [
        'name'              => $certificate->name,
        'entity'            => $entity,
        'type'              => $type,
        'dns_name'          => $certificate->dns_name,
        'dns_suffix'        => $certificate->dns_suffix,
        'created_at'        => $certificate->created_at,
        'date_expiration'   => $date_expiration,
        'state'             => $state,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/certificates');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('certificates', $myCertificates);

    $viewData->addTranslation('name', 'Nom');
    $viewData->addTranslation('entity', 'Entité');
    $viewData->addTranslation('type', 'Type');
    $viewData->addTranslation('dns_name', 'Nom DNS');
    $viewData->addTranslation('dns_suffix', 'Suffixe DNS');
    $viewData->addTranslation('created_at', 'Date de création');
    $viewData->addTranslation('date_expiration', "Date d'expiration");
    $viewData->addTranslation('state', 'Statut');

    return $view->render($response, 'subitem/certificates.html.twig', (array)$viewData);
  }

  public function showSubExternalLinks(Request $request, Response $response, $args): Response
  {
    global $translator;

    $computermodelclass = str_ireplace('\\v1\\Controllers\\', '\\Models\\', get_class($this));

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::find($args['id']);

    $item2 = new \App\Models\LinkItemtype();
    $externallinks = $item2::with('links')->where('item_type', $computermodelclass)->get();

    $item3 = new \App\Models\DomainItem();
    $domainitems = $item3->where(['item_id'=>$args['id'],'item_type'=>$computermodelclass])->get();

    $myExternalLinks = [];
    foreach ($externallinks as $externallink)
    {
      $name = '';
      $open_window = 0;
      $link = '';
      $data = '';
      $generate = '';
      if ($externallink->links !== null)
      {
        $name = $externallink->links->name;
        $open_window = $externallink->links->open_window;
        $link = $externallink->links->link;
        $data = $externallink->links->data;


        $location_id = '';
        $location_name = '';
        if ($myItem->location != null)
        {
          $location_id = $myItem->location->id;
          $location_name = $myItem->location->name;
        }

        $domains = [];
        foreach ($domainitems as $domainitem) {
          if ($domainitem->domain != null) {
            $domains[] = $domainitem->domain->name;
          }
        }

        $network_name = '';
        if ($myItem->network != null)
        {
          $network_name = $myItem->network->name;
        }

        $user_name = '';
        if ($myItem->user != null)
        {
          $user_name = $myItem->user->name;
        }
        $group_name = '';
        if ($myItem->group != null)
        {
          $group_name = $myItem->group->name;
        }

        $ips = [];
        $macs = [];

        $itemsLink = [
          'id' => $externallink->links->id,
          'name' => $myItem->name,
          'serial' => $myItem->serial,
          'otherserial' => $myItem->otherserial,
          'location_id' => $location_id,
          'location' => $location_name,
          'domains' => $domains,
          'network' => $network_name,
          'comment' => $myItem->comment,
          'user' => $user_name,
          'group' => $group_name,
          // 'realname' => $realname,
          // 'firstname' => $firstname,
          // 'login' => $login,
          // 'ips' => $ips,
          // 'macs' => $macs,
        ];

        $generate = $name . ' : ' . self::generateLinkContents($data, $itemsLink, true);
      }


      $myExternalLinks[] = [
        'name'          => $name,
        'open_window'   => $open_window,
        'link'          => $link,
        'generate'      => $generate,
      ];
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/externallinks');

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('externallinks', $myExternalLinks);

    return $view->render($response, 'subitem/externallinks.html.twig', (array)$viewData);
  }

  public function generateLinkContents($link, $item, $replaceByBr = false)
  {
    $new_link = $link;
    if ($replaceByBr === true) $new_link=str_ireplace("\n", "<br>", $new_link);
    $matches = [];
    if (preg_match_all('/\[FIELD:(\w+)\]/', $new_link, $matches)) {
      foreach ($matches[1] as $key => $field) {
        $new_link = self::checkAndReplaceProperty($item, $field, $matches[0][$key], $new_link, $replaceByBr);
      }
    }

    if (strstr($new_link, "[ID]")) {
      $new_link = self::checkAndReplaceProperty($item, 'id', "[ID]", $new_link, $replaceByBr);
    }
    if (strstr($link, "[NAME]")) {
      $new_link = self::checkAndReplaceProperty($item, 'name', "[NAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[SERIAL]")) {
      $new_link = self::checkAndReplaceProperty($item, 'serial', "[SERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[OTHERSERIAL]")) {
      $new_link = self::checkAndReplaceProperty($item, 'otherserial', "[OTHERSERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATIONID]")) {
      $new_link = self::checkAndReplaceProperty($item, 'location_id', "[LOCATIONID]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATION]")) {
      $new_link = self::checkAndReplaceProperty($item, 'location', "[LOCATION]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[DOMAIN]")) {
      $new_link = self::checkAndReplaceProperty($item, 'domains', "[DOMAIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[NETWORK]")) {
      $new_link = self::checkAndReplaceProperty($item, 'network', "[NETWORK]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[REALNAME]")) {
      $new_link = self::checkAndReplaceProperty($item, 'realname', "[REALNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[FIRSTNAME]")) {
      $new_link = self::checkAndReplaceProperty($item, 'firstname', "[FIRSTNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOGIN]")) {
      $new_link = self::checkAndReplaceProperty($item, 'login', "[LOGIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[USER]")) {
      $new_link = self::checkAndReplaceProperty($item, 'user', "[USER]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[GROUP]")) {
      $new_link = self::checkAndReplaceProperty($item, 'group', "[GROUP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[IP]")) {
      $new_link = self::checkAndReplaceProperty($item, 'ips', "[IP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[MAC]")) {
      $new_link = self::checkAndReplaceProperty($item, 'macs', "[MAC]", $new_link, $replaceByBr);
    }

    return $new_link;
  }

  public function checkAndReplaceProperty($item, $field, $strToReplace, $new_link, $replaceByBr = false)
  {
    $ret = $new_link;

    if (array_key_exists($field, $item)) {
      if (is_array($item[$field])) {
        $tmp = '';
        foreach ($item[$field] as $val) {
          if ($tmp != '') $tmp = $tmp  . "\n";
          $tmp = $tmp . $val;
        }
        $ret = str_replace($strToReplace, $tmp, $ret);
      } else {
        $ret = str_replace($strToReplace, $item[$field], $ret);
      }
      if ($replaceByBr === true) $ret=str_ireplace("\n", "<br>", $ret);
    }

    return $ret;
  }

  public function showSubKnowbaseitems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('knowbaseitems')->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/knowbaseitems');
    $rootUrl2 = '';
    if ($this->rootUrl2 != '') {
      $rootUrl2 = rtrim($rootUrl, $this->rootUrl2 . $args['id']);
    }

    $myKnowbaseitems = [];
    foreach ($myItem->knowbaseitems as $knowbaseitem)
    {
      $url = '';
      if ($rootUrl2 != '') {
        $url = $rootUrl2 . "/knowbaseitems/" . $knowbaseitem->id;
      }

      $myKnowbaseitems[$knowbaseitem->id] = [
        'name'           => $knowbaseitem->name,
        'created_at'     => $knowbaseitem->date,
        'updated_at'     => $knowbaseitem->updated_at,
        'url'            => $url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('knowbaseitems', $myKnowbaseitems);

    $viewData->addTranslation('name', $translator->translatePlural('Item', 'Items', 1));
    $viewData->addTranslation('created_at', $translator->translate('Creation date'));
    $viewData->addTranslation('updated_at', $translator->translate('Update date'));

    return $view->render($response, 'subitem/knowbaseitems.html.twig', (array)$viewData);
  }

}
