<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class User extends Common
{
  protected $model = '\App\Models\User';
  protected $rootUrl2 = '/users/';
  protected $choose = 'users';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\User();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\User();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\User();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubAuthorization(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\User();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('profiles')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/authorization');

    $profiles = [];
    foreach ($myItem->profiles as $profile)
    {
      $entity = \App\Models\Entity::find($profile->pivot->entity_id);
      $profiles[] = [
        'id'      => $profile->id,
        'name'    => $profile->name,
        'entity'  => [
          'id'    => $profile->pivot->entity_id,
          'name'  => $entity->name,
        ],
        'is_recursive' => $profile->pivot->is_recursive,
      ];
    }

    $form = [
      [
        'id'        => 1,
        'title'     => $translator->translatePlural('Entity', 'Entities', 1),
        'type'      => 'dropdown_remote',
        'name'      => 'entity',
        'dbname'    => 'entity_id',
        'itemtype'  => '\App\Models\Entity',
        'fillable'  => true,
        'display'   => true,
      ],
      [
        'id'        => 3,
        'title'     => $translator->translate('Child entities'),
        'type'      => 'boolean',
        'name'      => 'is_recursive',
        'fillable'  => true,
        'display'   => true,
      ],
      [
        'id'        => 2,
        'title'     => $translator->translatePlural('Profile', 'Profiles', 1),
        'type'      => 'dropdown_remote',
        'name'      => 'profile',
        'dbname'    => 'profile_id',
        'itemtype'  => '\App\Models\Profile',
        'fillable'  => true,
        'display'   => true,
      ],
    ];

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('profiles', $profiles);
    $viewData->addData('form', $form);

    return $view->render($response, 'subitem/authorization.html.twig', (array)$viewData);
  }

  public function itemSubAuthorization(Request $request, Response $response, $args): Response
  {
    $data = (object) $request->getParsedBody();

    if (property_exists($data, 'delete'))
    {
      if (
          property_exists($data, 'entity_id') &&
          property_exists($data, 'is_recursive') &&
          property_exists($data, 'profile_id')
      )
      {
        $user = \App\Models\User::find($args['id']);
        $user->profiles()
          ->wherePivot('entity_id', $data->entity_id)
          ->wherePivot('is_recursive', $data->is_recursive)
          ->detach($data->profile_id);
      }
    }
    else
    {
      $user = \App\Models\User::find($args['id']);
      $profile = \App\Models\Profile::find($data->profile);
      $entity = \App\Models\Entity::find($data->entity);
      if (!is_null($user) && !is_null($profile) && !is_null($entity))
      {
        $recursive = false;
        if (property_exists($data, 'is_recursive') && $data->is_recursive == 'on')
        {
          $recursive = true;
        }
        $user->profiles()->attach(
          $profile->id,
          ['is_recursive' => $recursive, 'entity_id' => $entity->id]
        );
      }
    }

    $uri = $request->getUri();
    header('Location: ' . (string) $uri);
    exit();
  }

  public function showSubGroups(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('group')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/groups');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myGroups = [];
    foreach ($myItem->group as $group)
    {
      $url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $group->pivot->group_id);

      if ($group->pivot->is_dynamic == 1)
      {
        $auto_val = $translator->translate('Yes');
      }
      else
      {
        $auto_val = $translator->translate('No');
      }

      if ($group->pivot->is_manager == 1)
      {
        $is_manager_val = $translator->translate('Yes');
      }
      else
      {
        $is_manager_val = $translator->translate('No');
      }

      if ($group->pivot->is_userdelegate == 1)
      {
        $is_userdelegate_val = $translator->translate('Yes');
      }
      else
      {
        $is_userdelegate_val = $translator->translate('No');
      }

      $myGroups[] = [
        'name'                    => $group->completename,
        'url'                     => $url,
        'auto'                    => $group->pivot->is_dynamic,
        'auto_val'                => $auto_val,
        'is_manager'              => $group->pivot->is_manager,
        'is_manager_val'          => $is_manager_val,
        'is_userdelegate'         => $group->pivot->is_userdelegate,
        'is_userdelegate_val'     => $is_userdelegate_val,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('groups', $myGroups);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('manager', $translator->translate('Manager'));
    $viewData->addTranslation('userdelegate', $translator->translate('Delegatee'));

    return $view->render($response, 'subitem/groups.html.twig', (array)$viewData);
  }

  public function showSubReservations(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $myItem2 = \App\Models\Reservation::where('user_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/reservations');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myReservations = [];
    $myReservations_old = [];
    foreach ($myItem2 as $current_reservation)
    {
      $myItem3 = \App\Models\Reservationitem::where('id', $current_reservation->reservationitem_id)->get();
      if ($myItem3 !== null)
      {
        foreach ($myItem3 as $current_reservationitem)
        {
          $item4 = new $current_reservationitem->item_type();
          $myItem4 = $item4->find($current_reservationitem->item_id);
          if ($myItem4 !== null)
          {
            $type_fr = $item4->getTitle();
            $type = $item4->getTable();

            $current_id = $myItem4->id;


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


            $item_name = $myItem4->name;
            if ($item_name == '')
            {
              $item_name = '(' . $myItem4->id . ')';
            }

            $item_url = $this->genereRootUrl2Link($rootUrl2, '/' . $type . '/', $myItem4->id);

            $entity = '';
            $entity_url = '';
            if ($current_reservationitem->entity !== null)
            {
              $entity = $current_reservationitem->entity->completename;
              $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $current_reservationitem->entity->id);
            }

            if ($end < date('Y-m-d H:i:s'))
            {
              $myReservations_old[] = [
                'begin'         => $begin,
                'end'           => $end,
                'user'          => $user,
                'user_url'      => $user_url,
                'comment'       => $comment,
                'item_name'     => $item_name,
                'item_url'      => $item_url,
                'entity'        => $entity,
                'entity_url'    => $entity_url,
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
                'item_name'     => $item_name,
                'item_url'      => $item_url,
                'entity'        => $entity,
                'entity_url'    => $entity_url,
              ];
            }
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
    $viewData->addTranslation('item', $translator->translatePlural('Item', 'Items', 1));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));

    return $view->render($response, 'subitem/reservations.html.twig', (array)$viewData);
  }
}
