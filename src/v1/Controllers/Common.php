<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Common
{
  protected $model = '';
  protected $rootUrl2 = '';
  protected $choose = '';
  protected $associateditems_model = '';
  protected $associateditems_model_id = '';

  protected $MINUTE_TIMESTAMP = '60';
  protected $HOUR_TIMESTAMP = '3600';
  protected $DAY_TIMESTAMP = '86400';
  protected $WEEK_TIMESTAMP = '604800';
  protected $MONTH_TIMESTAMP = '2592000';

  protected $APPROVAL_NONE = 1;
  protected $APPROVAL_WAITING = 2;
  protected $APPROVAL_ACCEPTED = 3;
  protected $APPROVAL_REFUSED = 4;

  protected $TTR = 0;
  protected $TTO = 1;

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
    if (!$this->canRightRead())
    {
      throw new \Exception('Unauthorized access', 401);
    }

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

    $myItem = $item->find($args['id']);

    if (!\App\v1\Controllers\Profile::canRightReadItem($myItem))
    {
      throw new \Exception('Unauthorized access', 401);
    }

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
    //           }
    //           else
    //           {
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
    \App\v1\Controllers\Toolbox::addSessionMessage('The item has been updated successfully');

    $uri = $request->getUri();
    header('Location: ' . (string) $uri);
    exit();
  }

  protected function commonShowITILItem(Request $request, Response $response, $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    // Load the item
    // $item->loadId($args['id']);
    $myItem = $item->find($args['id']);

    if (!\App\v1\Controllers\Profile::canRightReadItem($myItem))
    {
      throw new \Exception('Unauthorized access', 401);
    }
    $title = '';

    $fields = $item->getFormData($myItem);
    foreach ($fields as $field)
    {
      if ($field['name'] == 'name')
      {
        $title = $field['value'];
        break;
      }
    }

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($this->getUrlWithoutQuery($request)));
    $viewData->addHeaderColor($myItem->getColor());

    $viewData->addData('fields', $fields);
    $viewData->addData('feeds', $item->getFeeds($args['id']));
    $viewData->addData('title', $title);

    if (is_null($myItem->content))
    {
      $viewData->addData('content', null);
    }
    else
    {
      $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    }
    $ctrlFollowup = new \App\v1\Controllers\Followup();
    $viewData->addData('fullFollowup', $ctrlFollowup->canRightReadPrivateItem());

    $canAddFollowup = true;
    $canAddSolution = true;
    if ($myItem->canOnlyReadItem())
    {
      $canAddFollowup = false;
      $canAddSolution = false;
    }
    $viewData->addData('canAddFollowup', $canAddFollowup);
    $viewData->addData('canAddSolution', $canAddSolution);

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

    return $view->render($response, 'ITILForm.html.twig', (array)$viewData);
  }

  public function showNewItem(Request $request, Response $response, $args): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

    $item = new $this->model();

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($item, $request);

    $viewData->addData('fields', $item->getFormData($item));
    $viewData->addData('content', '');

    $viewData->addTranslation('selectvalue', $translator->translate('Select a value...'));

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }

  public function commonShowITILNewItem(Request $request, Response $response, $args, $item): Response
  {
    global $translator;
    $view = Twig::fromRequest($request);

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

    return $view->render($response, 'ITILForm.html.twig', (array)$viewData);
  }

  public function showSubHistory(Request $request, Response $response, $args)
  {
    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    // Load the item
    $myItem = $item->find($args['id']);

    $logs = [];
    if ($myItem !== null)
    {
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
      if (!$this->canRightCreate($this->model))
      {
        throw new \Exception('Unauthorized access', 401);
      }
      foreach ((array) $data as $key => $value)
      {
        if (isset($booleans[$key]))
        {
          if ($value == 'on')
          {
            $data->{$key} = true;
          }
          else
          {
            $data->{$key} = false;
          }
        }
      }
      $aData = $this->filterFieldsAllowedToWrite($this->model, (array) $data);

      $item = $this->model::create($aData);
    }
    else
    {
      // update
      if (!$this->canRightCreate($this->model))
      {
        throw new \Exception('Unauthorized access', 401);
      }

      $item = $this->model::find($id);
      if (!\App\v1\Controllers\Profile::canRightReadItem($item))
      {
        throw new \Exception('Unauthorized access', 401);
      }

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
          }
          else
          {
            $aData[$key] = false;
          }
        }
      }
      $aData = $this->filterFieldsAllowedToWrite($this->model, $aData);

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
          }
          else
          {
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
          $item->$key()->attach($groupId, $pivot);
        }
      }
    }
    if (is_null($id))
    {
      \App\v1\Controllers\Toolbox::addSessionMessage('The item has been created successfully');
    }
    else
    {
      \App\v1\Controllers\Toolbox::addSessionMessage('The item has been updated successfully');
    }

    // notification
    if (is_null($id))
    {
      \App\v1\Controllers\Notification::prepareNotification($item, 'new');
    }
    else
    {
      \App\v1\Controllers\Notification::prepareNotification($item, 'update');
    }
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

    $rootUrl = $this->genereRootUrl($request, '/notes');

    $myNotes = [];
    foreach ($myItem->notes as $note)
    {
      $content = str_ireplace("\n", "<br/>", $note->content);

      $user = '';
      if ($note->user !== null)
      {
        $user = $this->genereUserName($note->user->name, $note->user->lastname, $note->user->firstname);
      }

      $user_lastupdater = '';
      if ($note->userlastupdater !== null)
      {
        $user_lastupdater = $this->genereUserName(
          $note->userlastupdater->name,
          $note->userlastupdater->lastname,
          $note->userlastupdater->firstname
        );
      }

      $create = sprintf($translator->translate('Create by %1$s on %2$s'), $user, $note->created_at);

      $update = sprintf($translator->translate('Last update by %1$s on %2$s'), $user_lastupdater, $note->updated_at);

      $myNotes[] = [
        'content'     => $content,
        'create'      => $create,
        'update'      => $update,
        'updated_at'  => $note->updated_at,
      ];
    }

    // tri de la + récente à la + ancienne
    uasort($myNotes, function ($a, $b)
    {
      return $a['updated_at'] < $b['updated_at'];
    });

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

    $rootUrl = $this->genereRootUrl($request, '/domains');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDomains = [];
    foreach ($myItem->domains as $domain)
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
      $domainrelation = \App\Models\Domainrelation::find($domain->pivot->domainrelation_id);
      if ($domainrelation !== null)
      {
        $relation = $domainrelation->name;
        $relation_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/domainrelations/', $domainrelation->id);
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

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/domains');

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

  public function showSubAppliances(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('appliances')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/appliances');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myAppliances = [];
    foreach ($myItem->appliances as $appliance)
    {
      $appliance_url = $this->genereRootUrl2Link($rootUrl2, '/appliances/', $appliance->id);

      $myAppliances[] = [
        'name'  => $appliance->name,
        'url'   => $appliance_url,
      ];
    }

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

    $rootUrl = $this->genereRootUrl($request, '/certificates');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myCertificates = [];
    foreach ($myItem->certificates as $certificate)
    {
      $type = '';
      $type_url = '';
      if ($certificate->type !== null)
      {
        $type = $certificate->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/certificatetypes/', $certificate->type->id);
      }

      $entity = '';
      $entity_url = '';
      if ($certificate->entity !== null)
      {
        $entity = $certificate->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $certificate->entity->id);
      }

      $alert_expiration = false;
      $date_expiration = $certificate->date_expiration;
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

      $state = '';
      $state_url = '';
      if ($certificate->state !== null)
      {
        $state = $certificate->state->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $certificate->state->id);
      }

      $myCertificates[] = [
        'name'              => $certificate->name,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'type'              => $type,
        'type_url'          => $type_url,
        'dns_name'          => $certificate->dns_name,
        'dns_suffix'        => $certificate->dns_suffix,
        'created_at'        => $certificate->created_at,
        'date_expiration'   => $date_expiration,
        'alert_expiration'  => $alert_expiration,
        'state'             => $state,
        'state_url'         => $state_url,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('certificates', $myCertificates);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('dns_name', $translator->translate('DNS name'));
    $viewData->addTranslation('dns_suffix', $translator->translate('DNS suffix'));
    $viewData->addTranslation('created_at', $translator->translate('Creation date'));
    $viewData->addTranslation('date_expiration', $translator->translate('Expiration date'));
    $viewData->addTranslation('status', $translator->translate('Status'));

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
    $domainitems = $item3->where(['item_id' => $args['id'], 'item_type' => $computermodelclass])->get();

    $rootUrl = $this->genereRootUrl($request, '/externallinks');

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
        if ($myItem->location !== null)
        {
          $location_id = $myItem->location->id;
          $location_name = $myItem->location->name;
        }

        $domains = [];
        foreach ($domainitems as $domainitem)
        {
          if ($domainitem->domain !== null)
          {
            $domains[] = $domainitem->domain->name;
          }
        }

        $network_name = '';
        if ($myItem->network !== null)
        {
          $network_name = $myItem->network->name;
        }

        $users = [];
        if ($myItem->user !== null)
        {
          if (isset($myItem->user->name))
          {
            $users[] = $this->genereUserName($myItem->user->name, $myItem->user->lastname, $myItem->user->firstname);
          }
          else
          {
            foreach ($myItem->user as $user)
            {
              $users[] = $this->genereUserName($user->name, $user->lastname, $user->firstname);
            }
          }
        }

        $groups = [];
        if ($myItem->group !== null)
        {
          if (isset($myItem->group->name))
          {
            $groups[] = $myItem->group->name;
          }
          else
          {
            foreach ($myItem->group as $group)
            {
              $groups[] = $group->name;
            }
          }
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
          'users' => $users,
          'groups' => $groups,
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

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('externallinks', $myExternalLinks);

    return $view->render($response, 'subitem/externallinks.html.twig', (array)$viewData);
  }

  private function generateLinkContents($link, $item, $replaceByBr = false)
  {
    $new_link = $link;
    if ($replaceByBr === true)
    {
      $new_link = str_ireplace("\n", "<br>", $new_link);
    }
    $matches = [];
    if (preg_match_all('/\[FIELD:(\w+)\]/', $new_link, $matches))
    {
      foreach ($matches[1] as $key => $field)
      {
        $new_link = self::checkAndReplaceProperty($item, $field, $matches[0][$key], $new_link, $replaceByBr);
      }
    }

    if (strstr($new_link, "[ID]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'id', "[ID]", $new_link, $replaceByBr);
    }
    if (strstr($link, "[NAME]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'name', "[NAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[SERIAL]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'serial', "[SERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[OTHERSERIAL]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'otherserial', "[OTHERSERIAL]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATIONID]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'location_id', "[LOCATIONID]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOCATION]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'location', "[LOCATION]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[DOMAIN]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'domains', "[DOMAIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[NETWORK]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'network', "[NETWORK]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[REALNAME]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'realname', "[REALNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[FIRSTNAME]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'firstname', "[FIRSTNAME]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[LOGIN]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'login', "[LOGIN]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[USER]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'users', "[USER]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[GROUP]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'groups', "[GROUP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[IP]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'ips', "[IP]", $new_link, $replaceByBr);
    }
    if (strstr($new_link, "[MAC]"))
    {
      $new_link = self::checkAndReplaceProperty($item, 'macs', "[MAC]", $new_link, $replaceByBr);
    }

    return $new_link;
  }

  private function checkAndReplaceProperty($item, $field, $strToReplace, $new_link, $replaceByBr = false)
  {
    $ret = $new_link;

    if (array_key_exists($field, $item))
    {
      if (is_array($item[$field]))
      {
        $tmp = '';
        foreach ($item[$field] as $val)
        {
          if ($tmp != '')
          {
            $tmp = $tmp  . "\n";
          }
          $tmp = $tmp . $val;
        }
        $ret = str_replace($strToReplace, $tmp, $ret);
      }
      else
      {
        $ret = str_replace($strToReplace, $item[$field], $ret);
      }
      if ($replaceByBr === true)
      {
        $ret = str_ireplace("\n", "<br>", $ret);
      }
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

    $rootUrl = $this->genereRootUrl($request, '/knowbaseitems');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myKnowbaseitems = [];
    foreach ($myItem->knowbaseitems as $knowbaseitem)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/knowbaseitems/', $knowbaseitem->id);

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

  public function showSubDocuments(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('documents')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/documents');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myDocuments = [];
    foreach ($myItem->documents as $document)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

      $entity = '';
      $entity_url = '';
      if ($document->entity !== null)
      {
        $entity = $document->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $document->entity->id);
      }

      $rubrique = '';
      $rubrique_url = '';
      if ($document->categorie !== null)
      {
        $rubrique = $document->categorie->name;
        $rubrique_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/documentcategories/',
          $document->categorie->id
        );
      }

      $myDocuments[$document->id] = [
        'name'              => $document->name,
        'date'              => $document->pivot->updated_at,
        'url'               => $url,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'file'              => $document->filename,
        'weblink'           => $document->link,
        'rubrique'          => $rubrique,
        'rubrique_url'      => $rubrique_url,
        'mimetype'          => $document->mime,
        'balise'            => $document->tag,
      ];
    }

    // tri de la + récente à la + ancienne
    uasort($myDocuments, function ($a, $b)
    {
      return $a['date'] < $b['date'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('documents', $myDocuments);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('file', $translator->translate('File'));
    $viewData->addTranslation('weblink', $translator->translate('Web link'));
    $viewData->addTranslation('rubrique', $translator->translate('Heading'));
    $viewData->addTranslation('mimetype', $translator->translate('MIME type'));
    $viewData->addTranslation('balise', $translator->translate('Tag'));
    $viewData->addTranslation('date', $translator->translatePlural('Dates', 'Dates', 1));

    return $view->render($response, 'subitem/documents.html.twig', (array)$viewData);
  }

  public function showSubContracts(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('contracts')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/contracts');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myContracts = [];
    foreach ($myItem->contracts as $contract)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/contracts/', $contract->id);

      $entity = '';
      $entity_url = '';
      if ($contract->entity !== null)
      {
        $entity = $contract->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $contract->entity->id);
      }

      $type = '';
      $contracttype_url = '';
      if ($contract->type !== null)
      {
        $type = $contract->type->name;
        $contracttype_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/contracttypes/', $contract->type->id);
      }

      $suppliers = [];
      if ($contract->suppliers !== null)
      {
        foreach ($contract->suppliers as $supplier)
        {
          $supplier_url = $this->genereRootUrl2Link($rootUrl2, '/suppliers/', $supplier->id);

          $suppliers[$supplier->id] = [
            'name' => $supplier->name,
            'url' => $supplier_url,
          ];
        }
      }

      $duration = $contract->duration;
      if ($duration == 0)
      {
        $initial_contract_period = sprintf($translator->translatePlural('%d month', '%d months', 1), $duration);
      }
      if ($duration != 0)
      {
        $initial_contract_period = sprintf($translator->translatePlural('%d month', '%d months', $duration), $duration);
      }

      if ($contract->begin_date !== null)
      {
        $ladate = $contract->begin_date;
        if ($duration != 0)
        {
          $end_date = date('Y-m-d', strtotime('+' . $duration . ' month', strtotime($ladate)));
          if ($end_date < date('Y-m-d'))
          {
            $end_date = "<span style=\"color: red;\">" . $end_date . "</span>";
          }
          $initial_contract_period = $initial_contract_period . ' => ' . $end_date;
        }
      }

      $myContracts[$contract->id] = [
        'name'                      => $contract->name,
        'url'                       => $url,
        'entity'                    => $entity,
        'entity_url'                => $entity_url,
        'number'                    => $contract->num,
        'type'                      => $type,
        'contracttype_url'          => $contracttype_url,
        'suppliers'                 => $suppliers,
        'start_date'                => $contract->begin_date,
        'initial_contract_period'   => $initial_contract_period,
      ];
    }

    // tri de la + récente à la + ancienne
    uasort($myContracts, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('contracts', $myContracts);
    $viewData->addData('show_suppliers', true);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('number', $translator->translate('phone' . "\004" . 'Number'));
    $viewData->addTranslation('type', $translator->translatePlural('Contract type', 'Contract types', 1));
    $viewData->addTranslation('supplier', $translator->translatePlural('Supplier', 'Suppliers', 1));
    $viewData->addTranslation('start_date', $translator->translate('Start date'));
    $viewData->addTranslation('initial_contract_period', $translator->translate('Initial contract period'));

    return $view->render($response, 'subitem/contracts.html.twig', (array)$viewData);
  }

  public function showSubSuppliers(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('suppliers')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/suppliers');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $mySuppliers = [];
    foreach ($myItem->suppliers as $supplier)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/suppliers/', $supplier->id);

      $entity = '';
      $entity_url = '';
      if ($supplier->entity !== null)
      {
        $entity = $supplier->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $supplier->entity->id);
      }

      $type = '';
      $type_url = '';
      if ($supplier->type !== null)
      {
        $type = $supplier->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/suppliertypes/', $supplier->type->id);
      }

      $mySuppliers[$supplier->id] = [
        'name'           => $supplier->name,
        'url'            => $url,
        'entity'         => $entity,
        'entity_url'     => $entity_url,
        'type'           => $type,
        'type_url'       => $type_url,
        'phone'          => $supplier->phonenumber,
        'fax'            => $supplier->fax,
        'website'        => $supplier->website,
      ];
    }

    // tri de la + récente à la + ancienne
    uasort($mySuppliers, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('suppliers', $mySuppliers);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('type', $translator->translatePlural('Third party type', 'Third party types', 1));
    $viewData->addTranslation('phone', $translator->translatePlural('Phone', 'Phones', 1));
    $viewData->addTranslation('fax', $translator->translate('Fax'));
    $viewData->addTranslation('website', $translator->translate('Website'));

    return $view->render($response, 'subitem/suppliers.html.twig', (array)$viewData);
  }

  public function showSubSoftwares(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('softwareversions')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/softwares');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $softwares = [];
    foreach ($myItem->softwareversions as $softwareversion)
    {
      $softwareversion_url = $this->genereRootUrl2Link($rootUrl2, '/softwareversions/', $softwareversion->id);

      $software_url = $this->genereRootUrl2Link($rootUrl2, '/softwares/', $softwareversion->software->id);

      $softwares[] = [
        'id'        => $softwareversion->id,
        'name'      => $softwareversion->name,
        'url'       => $softwareversion_url,
        'software'  => [
          'id'    => $softwareversion->software->id,
          'name'  => $softwareversion->software->name,
          'url'   => $software_url,
        ]
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('softwares', $softwares);
    $viewData->addData('show', 'default');

    $viewData->addTranslation('software', $translator->translatePlural('Software', 'Software', 1));
    $viewData->addTranslation('version', $translator->translatePlural('Version', 'Versions', 1));

    return $view->render($response, 'subitem/softwares.html.twig', (array)$viewData);
  }

  public function showSubOperatingSystem(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('operatingsystems')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/operatingsystem');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $operatingsystem = [];
    foreach ($myItem->operatingsystems as $os)
    {
      $osa = \App\Models\Operatingsystemarchitecture::find($os->pivot->operatingsystemarchitecture_id);
      $osv = \App\Models\Operatingsystemversion::find($os->pivot->operatingsystemversion_id);
      $ossp = \App\Models\Operatingsystemservicepack::find($os->pivot->operatingsystemservicepack_id);
      $oskv = \App\Models\Operatingsystemkernelversion::find($os->pivot->operatingsystemkernelversion_id);
      $ose = \App\Models\Operatingsystemedition::find($os->pivot->operatingsystemedition_id);
      $osln = $os->pivot->license_number;
      $oslid = $os->pivot->licenseid;
      $osid = $os->pivot->installationdate;
      $oswo = $os->pivot->winowner;
      $oswc = $os->pivot->wincompany;
      $osoc = $os->pivot->oscomment;
      $oshid = $os->pivot->hostid;

      $architecture = '';
      if ($osa !== null)
      {
        $architecture = $osa->name;
      }
      $version = '';
      if ($osv !== null)
      {
        $version = $osv->name;
      }
      $servicepack = '';
      if ($ossp !== null)
      {
        $servicepack = $ossp->name;
      }
      $kernelversion = '';
      if ($oskv !== null)
      {
        $kernelversion = $oskv->name;
      }
      $edition = '';
      if ($ose !== null)
      {
        $edition = $ose->name;
      }
      $license_number = '';
      if ($osln !== null)
      {
        $license_number = $osln;
      }
      $licenseid = '';
      if ($oslid !== null)
      {
        $licenseid = $oslid;
      }
      $installationdate = '';
      if ($osid !== null)
      {
        $installationdate = $osid;
      }
      $winowner = '';
      if ($oswo !== null)
      {
        $winowner = $oswo;
      }
      $wincompany = '';
      if ($oswc !== null)
      {
        $wincompany = $oswc;
      }
      $oscomment = '';
      if ($osoc !== null)
      {
        $oscomment = $osoc;
      }
      $hostid = '';
      if ($oshid !== null)
      {
        $hostid = $oshid;
      }

      $operatingsystem = [
        'id'                => $os->id,
        'name'              => $os->name,
        'architecture'      => $architecture,
        'architecture_id'   => $os->pivot->operatingsystemarchitecture_id,
        'version'           => $version,
        'version_id'        => $os->pivot->operatingsystemversion_id,
        'servicepack'       => $servicepack,
        'servicepack_id'    => $os->pivot->operatingsystemservicepack_id,
        'kernelversion'     => $kernelversion,
        'kernelversion_id'  => $os->pivot->operatingsystemkernelversion_id,
        'edition'           => $edition,
        'edition_id'        => $os->pivot->operatingsystemedition_id,
        'licensenumber'     => $license_number,
        'licenseid'         => $licenseid,
        'installationdate'  => $installationdate,
        'winowner'          => $winowner,
        'wincompany'        => $wincompany,
        'oscomment'         => $oscomment,
        'hostid'            => $hostid,
      ];
    }

    $show = '';
    if ($this->rootUrl2 != '')
    {
      $show = str_ireplace('/', '', $this->rootUrl2);
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDefs = $item->getSpecificFunction('getDefinitionOperatingSystem');
    $myItemData = [];
    if (count($operatingsystem) > 0)
    {
      $myItemData = [
        'name'            => $operatingsystem['name'],
        'architecture'    => [
          'id'    => $operatingsystem['architecture_id'],
          'name'  => $operatingsystem['architecture'],
        ],
        'kernelversion'   => [
          'id'    => $operatingsystem['kernelversion_id'],
          'name'  => $operatingsystem['kernelversion'],
        ],
        'version'         => [
          'id'    => $operatingsystem['version_id'],
          'name'  => $operatingsystem['version'],
        ],
        'servicepack'     => [
          'id'    => $operatingsystem['servicepack_id'],
          'name'  => $operatingsystem['servicepack'],
        ],
        'edition'         => [
          'id'    => $operatingsystem['edition_id'],
          'name'  => $operatingsystem['edition'],
        ],
        'licenseid'       => $operatingsystem['licenseid'],
        'licensenumber'   => $operatingsystem['licensenumber'],
      ];
    }
    $myItemDataObject = json_decode(json_encode($myItemData));

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));
    $viewData->addData('show', $show);
    $viewData->addData('operatingsystem', $operatingsystem);

    $viewData->addTranslation('entreprise', 'Entreprise');
    $viewData->addTranslation('oscomment', $translator->translate('Comments'));
    $viewData->addTranslation('hostid', 'HostID');
    $viewData->addTranslation('owner', 'Propriétaire');
    $viewData->addTranslation('install_date', $translator->translate('Installation date'));

    return $view->render($response, 'subitem/operatingsystems.html.twig', (array)$viewData);
  }

  public function showSubItil(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('tickets', 'problems', 'changes')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/itil');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $tickets = [];
    foreach ($myItem->tickets as $ticket)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/tickets/', $ticket->id);

      $status = $this->getStatusArray()[$ticket->status];

      $entity = '';
      $entity_url = '';
      if ($ticket->entity !== null)
      {
        $entity = $ticket->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $ticket->entity->id);
      }

      $priority = $this->getPriorityArray()[$ticket->priority];

      $requesters = [];
      if ($ticket->requester !== null)
      {
        foreach ($ticket->requester as $requester)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($ticket->requestergroup !== null)
      {
        foreach ($ticket->requestergroup as $requestergroup)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($ticket->technician !== null)
      {
        foreach ($ticket->technician as $technician)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($ticket->techniciangroup !== null)
      {
        foreach ($ticket->techniciangroup as $techniciangroup)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $techniciangroup->completename,
          ];
        }
      }

      $associated_items = [];
      $item4 = new \App\Models\ItemTicket();
      $myItem4 = $item4::where('ticket_id', $ticket->id)->get();
      if ($myItem4 !== null)
      {
        foreach ($myItem4 as $val)
        {
          $item5 = new $val->item_type();
          $myItem5 = $item5->find($val->item_id);
          if ($myItem5 !== null)
          {
            $type5_fr = $item5->getTitle();
            $type5 = $item5->getTable();

            $name5 = $myItem5->name;

            $url5 = $this->genereRootUrl2Link($rootUrl2, '/' . $type5 . '/', $myItem5->id);

            if ($type5_fr != '')
            {
              $type5_fr = $type5_fr . ' - ';
            }

            $associated_items[] = [
              'type'     => $type5_fr,
              'name'     => $name5,
              'url'      => $url5,
            ];
          }
        }

        if (empty($associated_items))
        {
          $associated_items[] = [
            'type'     => '',
            'name'     => $translator->translate('General'),
            'url'      => '',
          ];
        }
      }

      $category = '';
      $category_url = '';
      if ($ticket->category !== null)
      {
        $category = $ticket->category->name;
        $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $ticket->category->id);
      }

      $planification = 0; // TODO

      $tickets[$ticket->id] = [
        'url'               => $url,
        'status'            => $status,
        'date'              => $ticket->date,
        'last_update'       => $ticket->updated_at,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'priority'          => $priority,
        'requesters'        => $requesters,
        'technicians'       => $technicians,
        'associated_items'  => $associated_items,
        'title'             => $ticket->name,
        'category'          => $category,
        'category_url'      => $category_url,
        'planification'     => $planification,
      ];
    }

    $problems = [];
    foreach ($myItem->problems as $problem)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/problems/', $problem->id);

      $status = $this->getStatusArray()[$problem->status];

      $entity = '';
      $entity_url = '';
      if ($problem->entity !== null)
      {
        $entity = $problem->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $problem->entity->id);
      }

      $priority = $this->getPriorityArray()[$problem->priority];

      $requesters = [];
      if ($problem->requester !== null)
      {
        foreach ($problem->requester as $requester)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($problem->requestergroup !== null)
      {
        foreach ($problem->requestergroup as $requestergroup)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($problem->technician !== null)
      {
        foreach ($problem->technician as $technician)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($problem->techniciangroup !== null)
      {
        foreach ($problem->techniciangroup as $techniciangroup)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $techniciangroup->completename,
          ];
        }
      }

      $category = '';
      $category_url = '';
      if ($problem->category !== null)
      {
        $category = $problem->category->name;
        $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $problem->category->id);
      }

      $planification = 0; // TODO

      $problems[$problem->id] = [
        'url'               => $url,
        'status'            => $status,
        'date'              => $problem->date,
        'last_update'       => $problem->updated_at,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'priority'          => $priority,
        'requesters'        => $requesters,
        'technicians'       => $technicians,
        'title'             => $problem->name,
        'category'          => $category,
        'category_url'      => $category_url,
        'planification'     => $planification,
      ];
    }

    $changes = [];
    foreach ($myItem->changes as $change)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/changes/', $change->id);

      $status = $this->getStatusArray()[$change->status];

      $entity = '';
      $entity_url = '';
      if ($change->entity !== null)
      {
        $entity = $change->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $change->entity->id);
      }

      $priority = $this->getPriorityArray()[$change->priority];

      $requesters = [];
      if ($change->requester !== null)
      {
        foreach ($change->requester as $requester)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($change->requestergroup !== null)
      {
        foreach ($change->requestergroup as $requestergroup)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url' => $requester_url,
            'name' => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($change->technician !== null)
      {
        foreach ($change->technician as $technician)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($change->techniciangroup !== null)
      {
        foreach ($change->techniciangroup as $techniciangroup)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url' => $technician_url,
            'name' => $techniciangroup->completename,
          ];
        }
      }

      $category = '';
      $category_url = '';
      if ($change->itilcategorie !== null)
      {
        $category = $change->itilcategorie->name;
        $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $change->itilcategorie->id);
      }

      $planification = 0; // TODO

      $changes[$change->id] = [
        'url'               => $url,
        'status'            => $status,
        'date'              => $change->date,
        'last_update'       => $change->updated_at,
        'entity'            => $entity,
        'entity_url'        => $entity_url,
        'priority'          => $priority,
        'requesters'        => $requesters,
        'technicians'       => $technicians,
        'title'             => $change->name,
        'category'          => $category,
        'category_url'      => $category_url,
        'planification'     => $planification,
      ];
    }

    $tickets_link_elements = [];
    $problems_link_elements = [];
    $changes_link_elements = [];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('tickets', $tickets);
    $viewData->addData('problems', $problems);
    $viewData->addData('changes', $changes);
    $viewData->addData('tickets_link_elements', $tickets_link_elements);
    $viewData->addData('problems_link_elements', $problems_link_elements);
    $viewData->addData('changes_link_elements', $changes_link_elements);

    $viewData->addTranslation('tickets', $translator->translatePlural('Ticket', 'Tickets', 2));
    $viewData->addTranslation('problems', $translator->translatePlural('Problem', 'Problems', 2));
    $viewData->addTranslation('changes', $translator->translatePlural('Change', 'Changes', 2));
    $viewData->addTranslation(
      'tickets_link_elements',
      $translator->translatePlural('Ticket on linked items', 'Tickets on linked items', 1)
    );
    $viewData->addTranslation('problems_link_elements', $translator->translate('Problems on linked items'));
    $viewData->addTranslation('changes_link_elements', $translator->translate('Changes on linked items'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_ticket_found', $translator->translate('No ticket found.'));
    $viewData->addTranslation('no_problem_found', $translator->translate('No problem found.'));
    $viewData->addTranslation('no_change_found', $translator->translate('No change found.'));

    return $view->render($response, 'subitem/itil.html.twig', (array)$viewData);
  }

  public static function getStatusArray()
  {
    global $translator;
    return [
      1 => [
        'title' => $translator->translate('New'),
        'displaystyle' => 'marked',
        'color' => 'olive',
        'icon'  => 'book open',
      ],
      2 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (assigned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'book reader',
      ],
      3 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (planned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'business time',
      ],
      4 => [
        'title' => $translator->translate('Pending'),
        'displaystyle' => 'marked',
        'color' => 'grey',
        'icon'  => 'pause',
      ],
      5 => [
        'title' => $translator->translate('Solved'),
        'displaystyle' => 'marked',
        'color' => 'purple',
        'icon'  => 'vote yea',
      ],
      6 => [
        'title' => $translator->translate('Closed'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      7 => [
        'title' => $translator->translate('status' . "\004" . 'Accepted'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      8 => [
        'title' => $translator->translate('Review'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      9 => [
        'title' => $translator->translate('Evaluation'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      10 => [
        'title' => $translator->translatePlural('Approval', 'Approvals', 1),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      11 => [
        'title' => $translator->translate('change' . "\004" . 'Testing'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
      12 => [
        'title' => $translator->translate('Qualification'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
    ];
  }

  public static function getPriorityArray()
  {
    global $translator;
    return [
      6 => [
        'title' => $translator->translate('priority' . "\004" . 'Major'),
        'color' => 'gsitmajor',
        'icon'  => 'fire extinguisher',
      ],
      5 => [
        'title' => $translator->translate('priority' . "\004" . 'Very high'),
        'color' => 'gsitveryhigh',
        'icon'  => 'fire alternate',
      ],
      4 => [
        'title' => $translator->translate('priority' . "\004" . 'High'),
        'color' => 'gsithigh',
        'icon'  => 'fire',
      ],
      3 => [
        'title' => $translator->translate('priority' . "\004" . 'Medium'),
        'color' => 'gsitmedium',
        'icon'  => 'volume up',
      ],
      2 => [
        'title' => $translator->translate('priority' . "\004" . 'Low'),
        'color' => 'gsitlow',
        'icon'  => 'volume down',
      ],
      1 => [
        'title' => $translator->translate('priority' . "\004" . 'Very low'),
        'color' => 'gsitverylow',
        'icon'  => 'volume off',
      ],
    ];
  }

  public function showSubComponents(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with(
      'memories',
      'firmwares',
      'processors',
      'harddrives',
      'batteries',
      'soundcards',
      'controllers',
      'powersupplies',
      'sensors',
      'devicepcis',
      'devicegenerics',
      'devicenetworkcards',
      'devicesimcards',
      'devicemotherboards',
      'devicecases',
      'devicegraphiccards',
      'devicedrives'
    )->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/components');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);


    $colorTab = [];
    $colorTab['memories'] = 'red';
    $colorTab['firmwares'] = 'orange';
    $colorTab['processors'] = 'olive';
    $colorTab['harddrives'] = 'teal';
    $colorTab['batteries'] = 'blue';
    $colorTab['soundcards'] = 'purple';
    $colorTab['controllers'] = 'red';
    $colorTab['powersupplies'] = 'orange';
    $colorTab['sensors'] = 'olive';
    $colorTab['devicepcis'] = 'teal';
    $colorTab['devicegenerics'] = 'blue';
    $colorTab['devicenetworkcards'] = 'purple';
    $colorTab['devicesimcards'] = 'brown';
    $colorTab['devicemotherboards'] = 'red';
    $colorTab['devicecases'] = 'orange';
    $colorTab['devicegraphiccards'] = 'olive';
    $colorTab['devicedrives'] = 'teal';

    $myMemories = [];
    foreach ($myItem->memories as $memory)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($memory->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($memory->manufacturer !== null)
      {
        $manufacturer = $memory->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $memory->manufacturer->id
        );
      }

      $type = '';
      $type_url = '';
      if ($memory->type !== null)
      {
        $type = $memory->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/devicememorytype/', $memory->type->id);
      }

      $serial = $memory->pivot->serial;

      $otherserial = $memory->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($memory->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($memory->documents !== null)
      {
        foreach ($memory->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $memory->pivot->id,
          'item_type' => 'App\\Models\\Item_devicememory'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myMemories[] = [
        'name'                => $memory->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'type'                => $type,
        'type_url'            => $type_url,
        'frequence'           => $memory->frequence,
        'size'                => $memory->pivot->size,
        'busID'               => $memory->pivot->busID,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['memories'],
      ];
    }

    $myFirmwares = [];
    foreach ($myItem->firmwares as $firmware)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($firmware->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($firmware->manufacturer !== null)
      {
        $manufacturer = $firmware->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $firmware->manufacturer->id
        );
      }

      $type = '';
      $type_url = '';
      if ($firmware->type !== null)
      {
        $type = $firmware->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/devices/devicefirmwaretypes/', $firmware->type->id);
      }

      $serial = $firmware->pivot->serial;

      $otherserial = $firmware->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($firmware->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($firmware->documents !== null)
      {
        foreach ($firmware->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $firmware->pivot->id,
          'item_type' => 'App\\Models\\Item_devicefirmware'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myFirmwares[] = [
        'name'                => $firmware->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'type'                => $type,
        'type_url'            => $type_url,
        'version'             => $firmware->version,
        'date'                => $firmware->date,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['firmwares'],
      ];
    }

    $myProcessors = [];
    foreach ($myItem->processors as $processor)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($processor->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($processor->manufacturer !== null)
      {
        $manufacturer = $processor->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $processor->manufacturer->id
        );
      }

      $serial = $processor->pivot->serial;

      $otherserial = $processor->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($processor->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $processor->pivot->busID;

      $documents = [];
      if ($processor->documents !== null)
      {
        foreach ($processor->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $processor->pivot->id,
          'item_type' => 'App\\Models\\Item_deviceprocessor'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myProcessors[] = [
        'name'                => $processor->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'frequency'           => $processor->pivot->frequency,
        'nbcores'             => $processor->pivot->nbcores,
        'nbthreads'           => $processor->pivot->nbthreads,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['processors'],
      ];
    }

    $myHarddrives = [];
    foreach ($myItem->harddrives as $harddrive)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($harddrive->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($harddrive->manufacturer !== null)
      {
        $manufacturer = $harddrive->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $harddrive->manufacturer->id
        );
      }

      $interface = '';
      $interface_url = '';
      if ($harddrive->interface !== null)
      {
        $interface = $harddrive->interface->name;
        $interface_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/interfacetypes/', $harddrive->interface->id);
      }

      $serial = $harddrive->pivot->serial;

      $otherserial = $harddrive->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($harddrive->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $harddrive->pivot->busID;

      $documents = [];
      if ($harddrive->documents !== null)
      {
        foreach ($harddrive->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $harddrive->pivot->id,
          'item_type' => 'App\\Models\\Item_deviceharddrive'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myHarddrives[] = [
        'name'                => $harddrive->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'rpm'                 => $harddrive->rpm,
        'cache'               => $harddrive->cache,
        'interface'           => $interface,
        'interface_url'       => $interface_url,
        'capacity'            => $harddrive->pivot->capacity,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['harddrives'],
      ];
    }

    $myBatteries = [];
    foreach ($myItem->batteries as $battery)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($battery->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($battery->manufacturer !== null)
      {
        $manufacturer = $battery->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $battery->manufacturer->id
        );
      }

      $type = '';
      $type_url = '';
      if ($battery->type !== null)
      {
        $type = $battery->type->name;
        $type_url = $this->genereRootUrl2Link($rootUrl2, '/devices/devicebatterytypes/', $battery->type->id);
      }

      $serial = $battery->pivot->serial;

      $otherserial = $battery->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($battery->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($battery->documents !== null)
      {
        foreach ($battery->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $battery->pivot->id,
          'item_type' => 'App\\Models\\Item_devicebattery'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myBatteries[] = [
        'name'                => $battery->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'type'                => $type,
        'type_url'            => $type_url,
        'voltage'             => $battery->voltage,
        'capacity'            => $battery->capacity,
        'manufacturing_date'  => $battery->pivot->manufacturing_date,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['batteries'],
      ];
    }

    $mySoundcards = [];
    foreach ($myItem->soundcards as $soundcard)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($soundcard->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($soundcard->manufacturer !== null)
      {
        $manufacturer = $soundcard->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $soundcard->manufacturer->id
        );
      }

      $serial = $soundcard->pivot->serial;

      $otherserial = $soundcard->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($soundcard->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $soundcard->pivot->busID;

      $documents = [];
      if ($soundcard->documents !== null)
      {
        foreach ($soundcard->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $soundcard->pivot->id,
          'item_type' => 'App\\Models\\Item_devicesoundcard'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $mySoundcards[] = [
        'name'                => $soundcard->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'type'                => $soundcard->type,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['soundcards'],
      ];
    }

    $myControllers = [];
    foreach ($myItem->controllers as $controller)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($controller->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($controller->manufacturer !== null)
      {
        $manufacturer = $controller->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $controller->manufacturer->id
        );
      }

      $interface = '';
      $interface_url = '';
      if ($controller->interface !== null)
      {
        $interface = $controller->interface->name;
        $interface_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/interfacetypes/', $controller->interface->id);
      }

      $serial = $controller->pivot->serial;

      $otherserial = $controller->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($controller->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $controller->pivot->busID;

      $documents = [];
      if ($controller->documents !== null)
      {
        foreach ($controller->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $controller->pivot->id,
          'item_type' => 'App\\Models\\Item_devicecontrol'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myControllers[] = [
        'name'                => $controller->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'interface'           => $interface,
        'interface_url'       => $interface_url,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['controllers'],
      ];
    }

    $myPowerSupplies = [];
    foreach ($myItem->powersupplies as $powersupply)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($powersupply->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($powersupply->manufacturer !== null)
      {
        $manufacturer = $powersupply->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $powersupply->manufacturer->id
        );
      }

      $serial = $powersupply->pivot->serial;

      $otherserial = $powersupply->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($powersupply->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($powersupply->documents !== null)
      {
        foreach ($powersupply->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $powersupply->pivot->id,
          'item_type' => 'App\\Models\\Item_devicepowersupply'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myPowerSupplies[] = [
        'name'                => $powersupply->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['powersupplies'],
      ];
    }

    $mySensors = [];
    foreach ($myItem->sensors as $sensor)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($sensor->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($sensor->manufacturer !== null)
      {
        $manufacturer = $sensor->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $sensor->manufacturer->id
        );
      }

      $serial = $sensor->pivot->serial;

      $otherserial = $sensor->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($sensor->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($sensor->documents !== null)
      {
        foreach ($sensor->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $sensor->pivot->id,
          'item_type' => 'App\\Models\\Item_devicesensor'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $mySensors[] = [
        'name'                => $sensor->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['sensors'],
      ];
    }

    $myDevicepcis = [];
    foreach ($myItem->devicepcis as $devicepci)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($devicepci->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $serial = $devicepci->pivot->serial;

      $otherserial = $devicepci->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($devicepci->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $devicepci->pivot->busID;

      $documents = [];
      if ($devicepci->documents !== null)
      {
        foreach ($devicepci->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $devicepci->pivot->id,
          'item_type' => 'App\\Models\\Item_devicepci'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myDevicepcis[] = [
        'name'            => $devicepci->name,
        'location'        => $location,
        'location_url'    => $location_url,
        'serial'          => $serial,
        'otherserial'     => $otherserial,
        'state'           => $state,
        'state_url'       => $state_url,
        'busID'           => $busID,
        'documents'       => $documents,
        'color'           => $colorTab['devicepcis'],
      ];
    }

    $myDevicegenerics = [];
    foreach ($myItem->devicegenerics as $devicegeneric)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($devicegeneric->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicegeneric->manufacturer !== null)
      {
        $manufacturer = $devicegeneric->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicegeneric->manufacturer->id
        );
      }

      $serial = $devicegeneric->pivot->serial;

      $otherserial = $devicegeneric->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($devicegeneric->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($devicegeneric->documents !== null)
      {
        foreach ($devicegeneric->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $devicegeneric->pivot->id,
          'item_type' => 'App\\Models\\Item_devicegeneric'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myDevicegenerics[] = [
        'name'                => $devicegeneric->name,
        'location'            => $location,
        'location_url'        => $location_url,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['devicegenerics'],
      ];
    }

    $myDevicenetworkcards = [];
    foreach ($myItem->devicenetworkcards as $devicenetworkcard)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($devicenetworkcard->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicenetworkcard->manufacturer !== null)
      {
        $manufacturer = $devicenetworkcard->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicenetworkcard->manufacturer->id
        );
      }

      $serial = $devicenetworkcard->pivot->serial;

      $otherserial = $devicenetworkcard->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($devicenetworkcard->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $devicenetworkcard->pivot->busID;

      $speed = $devicenetworkcard->bandwidth;

      $documents = [];
      if ($devicenetworkcard->documents !== null)
      {
        foreach ($devicenetworkcard->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $devicenetworkcard->pivot->id,
          'item_type' => 'App\\Models\\Item_devicenetworkcard'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $mac_address = $devicenetworkcard->pivot->mac;

      $myDevicenetworkcards[] = [
        'name'                => $devicenetworkcard->name,
        'location'            => $location,
        'location_url'        => $location_url,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'speed'               => $speed,
        'documents'           => $documents,
        'mac_address'         => $mac_address,
        'color'               => $colorTab['devicenetworkcards'],
      ];
    }

    $myDevicesimcards = [];
    foreach ($myItem->devicesimcards as $devicesimcard)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($devicesimcard->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $serial = $devicesimcard->pivot->serial;

      $otherserial = $devicesimcard->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($devicesimcard->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $line = '';
      $line_url = '';
      $find_line = \App\Models\Line::find($devicesimcard->pivot->line_id);
      if ($find_line !== null)
      {
        $line = $find_line->name;
        $line_url = $this->genereRootUrl2Link($rootUrl2, '/lines/', $find_line->id);
      }

      $msin = $devicesimcard->pivot->msin;

      $user = '';
      $user_url = '';
      $find_user = \App\Models\User::find($devicesimcard->pivot->user_id);
      if ($find_user !== null)
      {
        $user = $find_user->name;
        $user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $find_user->id);
      }

      $group = '';
      $group_url = '';
      $find_group = \App\Models\Group::find($devicesimcard->pivot->group_id);
      if ($find_group !== null)
      {
        $group = $find_group->name;
        $group_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $find_group->id);
      }

      $documents = [];
      if ($devicesimcard->documents !== null)
      {
        foreach ($devicesimcard->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $devicesimcard->pivot->id,
          'item_type' => 'App\\Models\\ItemDevicesimcard'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myDevicesimcards[] = [
        'name'                => $devicesimcard->name,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'line'                => $line,
        'line_url'            => $line_url,
        'msin'                => $msin,
        'user'                => $user,
        'user_url'            => $user_url,
        'group'               => $group,
        'group_url'           => $group_url,
        'documents'           => $documents,
        'color'               => $colorTab['devicesimcards'],
      ];
    }

    $myDevicemotherboards = [];
    foreach ($myItem->devicemotherboards as $devicemotherboard)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($devicemotherboard->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicemotherboard->manufacturer !== null)
      {
        $manufacturer = $devicemotherboard->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicemotherboard->manufacturer->id
        );
      }

      $serial = $devicemotherboard->pivot->serial;

      $otherserial = $devicemotherboard->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($devicemotherboard->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($devicemotherboard->documents !== null)
      {
        foreach ($devicemotherboard->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $devicemotherboard->pivot->id,
          'item_type' => 'App\\Models\\Item_devicemotherboard'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myDevicemotherboards[] = [
        'name'                => $devicemotherboard->name,
        'location'            => $location,
        'location_url'        => $location_url,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['devicemotherboards'],
      ];
    }

    $myDevicecases = [];
    foreach ($myItem->devicecases as $devicecase)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($devicecase->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicecase->manufacturer !== null)
      {
        $manufacturer = $devicecase->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicecase->manufacturer->id
        );
      }

      $serial = $devicecase->pivot->serial;

      $otherserial = $devicecase->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($devicecase->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $documents = [];
      if ($devicecase->documents !== null)
      {
        foreach ($devicecase->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $devicecase->pivot->id,
          'item_type' => 'App\\Models\\Item_devicecase'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myDevicecases[] = [
        'name'                => $devicecase->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'documents'           => $documents,
        'color'               => $colorTab['devicecases'],
      ];
    }

    $myDevicegraphiccards = [];
    foreach ($myItem->devicegraphiccards as $devicegraphiccard)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($devicegraphiccard->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicegraphiccard->manufacturer !== null)
      {
        $manufacturer = $devicegraphiccard->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicegraphiccard->manufacturer->id
        );
      }

      $interface = '';
      $interface_url = '';
      if ($devicegraphiccard->interface !== null)
      {
        $interface = $devicegraphiccard->interface->name;
        $interface_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/interfacetypes/',
          $devicegraphiccard->interface->id
        );
      }

      $serial = $devicegraphiccard->pivot->serial;

      $otherserial = $devicegraphiccard->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($devicegraphiccard->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $devicegraphiccard->pivot->busID;

      $documents = [];
      if ($devicegraphiccard->documents !== null)
      {
        foreach ($devicegraphiccard->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $devicegraphiccard->pivot->id,
          'item_type' => 'App\\Models\\Item_devicegraphiccard'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myDevicegraphiccards[] = [
        'name'                => $devicegraphiccard->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'interface'           => $interface,
        'interface_url'       => $interface_url,
        'chipset'             => $devicegraphiccard->chipset,
        'memory'              => $devicegraphiccard->pivot->memory,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['devicegraphiccards'],
      ];
    }

    $myDevicedrives = [];
    foreach ($myItem->devicedrives as $devicedrive)
    {
      $location = '';
      $location_url = '';
      $loc = \App\Models\Location::find($devicedrive->pivot->location_id);
      if ($loc !== null)
      {
        $location = $loc->name;
        $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $loc->id);
      }

      $manufacturer = '';
      $manufacturer_url = '';
      if ($devicedrive->manufacturer !== null)
      {
        $manufacturer = $devicedrive->manufacturer->name;
        $manufacturer_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/manufacturers/',
          $devicedrive->manufacturer->id
        );
      }

      $write = $devicedrive->is_writer;
      if ($write == 1)
      {
        $write_val = $translator->translate('Yes');
      }
      else
      {
        $write_val = $translator->translate('No');
      }

      $speed = $devicedrive->speed;

      $interface = '';
      $interface_url = '';
      if ($devicedrive->interface !== null)
      {
        $interface = $devicedrive->interface->name;
        $interface_url = $this->genereRootUrl2Link(
          $rootUrl2,
          '/dropdowns/interfacetypes/',
          $devicedrive->interface->id
        );
      }

      $serial = $devicedrive->pivot->serial;

      $otherserial = $devicedrive->pivot->otherserial;

      $state = '';
      $state_url = '';
      $status = \App\Models\State::find($devicedrive->pivot->state_id);
      if ($status !== null)
      {
        $state = $status->name;
        $state_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/states/', $status->id);
      }

      $busID = $devicedrive->pivot->busID;

      $documents = [];
      if ($devicedrive->documents !== null)
      {
        foreach ($devicedrive->documents as $document)
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

          $documents[$document->id] = [
            'name'  => $document->name,
            'url'   => $url,
          ];
        }

        $item5 = new \App\Models\Documentitem();
        $myItem5 = $item5::where([
          'item_id' => $devicedrive->pivot->id,
          'item_type' => 'App\\Models\\Item_devicedrive'
        ])->get();
        if ($myItem5 !== null)
        {
          foreach ($myItem5 as $current_documentitem)
          {
            $item6 = new \App\Models\Document();
            $myItem6 = $item6::where('id', $current_documentitem->document_id)->get();
            if ($myItem6 !== null)
            {
              foreach ($myItem6 as $current_document)
              {
                $url = $this->genereRootUrl2Link($rootUrl2, '/documents/', $current_document->id);

                $documents[$current_document->id] = [
                  'name'  => $current_document->name,
                  'url'   => $url,
                ];
              }
            }
          }
        }
      }

      $myDevicedrives[] = [
        'name'                => $devicedrive->name,
        'manufacturer'        => $manufacturer,
        'manufacturer_url'    => $manufacturer_url,
        'write'               => $write,
        'write_val'           => $write_val,
        'speed'               => $speed,
        'interface'           => $interface,
        'interface_url'       => $interface_url,
        'location'            => $location,
        'location_url'        => $location_url,
        'serial'              => $serial,
        'otherserial'         => $otherserial,
        'state'               => $state,
        'state_url'           => $state_url,
        'busID'               => $busID,
        'documents'           => $documents,
        'color'               => $colorTab['devicedrives'],
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('memories', $myMemories);
    $viewData->addData('firmwares', $myFirmwares);
    $viewData->addData('processors', $myProcessors);
    $viewData->addData('harddrives', $myHarddrives);
    $viewData->addData('batteries', $myBatteries);
    $viewData->addData('soundcards', $mySoundcards);
    $viewData->addData('controllers', $myControllers);
    $viewData->addData('powersupplies', $myPowerSupplies);
    $viewData->addData('sensors', $mySensors);
    $viewData->addData('devicepcis', $myDevicepcis);
    $viewData->addData('devicegenerics', $myDevicegenerics);
    $viewData->addData('devicenetworkcards', $myDevicenetworkcards);
    $viewData->addData('devicesimcards', $myDevicesimcards);
    $viewData->addData('devicemotherboards', $myDevicemotherboards);
    $viewData->addData('devicecases', $myDevicecases);
    $viewData->addData('devicegraphiccards', $myDevicegraphiccards);
    $viewData->addData('devicedrives', $myDevicedrives);

    $viewData->addTranslation('memory', $translator->translatePlural('Memory', 'Memories', 1));
    $viewData->addTranslation('manufacturer', $translator->translatePlural('Manufacturer', 'Manufacturers', 1));
    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('frequence', $translator->translate('Frequency'));
    $viewData->addTranslation(
      'size',
      sprintf('%1$s (%2$s)', $translator->translate('Size'), $translator->translate('Mio'))
    );
    $viewData->addTranslation('serial', $translator->translate('Serial number'));
    $viewData->addTranslation('location', $translator->translatePlural('Location', 'Locations', 1));
    $viewData->addTranslation('busID', $translator->translate('Position of the device on its bus'));
    $viewData->addTranslation('firmware', $translator->translatePlural('Firmware', 'Firmware', 1));
    $viewData->addTranslation('version', $translator->translatePlural('Version', 'Versions', 1));
    $viewData->addTranslation('install_date', $translator->translate('Installation date'));
    $viewData->addTranslation('processor', $translator->translatePlural('Processor', 'Processors', 1));
    $viewData->addTranslation(
      'frequence_mhz',
      sprintf('%1$s (%2$s)', $translator->translate('Frequency'), $translator->translate('MHz'))
    );
    $viewData->addTranslation('nbcores', $translator->translate('Number of cores'));
    $viewData->addTranslation('nbthreads', $translator->translate('Number of threads'));
    $viewData->addTranslation('harddrive', $translator->translatePlural('Hard drive', 'Hard drives', 1));
    $viewData->addTranslation('rpm', $translator->translate('Rpm'));
    $viewData->addTranslation('cache', $translator->translate('Cache'));
    $viewData->addTranslation('interface', $translator->translate('Interface'));
    $viewData->addTranslation(
      'capacity',
      sprintf('%1$s (%2$s)', $translator->translate('Capacity'), $translator->translate('Mio'))
    );
    $viewData->addTranslation('battery', $translator->translatePlural('Battery', 'Batteries', 1));
    $viewData->addTranslation(
      'voltage_mv',
      sprintf('%1$s (%2$s)', $translator->translate('Voltage'), $translator->translate('mV'))
    );
    $viewData->addTranslation(
      'capacity_mwh',
      sprintf('%1$s (%2$s)', $translator->translate('Capacity'), $translator->translate('mWh'))
    );
    $viewData->addTranslation('manufacturing_date', $translator->translate('Manufacturing date'));
    $viewData->addTranslation('soundcard', $translator->translatePlural('Soundcard', 'Soundcards', 1));
    $viewData->addTranslation('controller', $translator->translatePlural('Controller', 'Controllers', 1));
    $viewData->addTranslation('documents', $translator->translatePlural('Document', 'Documents', 2));
    $viewData->addTranslation('mac_address', $translator->translate('MAC address'));
    $viewData->addTranslation('powersupply', $translator->translatePlural('Power supply', 'Power supplies', 1));
    $viewData->addTranslation('sensor', $translator->translatePlural('Sensor', 'Sensors', 1));
    $viewData->addTranslation('devicepci', $translator->translatePlural('PCI device', 'PCI devices', 1));
    $viewData->addTranslation('devicegeneric', $translator->translatePlural('Generic device', 'Generic devices', 1));
    $viewData->addTranslation('devicenetworkcard', $translator->translatePlural('Network card', 'Network cards', 1));
    $viewData->addTranslation('devicesimcard', $translator->translatePlural('Simcard', 'Simcards', 1));
    $viewData->addTranslation('devicemotherboard', $translator->translatePlural('System board', 'System boards', 1));
    $viewData->addTranslation('devicecase', $translator->translatePlural('Case', 'Cases', 1));
    $viewData->addTranslation('devicegraphiccard', $translator->translatePlural('Graphics card', 'Graphics cards', 1));
    $viewData->addTranslation('devicedrive', $translator->translatePlural('Drive', 'Drives', 1));
    $viewData->addTranslation(
      'memory_mio',
      sprintf('%1$s (%2$s)', $translator->translatePlural('Memory', 'Memories', 1), $translator->translate('Mio'))
    );
    $viewData->addTranslation('chipset', $translator->translate('Chipset'));
    $viewData->addTranslation('write', $translator->translate('Write'));
    $viewData->addTranslation('speed', $translator->translate('Speed'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('msin', $translator->translate('Mobile Subscriber Identification Number'));
    $viewData->addTranslation('user', $translator->translatePlural('User', 'Users', 1));
    $viewData->addTranslation('group', $translator->translatePlural('Group', 'Groups', 1));
    $viewData->addTranslation('flow', $translator->translate('Flow'));
    $viewData->addTranslation('line', $translator->translatePlural('Line', 'Lines', 1));

    return $view->render($response, 'subitem/components.html.twig', (array)$viewData);
  }

  public function showSubVolumes(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('volumes')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/volumes');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myVolumes = [];
    foreach ($myItem->volumes as $volume)
    {
      if ($volume->is_dynamic == 1)
      {
        $auto_val = $translator->translate('Yes');
      }
      else
      {
        $auto_val = $translator->translate('No');
      }

      $filesystem = '';
      $filesystem_url = '';
      if ($volume->filesystem !== null)
      {
        $filesystem = $volume->filesystem->name;
        $filesystem_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/filesystems/', $volume->filesystem->id);
      }

      $usedpercent = 100;
      if ($volume->totalsize > 0)
      {
        $usedpercent = 100 - round(($volume->freesize / $volume->totalsize) * 100);
      }

      $encryption_status_val = '';
      if ($volume->encryption_status == 0)
      {
        $encryption_status_val = $translator->translate('Not encrypted');
      }
      if ($volume->encryption_status == 1)
      {
        $encryption_status_val = $translator->translate('Encrypted');
      }
      if ($volume->encryption_status == 2)
      {
        $encryption_status_val = $translator->translate('Partially encrypted');
      }

      $myVolumes[] = [
        'name'                      => $volume->name,
        'auto'                      => $volume->is_dynamic,
        'auto_val'                  => $auto_val,
        'device'                    => $volume->device,
        'mountpoint'                => $volume->mountpoint,
        'filesystem'                => $filesystem,
        'filesystem_url'            => $filesystem_url,
        'totalsize'                 => $volume->totalsize,
        'freesize'                  => $volume->freesize,
        'usedpercent'               => $usedpercent,
        'encryption_status'         => $volume->encryption_status,
        'encryption_status_val'     => $encryption_status_val,
        'encryption_tool'           => $volume->encryption_tool,
        'encryption_algorithm'      => $volume->encryption_algorithm,
        'encryption_type'           => $volume->encryption_type,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('volumes', $myVolumes);

    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('device', $translator->translate('Partition'));
    $viewData->addTranslation('mountpoint', $translator->translate('Mount point'));
    $viewData->addTranslation('filesystem', $translator->translatePlural('File system', 'File systems', 1));
    $viewData->addTranslation('totalsize', $translator->translate('Global size'));
    $viewData->addTranslation('freesize', $translator->translate('Free size'));
    $viewData->addTranslation('encryption', $translator->translate('Encryption'));
    $viewData->addTranslation('encryption_algorithm', $translator->translate('Encryption algorithm'));
    $viewData->addTranslation('encryption_tool', $translator->translate('Encryption tool'));
    $viewData->addTranslation('encryption_type', $translator->translate('Encryption type'));
    $viewData->addTranslation('usedpercent', 'Pourcentage utilisé');

    return $view->render($response, 'subitem/volumes.html.twig', (array)$viewData);
  }

  public function showSubItilTicketsCreated(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Ticket();
    $myItem2 = $item2::with('requester', 'requestergroup', 'technician', 'techniciangroup')->get();

    $rootUrl = $this->genereRootUrl($request, '/tickets');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $tickets = [];
    foreach ($myItem2 as $ticket)
    {
      $add_to_tab = false;

      $url = $this->genereRootUrl2Link($rootUrl2, '/tickets/', $ticket->id);

      $status = $this->getStatusArray()[$ticket->status];

      $entity = '';
      $entity_url = '';
      if ($ticket->entity !== null)
      {
        $entity = $ticket->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $ticket->entity->id);
      }

      $priority = $this->getPriorityArray()[$ticket->priority];

      $requesters = [];
      if ($ticket->requester !== null)
      {
        foreach ($ticket->requester as $requester)
        {
          if ($this->choose == 'users')
          {
            if ($requester->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($ticket->requestergroup !== null)
      {
        foreach ($ticket->requestergroup as $requestergroup)
        {
          if ($this->choose == 'groups')
          {
            if ($requestergroup->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($ticket->technician !== null)
      {
        foreach ($ticket->technician as $technician)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($ticket->techniciangroup !== null)
      {
        foreach ($ticket->techniciangroup as $techniciangroup)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $techniciangroup->completename,
          ];
        }
      }

      $associated_items = [];
      $item4 = new \App\Models\ItemTicket();
      $myItem4 = $item4::where('ticket_id', $ticket->id)->get();
      if ($myItem4 !== null)
      {
        foreach ($myItem4 as $val)
        {
          $item5 = new $val->item_type();
          $myItem5 = $item5->find($val->item_id);
          if ($myItem5 !== null)
          {
            $type5_fr = $item5->getTitle();
            $type5 = $item5->getTable();

            $name5 = $myItem5->name;

            $url5 = $this->genereRootUrl2Link($rootUrl2, '/' . $type5 . '/', $myItem5->id);

            if ($type5_fr != '')
            {
              $type5_fr = $type5_fr . ' - ';
            }

            $associated_items[] = [
              'type'     => $type5_fr,
              'name'     => $name5,
              'url'      => $url5,
            ];
          }
        }

        if (empty($associated_items))
        {
          $associated_items[] = [
            'type'     => '',
            'name'     => $translator->translate('General'),
            'url'      => '',
          ];
        }
      }

      $category = '';
      $category_url = '';
      if ($ticket->category !== null)
      {
        $category = $ticket->category->name;
        $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $ticket->category->id);
      }

      $planification = 0; // TODO

      if ($add_to_tab)
      {
        $tickets[$ticket->id] = [
          'url'                 => $url,
          'status'              => $status,
          'date'                => $ticket->date,
          'last_update'         => $ticket->updated_at,
          'entity'              => $entity,
          'entity_url'          => $entity_url,
          'priority'            => $priority,
          'requesters'          => $requesters,
          'technicians'         => $technicians,
          'associated_items'    => $associated_items,
          'title'               => $ticket->name,
          'category'            => $category,
          'category_url'        => $category_url,
          'planification'       => $planification,
        ];
      }
    }

    // tri de la + récente à la + ancienne
    uasort($tickets, function ($a, $b)
    {
      return $a['last_update'] < $b['last_update'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('tickets', $tickets);

    $viewData->addTranslation('tickets', $translator->translatePlural('Ticket', 'Tickets', 2));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_ticket_found', $translator->translate('No ticket found.'));

    return $view->render($response, 'subitem/itiltickets.html.twig', (array)$viewData);
  }

  public function showSubItilProblems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Problem();
    $myItem2 = $item2::with('requester', 'requestergroup', 'technician', 'techniciangroup')->get();

    $rootUrl = $this->genereRootUrl($request, '/problems');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $problems = [];
    foreach ($myItem2 as $problem)
    {
      $add_to_tab = false;

      $url = $this->genereRootUrl2Link($rootUrl2, '/problems/', $problem->id);

      $status = $this->getStatusArray()[$problem->status];

      $entity = '';
      $entity_url = '';
      if ($problem->entity !== null)
      {
        $entity = $problem->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $problem->entity->id);
      }

      $priority = $this->getPriorityArray()[$problem->priority];

      $requesters = [];
      if ($problem->requester !== null)
      {
        foreach ($problem->requester as $requester)
        {
          if ($this->choose == 'users')
          {
            if ($requester->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($problem->requestergroup !== null)
      {
        foreach ($problem->requestergroup as $requestergroup)
        {
          if ($this->choose == 'groups')
          {
            if ($requestergroup->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($problem->technician !== null)
      {
        foreach ($problem->technician as $technician)
        {
          if ($this->choose == 'users')
          {
            if ($technician->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($problem->techniciangroup !== null)
      {
        foreach ($problem->techniciangroup as $techniciangroup)
        {
          if ($this->choose == 'groups')
          {
            if ($techniciangroup->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $techniciangroup->completename,
          ];
        }
      }

      $category = '';
      $category_url = '';
      if ($problem->category !== null)
      {
        $category = $problem->category->name;
        $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $problem->category->id);
      }

      $planification = 0; // TODO

      if ($add_to_tab)
      {
        $problems[$problem->id] = [
          'url'                 => $url,
          'status'              => $status,
          'date'                => $problem->date,
          'last_update'         => $problem->updated_at,
          'entity'              => $entity,
          'entity_url'          => $entity_url,
          'priority'            => $priority,
          'requesters'          => $requesters,
          'technicians'         => $technicians,
          'title'               => $problem->name,
          'category'            => $category,
          'category_url'        => $category_url,
          'planification'       => $planification,
        ];
      }
    }

    // tri de la + récente à la + ancienne
    uasort($problems, function ($a, $b)
    {
      return $a['last_update'] < $b['last_update'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('problems', $problems);

    $viewData->addTranslation('problems', $translator->translatePlural('Problem', 'Problems', 2));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_problem_found', $translator->translate('No problem found.'));

    return $view->render($response, 'subitem/itilproblems.html.twig', (array)$viewData);
  }

  public function showSubItilChanges(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\Change();
    $myItem2 = $item2::with('requester', 'requestergroup', 'technician', 'techniciangroup')->get();

    $rootUrl = $this->genereRootUrl($request, '/changes');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $changes = [];
    foreach ($myItem2 as $change)
    {
      $add_to_tab = false;

      $url = $this->genereRootUrl2Link($rootUrl2, '/changes/', $change->id);

      $status = $this->getStatusArray()[$change->status];

      $entity = '';
      $entity_url = '';
      if ($change->entity !== null)
      {
        $entity = $change->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $change->entity->id);
      }

      $priority = $this->getPriorityArray()[$change->priority];

      $requesters = [];
      if ($change->requester !== null)
      {
        foreach ($change->requester as $requester)
        {
          if ($this->choose == 'users')
          {
            if ($requester->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($change->requestergroup !== null)
      {
        foreach ($change->requestergroup as $requestergroup)
        {
          if ($this->choose == 'groups')
          {
            if ($requestergroup->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($change->technician !== null)
      {
        foreach ($change->technician as $technician)
        {
          if ($this->choose == 'users')
          {
            if ($technician->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($change->techniciangroup !== null)
      {
        foreach ($change->techniciangroup as $techniciangroup)
        {
          if ($this->choose == 'groups')
          {
            if ($techniciangroup->id == $args['id'])
            {
              $add_to_tab = true;
            }
          }
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $techniciangroup->completename,
          ];
        }
      }

      $category = '';
      $category_url = '';
      if ($change->itilcategorie !== null)
      {
        $category = $change->itilcategorie->name;
        $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $change->itilcategorie->id);
      }

      $planification = 0; // TODO

      if ($add_to_tab)
      {
        $changes[$change->id] = [
          'url'                 => $url,
          'status'              => $status,
          'date'                => $change->date,
          'last_update'         => $change->updated_at,
          'entity'              => $entity,
          'entity_url'          => $entity_url,
          'priority'            => $priority,
          'requesters'          => $requesters,
          'technicians'         => $technicians,
          'title'               => $change->name,
          'category'            => $category,
          'category_url'        => $category_url,
          'planification'       => $planification,
        ];
      }
    }

    // tri de la + récente à la + ancienne
    uasort($changes, function ($a, $b)
    {
      return $a['last_update'] < $b['last_update'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('changes', $changes);

    $viewData->addTranslation('changes', $translator->translatePlural('Change', 'Changes', 2));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_change_found', $translator->translate('No change found.'));

    return $view->render($response, 'subitem/itilchanges.html.twig', (array)$viewData);
  }

  public function showSubConnections(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('connections')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/connections');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myConnections = [];
    foreach ($myItem->connections as $connection)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/computers/', $connection->id);

      $entity = '';
      $entity_url = '';
      if ($connection->entity !== null)
      {
        $entity = $connection->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $connection->entity->id);
      }

      if ($connection->pivot->is_dynamic == 1)
      {
        $auto_val = $translator->translate('Yes');
      }
      else
      {
        $auto_val = $translator->translate('No');
      }

      $myConnections[] = [
        'name'                 => $connection->name,
        'url'                  => $url,
        'auto'                 => $connection->pivot->is_dynamic,
        'auto_val'             => $auto_val,
        'entity'               => $entity,
        'entity_url'           => $entity_url,
        'serial_number'        => $connection->serial,
        'inventaire_number'    => $connection->otherserial,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('connections', $myConnections);
    $viewData->addData('show', 'default');

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));
    $viewData->addTranslation('no_connection_found', $translator->translate('Not connected.'));

    return $view->render($response, 'subitem/connections.html.twig', (array)$viewData);
  }

  public function showSubAssociatedItems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->associateditems_model();
    $myItem2 = $item2::where($this->associateditems_model_id, $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/associateditems');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myAssociatedItems = [];
    foreach ($myItem2 as $associateditem)
    {
      $associateditem_new = str_ireplace('Item_device', 'ItemDevice', $associateditem->item_type);
      $item3 = new $associateditem_new();
      $myItem3 = $item3->find($associateditem->item_id);
      if ($myItem3 !== null)
      {
        $type = $item3->getTable();
        $type_fr = $item3->getTitle();

        $name = $myItem3->name;
        if ($name == '')
        {
          $name = '(' . $myItem3->id . ')';
        }

        if (substr($type, 0, 6) == 'device')
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/devices/' . $type . '/', $myItem3->id);
        }
        else
        {
          $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);
        }

        $entity = '';
        $entity_url = '';
        if ($myItem3->entity !== null)
        {
          $entity = $myItem3->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
        }

        $serial_number = $myItem3->serial;

        $inventaire_number = $myItem3->otherserial;

        $myAssociatedItems[] = [
          'type'                 => $type_fr,
          'name'                 => $name,
          'url'                  => $url,
          'entity'               => $entity,
          'entity_url'           => $entity_url,
          'serial_number'        => $serial_number,
          'inventaire_number'    => $inventaire_number,
        ];
      }
    }

    // tri ordre alpha
    uasort($myAssociatedItems, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });
    uasort($myAssociatedItems, function ($a, $b)
    {
      return strtolower($a['type']) > strtolower($b['type']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('associateditems', $myAssociatedItems);
    $viewData->addData('show', 'computer');

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('serial_number', $translator->translate('Serial number'));
    $viewData->addTranslation('inventaire_number', $translator->translate('Inventory number'));

    return $view->render($response, 'subitem/associateditems.html.twig', (array)$viewData);
  }

  protected function canRightRead()
  {
    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      $ids = [];
      foreach ($profilerightcustoms as $custom)
      {
        if ($custom->read)
        {
          return true;
        }
      }
    }
    if ($profileright->read || $profileright->readmyitems || $profileright->readmygroupitems)
    {
      return true;
    }
    return false;
  }

  protected function canRightCreate()
  {
    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      $ids = [];
      foreach ($profilerightcustoms as $custom)
      {
        if ($custom->write)
        {
          return true;
        }
      }
    }
    if ($profileright->create)
    {
      return true;
    }
    return false;
  }

  protected function canRightUpdate()
  {
    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      $ids = [];
      foreach ($profilerightcustoms as $custom)
      {
        if ($custom->write)
        {
          return true;
        }
      }
    }
    if ($profileright->update)
    {
      return true;
    }
    return false;
  }

  public function canRightReadPrivateItem()
  {
    return false;
  }

  private function filterFieldsAllowedToWrite($model, $fields)
  {
    $item = new $model();
    $definitions = $item->getDefinitions();
    $defIds = [];
    foreach ($definitions as $definition)
    {
      $defIds[$definition['id']] = $definition['name'];
    }

    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', ltrim($this->model, '\\'))
      ->first();
    if (is_null($profileright))
    {
      return false;
    }
    if ($profileright->custom)
    {
      $profilerightcustoms = \App\Models\Profilerightcustom::where('profileright_id', $profileright->id)->get();
      $ids = [];
      foreach ($profilerightcustoms as $custom)
      {
        if (!$custom->write)
        {
          if (isset($fields[$defIds[$custom->definitionfield_id]]))
          {
            unset($fields[$defIds[$custom->definitionfield_id]]);
          }
        }
      }
    }
    return $fields;
  }

  public function showSubCosts(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('costs')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/costs');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myCosts = [];
    $myTicketCosts = [];
    $total_cost = 0;
    $total_actiontime = 0;
    $total_cost_time = 0;
    $total_cost_fixed = 0;
    $total_cost_material = 0;
    $ticket_costs_total_cost = 0;
    $ticket_costs_total_actiontime = 0;
    $ticket_costs_total_cost_time = 0;
    $ticket_costs_total_cost_fixed = 0;
    $ticket_costs_total_cost_material = 0;
    $total_costs = 0;
    foreach ($myItem->costs as $current_cost)
    {
      $budget = '';
      $budget_url = '';
      if ($current_cost->budget !== null)
      {
        $budget = $current_cost->budget->name;
        $budget_url = $this->genereRootUrl2Link($rootUrl2, '/budgets/', $current_cost->budget->id);
      }

      $cost = 0;
      $actiontime = 0;
      $cost_time = 0;
      $cost_fixed = 0;
      $cost_material = 0;
      if (($this->choose == 'tickets') || ($this->choose == 'problems') || ($this->choose == 'changes'))
      {
        if (isset($current_cost->actiontime))
        {
          $actiontime = $current_cost->actiontime;

          $total_actiontime = $total_actiontime + $actiontime;
        }
        if (isset($current_cost->cost_time))
        {
          $cost_time = $current_cost->cost_time;

          $total_cost_time = $total_cost_time + $this->computeCostTime($actiontime, $cost_time);
        }
        if (isset($current_cost->cost_fixed))
        {
          $cost_fixed = $current_cost->cost_fixed;

          $total_cost_fixed = $total_cost_fixed + ($cost_fixed);
        }
        if (isset($current_cost->cost_material))
        {
          $cost_material = $current_cost->cost_material;

          $total_cost_material = $total_cost_material + ($cost_material);
        }

        $cost = $this->computeTotalCost($actiontime, $cost_time, $cost_fixed, $cost_material);
        $total_cost = $total_cost + ($cost);
      }
      else
      {
        if (isset($current_cost->cost))
        {
          $cost = $current_cost->cost;

          $total_cost = $total_cost + ($cost);
        }
      }

      $myCosts[$current_cost->id] = [
        'name'               => $current_cost->name,
        'begin_date'         => $current_cost->begin_date,
        'end_date'           => $current_cost->end_date,
        'budget'             => $budget,
        'budget_url'         => $budget_url,
        'cost'               => $this->showCosts($cost),
        'actiontime'         => $this->timestampToString($actiontime, false),
        'cost_time'          => $this->showCosts($cost_time),
        'cost_fixed'         => $this->showCosts($cost_fixed),
        'cost_material'      => $this->showCosts($cost_material),
      ];
    }

    // tri de la + récente à la + ancienne
    uasort($myCosts, function ($a, $b)
    {
      return $a['begin_date'] > $b['begin_date'];
    });

    if ($this->choose == 'projects')
    {
      $item2 = new $this->model();
      $myItem2 = $item2::with('tasks')->find($args['id']);
      foreach ($myItem2->tasks as $current_task)
      {
        if ($current_task->tickets !== null)
        {
          foreach ($current_task->tickets as $current_ticket)
          {
            $ticket = $current_ticket->name;
            $ticket_url = $this->genereRootUrl2Link($rootUrl2, '/tickets/', $current_ticket->id);

            if ($current_ticket->costs !== null)
            {
              foreach ($current_ticket->costs as $current_cost)
              {
                $budget = '';
                $budget_url = '';
                if ($current_cost->budget !== null)
                {
                  $budget = $current_cost->budget->name;
                  $budget_url = $this->genereRootUrl2Link($rootUrl2, '/budgets/', $current_cost->budget->id);
                }

                $cost = 0;
                $actiontime = 0;
                $cost_time = 0;
                $cost_fixed = 0;
                $cost_material = 0;
                if (isset($current_cost->actiontime))
                {
                  $actiontime = $current_cost->actiontime;

                  $ticket_costs_total_actiontime = $ticket_costs_total_actiontime + $actiontime;
                }
                if (isset($current_cost->cost_time))
                {
                  $cost_time = $current_cost->cost_time;

                  $ticket_costs_total_cost_time =
                    $ticket_costs_total_cost_time + $this->computeCostTime($actiontime, $cost_time);
                }
                if (isset($current_cost->cost_fixed))
                {
                  $cost_fixed = $current_cost->cost_fixed;

                  $ticket_costs_total_cost_fixed = $ticket_costs_total_cost_fixed + ($cost_fixed);
                }
                if (isset($current_cost->cost_material))
                {
                  $cost_material = $current_cost->cost_material;

                  $ticket_costs_total_cost_material = $ticket_costs_total_cost_material + ($cost_material);
                }

                $cost = $this->computeTotalCost($actiontime, $cost_time, $cost_fixed, $cost_material);
                $ticket_costs_total_cost = $ticket_costs_total_cost + ($cost);

                $myTicketCosts[$current_cost->id] = [
                  'ticket'             => $ticket,
                  'ticket_url'         => $ticket_url,
                  'name'               => $current_cost->name,
                  'begin_date'         => $current_cost->begin_date,
                  'end_date'           => $current_cost->end_date,
                  'budget'             => $budget,
                  'budget_url'         => $budget_url,
                  'cost'               => $this->showCosts($cost),
                  'actiontime'         => $this->timestampToString($actiontime, false),
                  'cost_time'          => $this->showCosts($cost_time),
                  'cost_fixed'         => $this->showCosts($cost_fixed),
                  'cost_material'      => $this->showCosts($cost_material),
                ];
              }
            }
          }
        }
      }

      // tri de la + récente à la + ancienne
      uasort($myTicketCosts, function ($a, $b)
      {
        return $a['begin_date'] > $b['begin_date'];
      });

      $total_costs = $total_cost + $ticket_costs_total_cost;
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('costs', $myCosts);
    $viewData->addData('ticket_costs', $myTicketCosts);
    $viewData->addData('total_cost', $this->showCosts($total_cost));
    $viewData->addData('total_actiontime', $this->timestampToString($total_actiontime, false));
    $viewData->addData('total_cost_time', $this->showCosts($total_cost_time));
    $viewData->addData('total_cost_fixed', $this->showCosts($total_cost_fixed));
    $viewData->addData('total_cost_material', $this->showCosts($total_cost_material));
    $viewData->addData('ticket_costs_total_cost', $this->showCosts($ticket_costs_total_cost));
    $viewData->addData(
      'ticket_costs_total_actiontime',
      $this->timestampToString($ticket_costs_total_actiontime, false)
    );
    $viewData->addData('ticket_costs_total_cost_time', $this->showCosts($ticket_costs_total_cost_time));
    $viewData->addData('ticket_costs_total_cost_fixed', $this->showCosts($ticket_costs_total_cost_fixed));
    $viewData->addData('ticket_costs_total_cost_material', $this->showCosts($ticket_costs_total_cost_material));
    $viewData->addData('total_costs', $this->showCosts($total_costs));
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('begin_date', $translator->translate('Start date'));
    $viewData->addTranslation('end_date', $translator->translate('End date'));
    $viewData->addTranslation('budget', $translator->translatePlural('Budget', 'Budgets', 1));
    $viewData->addTranslation('cost', $translator->translatePlural('Cost', 'Costs', 1));
    $viewData->addTranslation('costs', $translator->translatePlural('Cost', 'Costs', 2));
    $viewData->addTranslation('total_cost', $translator->translate('Total cost'));
    $viewData->addTranslation('total', $translator->translate('Total'));
    $viewData->addTranslation('actiontime', $translator->translate('Duration'));
    $viewData->addTranslation('cost_time', $translator->translate('Time cost'));
    $viewData->addTranslation('cost_fixed', $translator->translate('Fixed cost'));
    $viewData->addTranslation('cost_material', $translator->translate('Material cost'));
    $viewData->addTranslation('ticket_costs', $translator->translatePlural('Ticket cost', 'Ticket costs', 2));
    $viewData->addTranslation('ticket', $translator->translatePlural('Ticket', 'Tickets', 1));

    return $view->render($response, 'subitem/costs.html.twig', (array)$viewData);
  }

  public function timestampToString($time, $display_sec = true, $use_days = true)
  {
    global $translator;

    $time = (float)$time;

    $sign = '';
    if ($time < 0)
    {
      $sign = '- ';
      $time = abs($time);
    }
    $time = floor($time);

    // Force display seconds if time is null
    if ($time < $this->MINUTE_TIMESTAMP)
    {
      $display_sec = true;
    }

    $units = $this->getTimestampTimeUnits($time);
    if ($use_days)
    {
      if ($units['day'] > 0)
      {
        if ($display_sec)
        {
          return sprintf(
            $translator->translate('%1$s%2$d days %3$d hours %4$d minutes %5$d seconds'),
            $sign,
            $units['day'],
            $units['hour'],
            $units['minute'],
            $units['second']
          );
        }
        return sprintf(
          $translator->translate('%1$s%2$d days %3$d hours %4$d minutes'),
          $sign,
          $units['day'],
          $units['hour'],
          $units['minute']
        );
      }
    }
    else
    {
      if ($units['day'] > 0)
      {
        $units['hour'] += 24 * $units['day'];
      }
    }

    if ($units['hour'] > 0)
    {
      if ($display_sec)
      {
        return sprintf(
          $translator->translate('%1$s%2$d hours %3$d minutes %4$d seconds'),
          $sign,
          $units['hour'],
          $units['minute'],
          $units['second']
        );
      }
      return sprintf($translator->translate('%1$s%2$d hours %3$d minutes'), $sign, $units['hour'], $units['minute']);
    }

    if ($units['minute'] > 0)
    {
      if ($display_sec)
      {
        return sprintf(
          $translator->translate('%1$s%2$d minutes %3$d seconds'),
          $sign,
          $units['minute'],
          $units['second']
        );
      }
      return sprintf(
        $translator->translatePlural('%1$s%2$d minute', '%1$s%2$d minutes', $units['minute']),
        $sign,
        $units['minute']
      );
    }

    if ($display_sec)
    {
      return sprintf(
        $translator->translatePlural('%1$s%2$s second', '%1$s%2$s seconds', $units['second']),
        $sign,
        $units['second']
      );
    }

    return '';
  }

  public function getTimestampTimeUnits($time)
  {
    $out = [];

    $time          = round(abs($time));
    $out['second'] = 0;
    $out['minute'] = 0;
    $out['hour']   = 0;
    $out['day']    = 0;

    $out['second'] = $time % $this->MINUTE_TIMESTAMP;
    $time         -= $out['second'];

    if ($time > 0)
    {
      $out['minute'] = ($time % $this->HOUR_TIMESTAMP) / $this->MINUTE_TIMESTAMP;
      $time         -= $out['minute'] * $this->MINUTE_TIMESTAMP;

      if ($time > 0)
      {
        $out['hour'] = ($time % $this->DAY_TIMESTAMP) / $this->HOUR_TIMESTAMP;
        $time       -= $out['hour'] * $this->HOUR_TIMESTAMP;

        if ($time > 0)
        {
          $out['day'] = $time / $this->DAY_TIMESTAMP;
        }
      }
    }
    return $out;
  }

  public function showCosts($cost)
  {
    return sprintf("%.2f", $cost);
  }

  public function computeCostTime($actiontime, $cost_time)
  {
    return $this->showCosts(($actiontime * $cost_time / $this->HOUR_TIMESTAMP));
  }

  public function computeTotalCost($actiontime, $cost_time, $cost_fixed, $cost_material)
  {
    return $this->showCosts(($actiontime * $cost_time / $this->HOUR_TIMESTAMP) + $cost_fixed + $cost_material);
  }

  public function getDaysOfWeekArray()
  {
    global $translator;

    $tab = [];
    $tab[0] = $translator->translate("Sunday");
    $tab[1] = $translator->translate("Monday");
    $tab[2] = $translator->translate("Tuesday");
    $tab[3] = $translator->translate("Wednesday");
    $tab[4] = $translator->translate("Thursday");
    $tab[5] = $translator->translate("Friday");
    $tab[6] = $translator->translate("Saturday");

    return $tab;
  }

  public function getMonthsOfYearArray()
  {
    global $translator;

    $tab = [];
    $tab[1]  = $translator->translate("January");
    $tab[2]  = $translator->translate("February");
    $tab[3]  = $translator->translate("March");
    $tab[4]  = $translator->translate("April");
    $tab[5]  = $translator->translate("May");
    $tab[6]  = $translator->translate("June");
    $tab[7]  = $translator->translate("July");
    $tab[8]  = $translator->translate("August");
    $tab[9]  = $translator->translate("September");
    $tab[10] = $translator->translate("October");
    $tab[11] = $translator->translate("November");
    $tab[12] = $translator->translate("December");

    return $tab;
  }

  public function showSubItems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/items');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myItems = [];
    foreach ($myItem->items as $current_item)
    {
      $item3 = new $current_item->item_type();
      $myItem3 = $item3->find($current_item->item_id);
      if ($myItem3 !== null)
      {
        $type_fr = $item3->getTitle();
        $type = $item3->getTable();

        if (array_key_exists($type, $myItems) !== true)
        {
          $myItems[$type] = [
            'type'  => $type,
            'name'  => $type_fr,
            'items' => [],
          ];
        }

        $current_id = $myItem3->id;

        $name = $myItem3->name;
        if ($name == '')
        {
          $name = '(' . $current_id . ')';
        }

        $url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $current_id);

        $location = '';
        $location_url = '';
        if ($myItem3->location !== null)
        {
          $location = $myItem3->location->name;
          $location_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $myItem3->location->id);
        }

        $documents = [];
        if ($myItem3->documents !== null)
        {
          foreach ($myItem3->documents as $document)
          {
            $url_document = $this->genereRootUrl2Link($rootUrl2, '/documents/', $document->id);

            $documents[$document->id] = [
              'name'  => $document->name,
              'url'   => $url_document,
            ];
          }
        }

        $myItems[$type]['items'][$current_id][$current_item->id] = [
          'name'            => $name,
          'url'             => $url,
          'location'        => $location,
          'location_url'    => $location_url,
          'documents'       => $documents,
        ];
      }
    }

    // tri ordre alpha
    uasort($myItems, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('items', $myItems);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('location', $translator->translatePlural('Location', 'Locations', 2));
    $viewData->addTranslation('documents', $translator->translatePlural('Document', 'Documents', 2));

    return $view->render($response, 'subitem/items.html.twig', (array)$viewData);
  }

  public function getValueWithUnit($value, $unit, $decimals = 0)
  {
    global $translator;

    $formatted_number = is_numeric($value)
    ? $this->formatNumber($value, false, $decimals)
    : $value;

    if (strlen($unit) == 0)
    {
      return $formatted_number;
    }

    switch ($unit)
    {
      case 'year':
        //TRANS: %s is a number of years
          return sprintf($translator->translatePlural('%s year', '%s years', $value), $formatted_number);

      case 'month':
        //TRANS: %s is a number of months
          return sprintf($translator->translatePlural('%s month', '%s months', $value), $formatted_number);

      case 'day':
        //TRANS: %s is a number of days
          return sprintf($translator->translatePlural('%s day', '%s days', $value), $formatted_number);

      case 'hour':
        //TRANS: %s is a number of hours
          return sprintf($translator->translatePlural('%s hour', '%s hours', $value), $formatted_number);

      case 'minute':
        //TRANS: %s is a number of minutes
          return sprintf($translator->translatePlural('%s minute', '%s minutes', $value), $formatted_number);

      case 'second':
        //TRANS: %s is a number of seconds
          return sprintf($translator->translatePlural('%s second', '%s seconds', $value), $formatted_number);

      case 'millisecond':
        //TRANS: %s is a number of milliseconds
          return sprintf($translator->translatePlural('%s millisecond', '%s milliseconds', $value), $formatted_number);

      case 'auto':
          return $this->getSize($value * 1024 * 1024);

      case '%':
          return sprintf($translator->translate('%s%%'), $formatted_number);

      default:
          return sprintf($translator->translate('%1$s %2$s'), $formatted_number, $unit);
    }
  }

  public function formatNumber($number, $edit = false, $forcedecimal = -1)
  {
    if (!(isset($_SESSION['glpinumber_format'])))
    {
      $_SESSION['glpinumber_format'] = '';
    }

    // Php 5.3 : number_format() expects parameter 1 to be double,
    if ($number == "")
    {
      $number = 0;
    }
    elseif ($number == "-")
    { // used for not defines value (from Infocom::Amort, p.e.)
      return "-";
    }

    $number  = doubleval($number);
    $decimal = 2;
    if ($forcedecimal >= 0)
    {
      $decimal = $forcedecimal;
    }

    // Edit: clean display for mysql
    if ($edit)
    {
      return number_format($number, $decimal, '.', '');
    }

    // Display: clean display
    switch ($_SESSION['glpinumber_format'])
    {
      case 0: // French
          return str_replace(' ', '&nbsp;', number_format($number, $decimal, '.', ' '));

      case 2: // Other French
          return str_replace(' ', '&nbsp;', number_format($number, $decimal, ',', ' '));

      case 3: // No space with dot
          return number_format($number, $decimal, '.', '');

      case 4: // No space with comma
          return number_format($number, $decimal, ',', '');

      default: // English
          return number_format($number, $decimal, '.', ',');
    }
  }

  public function getSize($size)
  {
    global $translator;

    //TRANS: list of unit (o for octet)
    $bytes = [
      $translator->translate('o'),
      $translator->translate('Kio'),
      $translator->translate('Mio'),
      $translator->translate('Gio'),
      $translator->translate('Tio')
    ];
    foreach ($bytes as $val)
    {
      if ($size > 1024)
      {
        $size = $size / 1024;
      }
      else
      {
        break;
      }
    }
    //TRANS: %1$s is a number maybe float or string and %2$s the unit
    return sprintf($translator->translate('%1$s %2$s'), round($size, 2), $val);
  }

  public function showSubChanges(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with(['changes'])->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/changes');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $changes = [];
    foreach ($myItem->changes as $change)
    {
      $add_to_tab = false;
      if ($this->choose == 'tickets')
      {
        if ($change->pivot->ticket_id == $args['id'])
        {
          $add_to_tab = true;
        }
      }
      if ($this->choose == 'problems')
      {
        if ($change->pivot->problem_id == $args['id'])
        {
          $add_to_tab = true;
        }
      }

      $url = $this->genereRootUrl2Link($rootUrl2, '/changes/', $change->id);

      $status = $this->getStatusArray()[$change->status];

      $entity = '';
      $entity_url = '';
      if ($change->entity !== null)
      {
        $entity = $change->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $change->entity->id);
      }

      $priority = $this->getPriorityArray()[$change->priority];

      $requesters = [];
      if ($change->requester !== null)
      {
        foreach ($change->requester as $requester)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
          ];
        }
      }
      if ($change->requestergroup !== null)
      {
        foreach ($change->requestergroup as $requestergroup)
        {
          $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

          $requesters[] = [
            'url'   => $requester_url,
            'name'  => $requestergroup->completename,
          ];
        }
      }

      $technicians = [];
      if ($change->technician !== null)
      {
        foreach ($change->technician as $technician)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
          ];
        }
      }
      if ($change->techniciangroup !== null)
      {
        foreach ($change->techniciangroup as $techniciangroup)
        {
          $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

          $technicians[] = [
            'url'   => $technician_url,
            'name'  => $techniciangroup->completename,
          ];
        }
      }

      $category = '';
      $category_url = '';
      if ($change->itilcategorie !== null)
      {
        $category = $change->itilcategorie->name;
        $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $change->itilcategorie->id);
      }

      $planification = 0; // TODO

      if ($add_to_tab)
      {
        $changes[$change->id] = [
          'url'               => $url,
          'status'            => $status,
          'date'              => $change->date,
          'last_update'       => $change->updated_at,
          'entity'            => $entity,
          'entity_url'        => $entity_url,
          'priority'          => $priority,
          'requesters'        => $requesters,
          'technicians'       => $technicians,
          'title'             => $change->name,
          'category'          => $category,
          'category_url'      => $category_url,
          'planification'     => $planification,
        ];
      }
    }

    // tri de la + récente à la + ancienne
    uasort($changes, function ($a, $b)
    {
      return $a['last_update'] < $b['last_update'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('changes', $changes);

    $viewData->addTranslation('changes', $translator->translatePlural('Change', 'Changes', 2));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_change_found', $translator->translate('No change found.'));

    return $view->render($response, 'subitem/itilchanges.html.twig', (array)$viewData);
  }

  public function showSubProjects(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $ticketclass = str_ireplace('\\v1\\Controllers\\', '\\Models\\', get_class($this));
    $item2 = new \App\Models\Itilproject();
    $myItem2 = $item2->where(['item_id' => $args['id'], 'item_type' => $ticketclass])->get();

    $rootUrl = $this->genereRootUrl($request, '/projects');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProjects = [];
    foreach ($myItem2 as $project)
    {
      $item3 = new \App\Models\Project();
      $myItem3 = $item3->find($project->project_id);
      if ($myItem3 !== null)
      {
        $name = $myItem3->name;

        $url = $this->genereRootUrl2Link($rootUrl2, '/projects/', $myItem3->id);

        $status = '';
        $status_color = '';
        if ($myItem3->state !== null)
        {
          $status = $myItem3->state->name;
          $status_color = $myItem3->state->color;
        }

        $open_date = $myItem3->date;

        $last_update = $myItem3->updated_at;

        $entity = '';
        $entity_url = '';
        if ($myItem3->entity !== null)
        {
          $entity = $myItem3->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
        }

        $priority = $this->getPriorityArray()[$myItem3->priority];

        $manager = '';
        $manager_url = '';
        if ($myItem3->user !== null)
        {
          $manager = $this->genereUserName($myItem3->user->name, $myItem3->user->lastname, $myItem3->user->firstname);
          $manager_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $myItem3->user->id);
        }

        $manager_group = '';
        $manager_group_url = '';
        if ($myItem3->group !== null)
        {
          $manager_group = $myItem3->group->completename;
          $manager_group_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $myItem3->group->id);
        }

        $myProjects[] = [
          'name'                => $name,
          'url'                 => $url,
          'status'              => $status,
          'status_color'        => $status_color,
          'open_date'           => $open_date,
          'last_update'         => $last_update,
          'entity'              => $entity,
          'entity_url'          => $entity_url,
          'priority'            => $priority,
          'manager'             => $manager,
          'manager_url'         => $manager_url,
          'manager_group'       => $manager_group,
          'manager_group_url'   => $manager_group_url,
        ];
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('projects', $myProjects);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('open_date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('manager', $translator->translate('Manager'));
    $viewData->addTranslation('manager_group', $translator->translate('Manager group'));

    return $view->render($response, 'subitem/projects.html.twig', (array)$viewData);
  }

  public function showSubTickets(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    if ($this->choose == 'changes')
    {
      $item2 = new \App\Models\ChangeTicket();
      $myItem2 = $item2::where('change_id', $args['id'])->get();
    }
    if ($this->choose == 'problems')
    {
      $item2 = new \App\Models\ProblemTicket();
      $myItem2 = $item2::where('problem_id', $args['id'])->get();
    }
    if ($this->choose == 'projecttasks')
    {
      $item2 = new \App\Models\ProjecttaskTicket();
      $myItem2 = $item2::where('projecttask_id', $args['id'])->get();
    }

    $rootUrl = $this->genereRootUrl($request, '/tickets');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $tickets = [];
    foreach ($myItem2 as $current_item)
    {
      $item3 = new \App\Models\Ticket();
      $myItem3 = $item3->find($current_item->ticket_id);
      if ($myItem3 !== null)
      {
        $url = $this->genereRootUrl2Link($rootUrl2, '/tickets/', $myItem3->id);

        $status = $this->getStatusArray()[$myItem3->status];

        $entity = '';
        $entity_url = '';
        if ($myItem3->entity !== null)
        {
          $entity = $myItem3->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
        }

        $priority = $this->getPriorityArray()[$myItem3->priority];

        $requesters = [];
        if ($myItem3->requester !== null)
        {
          foreach ($myItem3->requester as $requester)
          {
            $requester_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $requester->id);

            $requesters[] = [
              'url'   => $requester_url,
              'name'  => $this->genereUserName($requester->name, $requester->lastname, $requester->firstname),
            ];
          }
        }
        if ($myItem3->requestergroup !== null)
        {
          foreach ($myItem3->requestergroup as $requestergroup)
          {
            $requester_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $requestergroup->id);

            $requesters[] = [
              'url'   => $requester_url,
              'name'  => $requestergroup->completename,
            ];
          }
        }

        $technicians = [];
        if ($myItem3->technician !== null)
        {
          foreach ($myItem3->technician as $technician)
          {
            $technician_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $technician->id);

            $technicians[] = [
              'url'   => $technician_url,
              'name'  => $this->genereUserName($technician->name, $technician->lastname, $technician->firstname),
            ];
          }
        }
        if ($myItem3->techniciangroup !== null)
        {
          foreach ($myItem3->techniciangroup as $techniciangroup)
          {
            $technician_url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $techniciangroup->id);

            $technicians[] = [
              'url'   => $technician_url,
              'name'  => $techniciangroup->completename,
            ];
          }
        }

        $associated_items = [];
        $item4 = new \App\Models\ItemTicket();
        $myItem4 = $item4::where('ticket_id', $current_item->ticket_id)->get();
        if ($myItem4 !== null)
        {
          foreach ($myItem4 as $val)
          {
            $item5 = new $val->item_type();
            $myItem5 = $item5->find($val->item_id);
            if ($myItem5 !== null)
            {
              $type5_fr = $item5->getTitle();
              $type5 = $item5->getTable();

              $name5 = $myItem5->name;

              $url5 = $this->genereRootUrl2Link($rootUrl2, '/' . $type5 . '/', $myItem5->id);

              if ($type5_fr != '')
              {
                $type5_fr = $type5_fr . ' - ';
              }

              $associated_items[] = [
                'type'     => $type5_fr,
                'name'     => $name5,
                'url'      => $url5,
              ];
            }
          }

          if (empty($associated_items))
          {
            $associated_items[] = [
              'type'     => '',
              'name'     => $translator->translate('General'),
              'url'      => '',
            ];
          }
        }

        $category = '';
        $category_url = '';
        if ($myItem3->category !== null)
        {
          $category = $myItem3->category->name;
          $category_url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/categories/', $myItem3->category->id);
        }

        $planification = 0; // TODO

        $tickets[$myItem3->id] = [
          'url'                 => $url,
          'status'              => $status,
          'date'                => $myItem3->date,
          'last_update'         => $myItem3->updated_at,
          'entity'              => $entity,
          'entity_url'          => $entity_url,
          'priority'            => $priority,
          'requesters'          => $requesters,
          'technicians'         => $technicians,
          'associated_items'    => $associated_items,
          'title'               => $myItem3->name,
          'category'            => $category,
          'category_url'        => $category_url,
          'planification'       => $planification,
        ];
      }
    }

    // tri de la + récente à la + ancienne
    uasort($tickets, function ($a, $b)
    {
      return $a['last_update'] > $b['last_update'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('tickets', $tickets);

    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('date', $translator->translatePlural('Date', 'Dates', 1));
    $viewData->addTranslation('last_update', $translator->translate('Last update'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('priority', $translator->translate('Priority'));
    $viewData->addTranslation('requesters', $translator->translatePlural('Requester', 'Requesters', 1));
    $viewData->addTranslation('technicians', $translator->translate('Assigned'));
    $viewData->addTranslation(
      'associated_items',
      $translator->translatePlural('Associated element', 'Associated elements', 2)
    );
    $viewData->addTranslation('category', $translator->translate('Category'));
    $viewData->addTranslation('title', $translator->translate('Title'));
    $viewData->addTranslation('planification', $translator->translate('Planification'));
    $viewData->addTranslation('no_ticket_found', $translator->translate('No ticket found.'));

    return $view->render($response, 'subitem/itiltickets.html.twig', (array)$viewData);
  }

  public function showStats(Request $request, Response $response, $args)
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/stats');

    $feeds = [];

    $feeds[] = [
      'date'  => $myItem->date,
      'text'  => $translator->translate('Opening date'),
      'icon'  => 'pencil alternate',
      'color' => 'blue'
    ];

    $feeds[] = [
      'date'  => $myItem->time_to_resolve,
      'text'  => $translator->translate('Time to resolve'),
      'icon'  => 'hourglass half',
      'color' => 'blue'
    ];
    if ($myItem->status >= 5)
    {
      $feeds[] = [
        'date'  => $myItem->solvedate,
        'text'  => $translator->translate('Resolution date'),
        'icon'  => 'check circle',
        'color' => 'blue'
      ];
    }
    if ($myItem->status == 6)
    {
      $feeds[] = [
        'date'  => $myItem->closedate,
        'text'  => $translator->translate('Closing date'),
        'icon'  => 'flag checkered',
        'color' => 'blue'
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('feeds', $feeds);

    return $view->render($response, 'subitem/stats.html.twig', (array) $viewData);
  }

  public function showSubApprovals(Request $request, Response $response, $args)
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('approvals')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/approvals');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myApprovals = [];
    foreach ($myItem->approvals as $approval)
    {
      $status = $this->getApprovalStatus()[$approval->status];

      $request_date = $approval->submission_date;

      $request_user = '';
      $request_user_url = '';
      if ($approval->usersrequester !== null)
      {
        $request_user = $this->genereUserName(
          $approval->usersrequester->name,
          $approval->usersrequester->lastname,
          $approval->usersrequester->firstname
        );
        $request_user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $approval->usersrequester->id);
      }

      $request_comment = $approval->comment_submission;

      $approval_date = $approval->validation_date;

      $approval_user = '';
      $approval_user_url = '';
      if ($approval->uservalidate !== null)
      {
        $approval_user = $this->genereUserName(
          $approval->uservalidate->name,
          $approval->uservalidate->lastname,
          $approval->uservalidate->firstname
        );
        $approval_user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $approval->uservalidate->id);
      }

      $approval_comment = $approval->comment_validation;

      $myApprovals[] = [
        'status'              => $status,
        'request_date'        => $request_date,
        'request_user'        => $request_user,
        'request_user_url'    => $request_user_url,
        'request_comment'     => $request_comment,
        'approval_date'       => $approval_date,
        'approval_user'       => $approval_user,
        'approval_user_url'   => $approval_user_url,
        'approval_comment'    => $approval_comment,
      ];
    }

    // tri de la + récente à la + ancienne
    uasort($myApprovals, function ($a, $b)
    {
      return $a['request_date'] < $b['request_date'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('approvals', $myApprovals);

    $viewData->addTranslation('status', $translator->translate('Status'));
    $viewData->addTranslation('request_date', $translator->translate('Request date'));
    $viewData->addTranslation('request_user', $translator->translate('Approval requester'));
    $viewData->addTranslation('request_comment', $translator->translate('Request comments'));
    $viewData->addTranslation('approval_date', $translator->translate('Approval status'));
    $viewData->addTranslation('approval_user', $translator->translate('Approver'));
    $viewData->addTranslation('approval_comment', $translator->translate('Approval comments'));

    return $view->render($response, 'subitem/approvals.html.twig', (array)$viewData);
  }

  public function getApprovalStatus()
  {
    global $translator;

    return [
      $this->APPROVAL_WAITING => [
        'title' => $translator->translate('Waiting for approval'),
        'color' => '#FFC65D',
      ],
      $this->APPROVAL_REFUSED => [
        'title' => $translator->translate('Refused'),
        'color' => '#cf9b9b',
      ],
      $this->APPROVAL_ACCEPTED => [
        'title' => $translator->translate('Granted'),
        'color' => '#9BA563',
      ],
    ];
  }

  public function genereUserName($name, $lastname = '', $firstname = '', $add_name = false)
  {
    $ret = $lastname . ' ' . $firstname;
    $ret = trim($ret);

    if ($ret != '')
    {
      if ($add_name === true)
      {
        $ret = $ret . ' (' . $name . ')';
      }
    }
    else
    {
      $ret = $name;
    }

    return $ret;
  }

  public function showSubInfocoms(Request $request, Response $response, $args)
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('infocom')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/infocom');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myInfocom = [];
    foreach ($myItem->infocom as $infocom)
    {
      $comment = $infocom->comment;

      $entity_id = $infocom->entity_id;
      $entity_name = '';
      if ($infocom->entity !== null)
      {
        $entity_name = $infocom->entity->completename;
      }

      $is_recursive = $infocom->is_recursive;

      $buy_date = $infocom->buy_date;

      $use_date = $infocom->use_date;

      $warranty_duration = $infocom->warranty_duration;

      $warranty_info = $infocom->warranty_info;

      $supplier_id = $infocom->supplier_id;
      $supplier_name = '';
      if ($infocom->supplier !== null)
      {
        $supplier_name = $infocom->supplier->name;
      }

      $order_number = $infocom->order_number;

      $delivery_number = $infocom->delivery_number;

      $immo_number = $infocom->immo_number;

      $value = $this->showCosts($infocom->value);

      $warranty_value = $this->showCosts($infocom->warranty_value);

      $sink_time = $infocom->sink_time;

      $sink_type = $infocom->sink_type;

      $sink_coeff = $infocom->sink_coeff;

      $bill = $infocom->bill;

      $budget_id = $infocom->budget_id;
      $budget_name = '';
      if ($infocom->budget !== null)
      {
        $budget_name = $infocom->budget->name;
      }

      $alert = $infocom->alert;

      $order_date = $infocom->order_date;

      $delivery_date = $infocom->delivery_date;

      $inventory_date = $infocom->inventory_date;

      $warranty_date = $infocom->warranty_date;

      $decommission_date = '';
      $decommission_date_tmp = explode(' ', $infocom->decommission_date);
      if (count($decommission_date_tmp) == 2)
      {
        $decommission_date = $decommission_date_tmp[0];
      }

      $businesscriticity_id = $infocom->businesscriticity_id;
      $businesscriticity_name = '';
      if ($infocom->businesscriticity !== null)
      {
        $businesscriticity_name = $infocom->businesscriticity->name;
      }

      $myInfocom = [
        'comment'                   => $comment,
        'entity_id'                 => $entity_id,
        'entity_name'               => $entity_name,
        'is_recursive'              => $is_recursive,
        'buy_date'                  => $buy_date,
        'use_date'                  => $use_date,
        'warranty_duration'         => $warranty_duration,
        'warranty_info'             => $warranty_info,
        'supplier_id'               => $supplier_id,
        'supplier_name'             => $supplier_name,
        'order_number'              => $order_number,
        'delivery_number'           => $delivery_number,
        'immo_number'               => $immo_number,
        'value'                     => $value,
        'warranty_value'            => $warranty_value,
        'sink_time'                 => $sink_time,
        'sink_type'                 => $sink_type,
        'sink_coeff'                => $sink_coeff,
        'bill'                      => $bill,
        'budget_id'                 => $budget_id,
        'budget_name'               => $budget_name,
        'alert'                     => $alert,
        'order_date'                => $order_date,
        'delivery_date'             => $delivery_date,
        'inventory_date'            => $inventory_date,
        'warranty_date'             => $warranty_date,
        'decommission_date'         => $decommission_date,
        'businesscriticity_id'      => $businesscriticity_id,
        'businesscriticity_name'    => $businesscriticity_name,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDefs = $item->getSpecificFunction('getDefinitionInfocom');
    $myItemData = [];
    if (count($myInfocom) > 0)
    {
      $myItemData = [
        'order_date'          => $myInfocom['order_date'],
        'buy_date'            => $myInfocom['buy_date'],
        'delivery_date'       => $myInfocom['delivery_date'],
        'use_date'            => $myInfocom['use_date'],
        'inventory_date'      => $myInfocom['inventory_date'],
        'decommission_date'   => $myInfocom['decommission_date'],
        'supplier'            => [
          'id'    => $myInfocom['supplier_id'],
          'name'  => $myInfocom['supplier_name'],
        ],
        'budget'              => [
          'id'    => $myInfocom['budget_id'],
          'name'  => $myInfocom['budget_name'],
        ],
        'order_number'        => $myInfocom['order_number'],
        'immo_number'         => $myInfocom['immo_number'],
        'bill'                => $myInfocom['bill'],
        'delivery_number'     => $myInfocom['delivery_number'],
        'value'               => $myInfocom['value'],
        'warranty_value'      => $myInfocom['warranty_value'],
        'sink_type'           => $myInfocom['sink_type'],
        'sink_time'           => $myInfocom['sink_time'],
        'sink_coeff'          => $myInfocom['sink_coeff'],
        'businesscriticity'   => [
          'id'    => $myInfocom['businesscriticity_id'],
          'name'  => $myInfocom['businesscriticity_name'],
        ],
        'comment'             => $myInfocom['comment'],
        'warranty_date'       => $myInfocom['warranty_date'],
        'warranty_duration'   => $myInfocom['warranty_duration'],
        'warranty_info'       => $myInfocom['warranty_info'],
      ];
    }
    $myItemDataObject = json_decode(json_encode($myItemData));

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));

    return $view->render($response, 'subitem/infocom.html.twig', (array)$viewData);
  }

  public function showSubAttachedItems(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->associateditems_model();
    $myItem2 = $item2::where($this->associateditems_model_id, $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/attacheditems');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myAttachedItems = [];
    $nb_total = 0;
    foreach ($myItem2 as $current_attacheditem)
    {
      $item3 = new $current_attacheditem->item_type();
      $myItem3 = $item3->find($current_attacheditem->item_id);
      if ($myItem3 !== null)
      {
        $type_fr = $item3->getTitle();
        $type = $item3->getTable();

        $entity = '';
        $entity_url = '';
        if ($myItem3->entity !== null)
        {
          $entity = $myItem3->entity->completename;
          $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
        }

        $nom = $myItem3->name;

        $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

        $serial = $myItem3->serial;

        $otherserial = $myItem3->otherserial;

        $first = false;
        if (array_key_exists($type, $myAttachedItems) !== true)
        {
          $myAttachedItems[$type] = [
            'name'  => $type_fr,
            'nb'    => 0,
            'items' => [],
          ];

          $first = true;
        }

        $status = '';
        if ($this->choose == 'contracts')
        {
          if ($myItem3->state !== null)
          {
            $status = $myItem3->state->name;
          }
        }

        $domain_relation = '';
        $domain_relation_url = '';
        if ($this->choose == 'domains')
        {
          if ($current_attacheditem->relation !== null)
          {
            $domain_relation = $current_attacheditem->relation->name;
            $domain_relation_url = $this->genereRootUrl2Link(
              $rootUrl2,
              '/dropdowns/domainrelations/',
              $current_attacheditem->relation->id
            );
          }
        }

        $value = '';
        if ($this->choose == 'budgets')
        {
          $value = $this->showCosts($current_attacheditem->value);
        }

        $myAttachedItems[$type]['items'][$myItem3->id] = [
          'first'                 => $first,
          'entity'                => $entity,
          'entity_url'            => $entity_url,
          'nom'                   => $nom,
          'nom_url'               => $nom_url,
          'serial'                => $serial,
          'otherserial'           => $otherserial,
          'status'                => $status,
          'domain_relation'       => $domain_relation,
          'domain_relation_url'   => $domain_relation_url,
          'value'                 => $value,
        ];

        $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
      }
    }

    if ($this->choose == 'budgets')
    {
      $item2 = new \App\Models\Contractcost();
      $myItem2 = $item2::where('budget_id', $args['id'])->get();
      foreach ($myItem2 as $current_attacheditem)
      {
        $item3 = new \App\Models\Contract();
        $myItem3 = $item3->find($current_attacheditem->contract_id);
        if ($myItem3 !== null)
        {
          $type_fr = $translator->translatePlural('Contract', 'Contract', 1);
          $type = 'contracts';

          $entity = '';
          $entity_url = '';
          if ($myItem3->entity !== null)
          {
            $entity = $myItem3->entity->completename;
            $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
          }

          $nom = $myItem3->name;

          $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

          $serial = '';

          $otherserial = '';

          $first = false;
          if (array_key_exists($type, $myAttachedItems) !== true)
          {
            $myAttachedItems[$type] = [
              'name'  => $type_fr,
              'nb'    => 0,
              'items' => [],
            ];

            $first = true;
          }

          $status = '';

          $domain_relation = '';
          $domain_relation_url = '';

          $value = $this->showCosts($current_attacheditem->cost);

          if (array_key_exists($myItem3->id, $myAttachedItems[$type]['items']) !== true)
          {
            $myAttachedItems[$type]['items'][$myItem3->id] = [
              'first'                 => $first,
              'entity'                => $entity,
              'entity_url'            => $entity_url,
              'nom'                   => $nom,
              'nom_url'               => $nom_url,
              'serial'                => $serial,
              'otherserial'           => $otherserial,
              'status'                => $status,
              'domain_relation'       => $domain_relation,
              'domain_relation_url'   => $domain_relation_url,
              'value'                 => $value,
            ];
          }
          else
          {
            $sum = $myAttachedItems[$type]['items'][$myItem3->id]['value'] + $value;
            $myAttachedItems[$type]['items'][$myItem3->id]['value'] = $this->showCosts($sum);
          }

          $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
        }
      }

      $item2 = new \App\Models\Ticketcost();
      $myItem2 = $item2::where('budget_id', $args['id'])->get();
      foreach ($myItem2 as $current_attacheditem)
      {
        $item3 = new \App\Models\Ticket();
        $myItem3 = $item3->find($current_attacheditem->ticket_id);
        if ($myItem3 !== null)
        {
          $type_fr = $translator->translatePlural('Ticket', 'Tickets', 1);
          $type = 'tickets';

          $entity = '';
          $entity_url = '';
          if ($myItem3->entity !== null)
          {
            $entity = $myItem3->entity->completename;
            $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
          }

          $nom = $myItem3->name;

          $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

          $serial = '';

          $otherserial = '';

          $first = false;
          if (array_key_exists($type, $myAttachedItems) !== true)
          {
            $myAttachedItems[$type] = [
              'name'  => $type_fr,
              'nb'    => 0,
              'items' => [],
            ];

            $first = true;
          }

          $status = '';

          $domain_relation = '';
          $domain_relation_url = '';

          $value = $this->computeTotalCost(
            $current_attacheditem->actiontime,
            $current_attacheditem->cost_time,
            $current_attacheditem->cost_fixed,
            $current_attacheditem->cost_material
          );

          if (array_key_exists($myItem3->id, $myAttachedItems[$type]['items']) !== true)
          {
            $myAttachedItems[$type]['items'][$myItem3->id] = [
              'first'                 => $first,
              'entity'                => $entity,
              'entity_url'            => $entity_url,
              'nom'                   => $nom,
              'nom_url'               => $nom_url,
              'serial'                => $serial,
              'otherserial'           => $otherserial,
              'status'                => $status,
              'domain_relation'       => $domain_relation,
              'domain_relation_url'   => $domain_relation_url,
              'value'                 => $value,
            ];
          }
          else
          {
            $sum = $myAttachedItems[$type]['items'][$myItem3->id]['value'] + $value;
            $myAttachedItems[$type]['items'][$myItem3->id]['value'] = $this->showCosts($sum);
          }

          $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
        }
      }

      $item2 = new \App\Models\Problemcost();
      $myItem2 = $item2::where('budget_id', $args['id'])->get();
      foreach ($myItem2 as $current_attacheditem)
      {
        $item3 = new \App\Models\Problem();
        $myItem3 = $item3->find($current_attacheditem->problem_id);
        if ($myItem3 !== null)
        {
          $type_fr = $translator->translatePlural('Problem', 'Problems', 1);
          $type = 'problems';

          $entity = '';
          $entity_url = '';
          if ($myItem3->entity !== null)
          {
            $entity = $myItem3->entity->completename;
            $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
          }

          $nom = $myItem3->name;

          $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

          $serial = '';

          $otherserial = '';

          $first = false;
          if (array_key_exists($type, $myAttachedItems) !== true)
          {
            $myAttachedItems[$type] = [
              'name'  => $type_fr,
              'nb'    => 0,
              'items' => [],
            ];

            $first = true;
          }

          $status = '';

          $domain_relation = '';
          $domain_relation_url = '';

          $value = $this->computeTotalCost(
            $current_attacheditem->actiontime,
            $current_attacheditem->cost_time,
            $current_attacheditem->cost_fixed,
            $current_attacheditem->cost_material
          );

          if (array_key_exists($myItem3->id, $myAttachedItems[$type]['items']) !== true)
          {
            $myAttachedItems[$type]['items'][$myItem3->id] = [
              'first'                 => $first,
              'entity'                => $entity,
              'entity_url'            => $entity_url,
              'nom'                   => $nom,
              'nom_url'               => $nom_url,
              'serial'                => $serial,
              'otherserial'           => $otherserial,
              'status'                => $status,
              'domain_relation'       => $domain_relation,
              'domain_relation_url'   => $domain_relation_url,
              'value'                 => $value,
            ];
          }
          else
          {
            $sum = $myAttachedItems[$type]['items'][$myItem3->id]['value'] + $value;
            $myAttachedItems[$type]['items'][$myItem3->id]['value'] = $this->showCosts($sum);
          }

          $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
        }
      }

      $item2 = new \App\Models\Changecost();
      $myItem2 = $item2::where('budget_id', $args['id'])->get();
      foreach ($myItem2 as $current_attacheditem)
      {
        $item3 = new \App\Models\Change();
        $myItem3 = $item3->find($current_attacheditem->change_id);
        if ($myItem3 !== null)
        {
          $type_fr = $translator->translatePlural('Change', 'Changes', 1);
          $type = 'changes';

          $entity = '';
          $entity_url = '';
          if ($myItem3->entity !== null)
          {
            $entity = $myItem3->entity->completename;
            $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
          }

          $nom = $myItem3->name;

          $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

          $serial = '';

          $otherserial = '';

          $first = false;
          if (array_key_exists($type, $myAttachedItems) !== true)
          {
            $myAttachedItems[$type] = [
              'name' => $type_fr,
              'nb' => 0,
              'items' => [],
            ];

            $first = true;
          }

          $status = '';

          $domain_relation = '';
          $domain_relation_url = '';

          $value = $this->computeTotalCost(
            $current_attacheditem->actiontime,
            $current_attacheditem->cost_time,
            $current_attacheditem->cost_fixed,
            $current_attacheditem->cost_material
          );

          if (array_key_exists($myItem3->id, $myAttachedItems[$type]['items']) !== true)
          {
            $myAttachedItems[$type]['items'][$myItem3->id] = [
              'first'                 => $first,
              'entity'                => $entity,
              'entity_url'            => $entity_url,
              'nom'                   => $nom,
              'nom_url'               => $nom_url,
              'serial'                => $serial,
              'otherserial'           => $otherserial,
              'status'                => $status,
              'domain_relation'       => $domain_relation,
              'domain_relation_url'   => $domain_relation_url,
              'value'                 => $value,
            ];
          }
          else
          {
            $sum = $myAttachedItems[$type]['items'][$myItem3->id]['value'] + $value;
            $myAttachedItems[$type]['items'][$myItem3->id]['value'] = $this->showCosts($sum);
          }

          $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
        }
      }

      $item2 = new \App\Models\Projectcost();
      $myItem2 = $item2::where('budget_id', $args['id'])->get();
      foreach ($myItem2 as $current_attacheditem)
      {
        $item3 = new \App\Models\Project();
        $myItem3 = $item3->find($current_attacheditem->project_id);
        if ($myItem3 !== null)
        {
          $type_fr = $translator->translatePlural('Project', 'Projects', 1);
          $type = 'projects';

          $entity = '';
          $entity_url = '';
          if ($myItem3->entity !== null)
          {
            $entity = $myItem3->entity->completename;
            $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $myItem3->entity->id);
          }

          $nom = $myItem3->name;

          $nom_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem3->id);

          $serial = '';

          $otherserial = '';

          $first = false;
          if (array_key_exists($type, $myAttachedItems) !== true)
          {
            $myAttachedItems[$type] = [
              'name'  => $type_fr,
              'nb'    => 0,
              'items' => [],
            ];

            $first = true;
          }

          $status = '';

          $domain_relation = '';
          $domain_relation_url = '';

          $value = $this->showCosts($current_attacheditem->cost);

          if (array_key_exists($myItem3->id, $myAttachedItems[$type]['items']) !== true)
          {
            $myAttachedItems[$type]['items'][$myItem3->id] = [
              'first'                 => $first,
              'entity'                => $entity,
              'entity_url'            => $entity_url,
              'nom'                   => $nom,
              'nom_url'               => $nom_url,
              'serial'                => $serial,
              'otherserial'           => $otherserial,
              'status'                => $status,
              'domain_relation'       => $domain_relation,
              'domain_relation_url'   => $domain_relation_url,
              'value'                 => $value,
            ];
          }
          else
          {
            $sum = $myAttachedItems[$type]['items'][$myItem3->id]['value'] + $value;
            $myAttachedItems[$type]['items'][$myItem3->id]['value'] = $this->showCosts($sum);
          }

          $myAttachedItems[$type]['nb'] = count($myAttachedItems[$type]['items']);
        }
      }
    }

    // tri par ordre alpha
    uasort($myAttachedItems, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    foreach (array_keys($myAttachedItems) as $type_item)
    {
      $nb_total = $nb_total + $myAttachedItems[$type_item]['nb'];

      if (stristr($type_item, 'consumable'))
      {
        $myAttachedItems[$type_item]['name'] = $myAttachedItems[$type_item]['name'] . ' (' . $type_item . ')';
      }
      if (stristr($type_item, 'cartridge'))
      {
        $myAttachedItems[$type_item]['name'] = $myAttachedItems[$type_item]['name'] . ' (' . $type_item . ')';
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('attacheditems', $myAttachedItems);
    $viewData->addData('show', $this->choose);
    $viewData->addData('nb_total', $nb_total);

    $viewData->addTranslation('type', $translator->translatePlural('Type', 'Types', 1));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('serial', $translator->translate('Serial number'));
    $viewData->addTranslation('otherserial', $translator->translate('Inventory number'));
    $viewData->addTranslation('status', $translator->translate('State'));
    $viewData->addTranslation('domain_relation', $translator->translatePlural(
      'Domain relation',
      'Domains relations',
      1
    ));
    $viewData->addTranslation('value', $translator->translate('Value'));
    $viewData->addTranslation('total', $translator->translate('Total'));

    return $view->render($response, 'subitem/attacheditems.html.twig', (array)$viewData);
  }

  public function genereRootUrl($request, $param = '')
  {
    $rootUrl = $this->getUrlWithoutQuery($request);
    if ($param != '')
    {
      $rootUrl = rtrim($rootUrl, '/' . $param);
    }

    return $rootUrl;
  }

  public function genereRootUrl2($rootUrl, $param = '')
  {
    $rootUrl2 = '';
    if (($this->rootUrl2 != '') && ($param != ''))
    {
      $rootUrl2 = rtrim($rootUrl, $param);
    }

    return $rootUrl2;
  }

  public function genereRootUrl2Link($rootUrl2, $param, $id)
  {
    $rootUrl2Link = '';
    if (($rootUrl2 != '') && ($param != '') && ($param != '//') && ($id != ''))
    {
      $rootUrl2Link = $rootUrl2 . $param . $id;
    }

    return $rootUrl2Link;
  }

  public function showSubReservations(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/reservations');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myReservations = [];
    $myReservations_old = [];
    foreach ($myItem->reservations as $current_reservationitem)
    {
      if ($current_reservationitem->reservations !== null)
      {
        foreach ($current_reservationitem->reservations as $current_reservation)
        {
          $begin = $current_reservation->begin;

          $end = $current_reservation->end;

          $user = '';
          $user_url = '';
          if ($current_reservation->user !== null)
          {
            $user = $this->genereUserName(
              $current_reservation->user->name,
              $current_reservation->user->lastname,
              $current_reservation->user->firstname
            );
            $user_url = $this->genereRootUrl2Link($rootUrl2, '/users/', $current_reservation->user->id);
          }

          $comment = $current_reservation->comment;


          if ($end < date('Y-m-d H:i:s'))
          {
            $myReservations_old[] = [
              'begin'       => $begin,
              'end'         => $end,
              'user'        => $user,
              'user_url'    => $user_url,
              'comment'     => $comment,
            ];
          }
          else
          {
            $myReservations[] = [
              'begin'       => $begin,
              'end'         => $end,
              'user'        => $user,
              'user_url'    => $user_url,
              'comment'     => $comment,
            ];
          }
        }
      }
    }

    // tri par ordre + ancien
    uasort($myReservations, function ($a, $b)
    {
      return $a['begin'] > $b['begin'];
    });
    // tri par ordre + recent
    uasort($myReservations_old, function ($a, $b)
    {
      return $a['begin'] < $b['begin'];
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('reservations', $myReservations);
    $viewData->addData('reservations_old', $myReservations_old);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('start_date', $translator->translate('Start date'));
    $viewData->addTranslation('end_date', $translator->translate('End date'));
    $viewData->addTranslation('by', $translator->translate('By'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));
    $viewData->addTranslation('current_reservations', $translator->translate('Current and future reservations'));
    $viewData->addTranslation('past_reservations', $translator->translate('Past reservations'));
    $viewData->addTranslation('no_reservations', $translator->translate('No reservation'));

    return $view->render($response, 'subitem/reservations.html.twig', (array)$viewData);
  }
}
