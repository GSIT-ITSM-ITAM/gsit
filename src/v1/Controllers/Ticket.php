<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class Ticket extends Common
{
  protected $model = '\App\Models\Ticket';
  protected $rootUrl2 = '/tickets/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Ticket();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Ticket();
    return $this->commonShowITILItem($request, $response, $args, $item);
  }

  public function showNewItem(Request $request, Response $response, $args): Response
  {
    $session = new \SlimSession\Helper();
    $session->ticketCreationDate = gmdate('Y-m-d H:i:s');

    $item = new \App\Models\Ticket();
    return $this->commonShowITILNewItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();

    $data = $this->prepareDataSave($data, $args['id']);

    $this->saveItem($data, $args['id']);

    $uri = $request->getUri();
    return $response
      ->withHeader('Location', (string) $uri)
      ->withStatus(302);

    // Old code

    $myItem = \App\Models\Ticket::find($args['id']);
    $currentUrgency = $myItem->urgency;
    $currentImpact = $myItem->impact;

    // rewrite data with right database name (for dropdown mainly)
    $definitions = $myItem->getDefinitions();
    foreach ($definitions as $def)
    {
      echo "<br>";
      if (property_exists($data, $def['name']))
      {
        if (in_array($def['type'], ['input', 'textarea', 'dropdown']))
        {
          if ($myItem->{$def['name']} != $data->{$def['name']})
          {
            $myItem->{$def['name']} = $data->{$def['name']};
          }
        }
        elseif ($def['type'] == 'dropdown_remote')
        {
          if (isset($def['multiple']))
          {
            // TODO disabled because colision with technician, group technician... and rules
            // $values = $data->{$def['name']};
            // if (!is_array($values))
            // {
            //   if (empty($values))
            //   {
            //     $values = [];
            //   } else {
            //     $values = explode(',', $values);
            //   }
            // }
            // // save
            // $myItem->{$def['name']}()->syncWithPivotValues($values, $def['pivot']);
          }
          elseif ($myItem->{$def['dbname']} != $data->{$def['name']})
          {
            $myItem->{$def['dbname']} = $data->{$def['name']};
          }
        }
      }
    }

    // automatic recalculate if user changes urgence or technician change impact
    // $canpriority = Session::haveRight($this->rightname, self::CHANGEPRIORITY);
    $canpriority = true;
    if (
        (property_exists($data, 'urgency') && $data->urgency != $currentUrgency) ||
        (property_exists($data, 'impact') && $data->impact != $currentImpact)
        //  &&
        // ($canpriority && !$model->isDirty('priority') || !$canpriority)
    )
    {
      $myItem->priority = \App\v1\Controllers\Ticket::computePriority($myItem->urgency, $myItem->impact);
    }

    // TODO manage security, check if can't steal or own the ticket

    // TODO Manage template?

    // test rules, need write with old prepareInputtoupdate
    $input = [
      'name' => $myItem->name,
      'urgency' => $myItem->urgency,
      'priority' => $myItem->priority,
      '_users_id_requester' => [],
      '_users_id_assign' => [],
      '_groups_id_assign' => [],
    ];

    // manage requesters
    $requesters = [];
    if (!empty($data->requester))
    {
      $requesters = explode(',', $data->requester);
      foreach ($requesters as $requester)
      {
        $input['_users_id_requester'][] = $requester;
      }
    }

    // Manage technicians
    $techs = [];
    if (!empty($data->technician))
    {
      $techs = explode(',', $data->technician);
      foreach ($techs as $techId)
      {
        $input['_users_id_assign'][] = $techId;
      }
    }

    // manage technicians groups
    $techgroups = [];
    if (!empty($data->techniciangroup))
    {
      $techgroups = explode(',', $data->techniciangroup);
      foreach ($techgroups as $groupId)
      {
        $input['_groups_id_assign'][] = $groupId;
      }
    }

    $rule = new \App\v1\Controllers\Rules\Ticket();
    $updateData = $rule->processAllRules(
      $input
    );
    // print_r($updateData);
    // exit;

    foreach ($updateData as $field => $value)
    {
      if (isset($myItem->attributes[$field]) && $myItem->{$field} != $value)
      {
        $myItem->{$field} = $value;
      }
    }

    // Manage _additional_groups_assigns
    if (isset($updateData['_additional_groups_assigns']))
    {
      $techgroups = array_merge($techgroups, $updateData['_additional_groups_assigns']);
    }


    // Test for technician
    // $myItem->technician()->sync([2, 3]);
    // check before if not exixts
    // $myItem->technician()->attach(3, ['type' => 2]);

    $myItem->save();

    // Update requesters groups
    $dbGroups = [];
    foreach ($myItem->techniciangroup as $group)
    {
      $dbGroups[] = $group->id;
    }
    // To delete
    $toDelete = array_diff($dbGroups, $techgroups);
    foreach ($toDelete as $groupId)
    {
      $myItem->techniciangroup()->detach($groupId, ['type' => 2]);
    }

    // To add
    $toAdd = array_diff($techgroups, $dbGroups);
    foreach ($toAdd as $groupId)
    {
      $myItem->techniciangroup()->attach($groupId, ['type' => 2]);
    }

    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage("The ticket has been updated successfully");

    $uri = $request->getUri();
    header('Location: ' . (string) $uri);
    exit();
    // return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showStats(Request $request, Response $response, $args)
  {
    global $translator;
    $item = new \App\Models\Ticket();
    $view = Twig::fromRequest($request);

    $myItem = $item::find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/stats');

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

  public function showProblem(Request $request, Response $response, $args)
  {
    global $translator;
    $item = new \App\Models\Ticket();
    $view = Twig::fromRequest($request);

    $myItem = $item::with(['problems'])->find($args['id']);

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/problem');

    // $problems = $myItem->problem()->get()->toArray();
    $problems = [];

    foreach ($myItem->problems as $problem) {
      $problems[] = $problem->toArray();
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('feeds', $item->getFeeds($args['id']));
    $viewData->addData('content', \App\v1\Controllers\Toolbox::convertMarkdownToHtml($myItem->content));
    $viewData->addData('problems', $problems);

    $viewData->addTranslation('attachItem', $translator->translate('Attach to an existant problem'));
    $viewData->addTranslation('selectItem', $translator->translate('Select problem...'));
    $viewData->addTranslation('buttonAttach', $translator->translate('Attach'));
    $viewData->addTranslation('addItem', $translator->translate('Add new problem'));
    $viewData->addTranslation('buttonCreate', $translator->translate('Create'));
    $viewData->addTranslation('attachedItems', $translator->translate('Problems attached'));
    $viewData->addTranslation('updated', $translator->translate('Last update'));
    $viewData->addTranslation('or', $translator->translate('Ou'));

    return $view->render($response, 'subitem/problem.html.twig', (array) $viewData);
  }

  public function postProblem(Request $request, Response $response, $args)
  {
    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'problem') && is_numeric($data->problem))
    {
      $myItem = \App\Models\Ticket::find($args['id']);
      $myItem->problems()->attach((int)$data->problem);

      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage("The ticket has been attached to problem successfully");
    } else {
      // add message to session
      \App\v1\Controllers\Toolbox::addSessionMessage('Error to attache ticket to problem', 'error');
    }

    $uri = $request->getUri();
    header('Location: ' . (string) $uri);
    exit();
  }

  /**
    * Compute Priority
    *
    * @param $urgency   integer from 1 to 5
    * @param $impact    integer from 1 to 5
    *
    * @return integer from 1 to 5 (priority)
   **/
  public static function computePriority($urgency, $impact)
  {
    $priority_matrix = \App\Models\Config::where('context', 'core')->where('name', 'priority_matrix')->first();
    if (!is_null($priority_matrix))
    {
      $matrix = json_decode($priority_matrix->value, true);
      if (isset($matrix[(int) $urgency][(int) $impact]))
      {
        return $matrix[(int) $urgency][(int) $impact];
      }
    }
    // Failback to trivial
    return round(($urgency + $impact) / 2);
  }

  /**
   * Prepare data to save
   *   * manage compute priority
   *   * manage rules
   *   * store in DB the ticket, the users, the groups... so all linked to ticket
   */
  public function prepareDataSave($data, $id = null)
  {

    if (is_null($id))
    {
      $myItem = new \App\Models\Ticket();
    } else {
      $myItem = \App\Models\Ticket::find($id);
    }
    $definitions = $myItem->getDefinitions();


    if (!property_exists($data, 'priority') || ($myItem->priority == $data->priority))
    {
      if (!property_exists($data, 'impact'))
      {
        $data->impact = 3;
      }
      $data->priority = self::computePriority($data->urgency, $data->impact);
    }

    // TODO rules


    return $data;

    ////////////////////////// OLD CODE \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    // global $translator;


    // Fill $input
    $input = [
      'urgency' => 3,
      'impact'  => 3,
    ];
    $propertiesList = ['impact', 'urgency', 'priority', 'name', 'content', 'status', 'category', 'location'];
    if (is_null($id))
    {
      $propertiesList[] = 'content';
    }
    foreach ($propertiesList as $property)
    {
      if (property_exists($data, $property))
      {
        $input[$property] = $data->{$property};
      }
    }

    foreach ($definitions as $def)
    {
      if (isset($def['multiple']))
      {
        if (property_exists($data, $def['name']))
        {
          $input[$def['name']] = [];
          $requesters = explode(',', $data->{$def['name']});
          foreach ($requesters as $requester)
          {
            $input[$def['name']][] = $requester;
          }
        } else {
          $input[$def['name']] = [];
        }
        $input[$def['name']] = array_filter($input[$def['name']]);
      }
    }


    // Convert data to rules
    if (isset($input['category']))
    {
      $input['itilcategories_id'] = $input['category'];
    }
    // TODO itilcategories_id_cn && itilcategories_id_code
    //
    if (isset($input['requester']))
    {
      $input['_users_id_requester'] = $input['requester'];
    }
    // _groups_id_of_requester
    // _locations_id_of_requester
    // _locations_id_of_item
    // _groups_id_of_item
    // _states_id_of_item
    // locations_id
    if (isset($input['requestergroup']))
    {
      $input['_groups_id_requester'] = $input['requestergroup'];
    }
    if (isset($input['technician']))
    {
      $input['_users_id_assign'] = $input['technician'];
    }
    if (isset($input['techniciangroup']))
    {
      $input['_groups_id_assign'] = $input['techniciangroup'];
    } else {
      $input['techniciangroup'] = [];
    }
    // _suppliers_id_assign
    if (isset($input['watcher']))
    {
      $input['_users_id_observer'] = $input['watcher'];
    }
    if (isset($input['watchergroup']))
    {
      $input['_groups_id_observer'] = $input['watchergroup'];
    }
    // requesttypes_id
    // itemtype
    // entities_id
    // profiles_id
    // _mailgate
    // _x-priority
    // slas_id_ttr
    // slas_id_tto
    // olas_id_ttr
    // olas_id_tto
    // _date_creation_calendars_id


    // compute priority
    if ($myItem->priority == $data->priority)
    {
      $input['priority'] = self::computePriority($input['urgency'], $input['impact']);
    }

    // play rules
    $rule = new \App\v1\Controllers\Rules\Ticket();
    $updateData = $rule->processAllRules(
      $input,
    );

    // TODO manage the data returned by the rules
    if (isset($updateData['_additional_groups_assigns']))
    {
      // usage of array_filter is to remove empty value
      $input['techniciangroup'] = array_filter(
        array_merge(
          $input['techniciangroup'],
          $updateData['_additional_groups_assigns']
        )
      );
    }

    // Get multiple because it's many to many relation ship and can't be filled directly in the table
    $exclude = [];
    foreach ($definitions as $def)
    {
      if (isset($def['multiple']))
      {
        $exclude[] = $def['name'];
      }
    }

    $itemFields = [];
    foreach ($definitions as $definition)
    {
      if (!is_null($id) && $definition['name'] == 'content')
      {
        continue;
      }
      $itemFields[] = $definition['name'];
    }
    foreach ($input as $field => $value)
    {
      if ($field == 'category')
      {
        $field = 'category_id';
      }
      if ($field == 'location')
      {
        $field = 'location_id';
      }
      if (
          in_array($field, $itemFields) &&
          $myItem->{$field} != $value &&
          !in_array($field, $exclude)
      )
      {
        $myItem->{$field} = $value;
      }
    }

    $myItem->save();

    // Update multiple items
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
        $dbItems = [];
        foreach ($myItem->$key as $item)
        {
          $dbItems[] = $item->id;
        }
        // To delete
        $toDelete = array_diff($dbItems, $input[$key]);
        foreach ($toDelete as $groupId)
        {
          $myItem->$key()->detach($groupId, $pivot);
        }

        // To add
        $toAdd = array_diff($input[$key], $dbItems);
        foreach ($toAdd as $groupId)
        {
          $myItem->$key()->attach($groupId, $pivot);
        }
      }
    }


    // Update requesters groups
      // $dbGroups = [];
      // foreach ($myItem->techniciangroup as $group)
      // {
      //   $dbGroups[] = $group->id;
      // }
      // // To delete
      // $toDelete = array_diff($dbGroups, $input['techniciangroup']);
      // foreach ($toDelete as $groupId)
      // {
      //   $myItem->techniciangroup()->detach($groupId, ['type' => 2]);
      // }

      // // To add
      // $toAdd = array_diff($input['techniciangroup'], $dbGroups);
      // foreach ($toAdd as $groupId)
      // {
      //   $myItem->techniciangroup()->attach($groupId, ['type' => 2]);
      // }




// print_r($updateData);
// echo "<br>";
// print_r($input);
// exit;

    // update each models (ticket, users, groups...)

    // add message to session
    \App\v1\Controllers\Toolbox::addSessionMessage('Operation successful');

    return $myItem->id;
  }
}
