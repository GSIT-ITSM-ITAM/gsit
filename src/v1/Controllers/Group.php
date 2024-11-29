<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Group extends Common
{
  protected $model = '\App\Models\Group';
  protected $rootUrl2 = '/groups/';
  protected $choose = 'groups';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Group();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Group();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Group();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubUsers(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new \App\Models\User();
    $myItem2 = $item2::with('group')->get();

    $rootUrl = $this->genereRootUrl($request, '/users');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myUsers = [];
    foreach ($myItem2 as $user)
    {
      if ($user->group !== null)
      {
        foreach ($user->group as $group)
        {
          if ($group->pivot->group_id == $args['id'])
          {
            $user_name = $this->genereUserName($user->name, $user->lastname, $user->firstname);

            $url = $this->genereRootUrl2Link($rootUrl2, '/users/', $group->pivot->user_id);

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

            if ($user->is_active == 1)
            {
              $is_active_val = $translator->translate('Yes');
            }
            else
            {
              $is_active_val = $translator->translate('No');
            }

            $myUsers[$group->pivot->user_id] = [
              'name'                  => $user_name,
              'url'                   => $url,
              'auto'                  => $group->pivot->is_dynamic,
              'auto_val'              => $auto_val,
              'is_manager'            => $group->pivot->is_manager,
              'is_manager_val'        => $is_manager_val,
              'is_userdelegate'       => $group->pivot->is_userdelegate,
              'is_userdelegate_val'   => $is_userdelegate_val,
              'is_active'             => $user->is_active,
              'is_active_val'         => $is_active_val,
            ];
          }
        }
      }
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('users', $myUsers);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('auto', $translator->translate('Automatic inventory'));
    $viewData->addTranslation('manager', $translator->translate('Manager'));
    $viewData->addTranslation('userdelegate', $translator->translate('Delegatee'));
    $viewData->addTranslation('active', $translator->translate('Active'));

    return $view->render($response, 'subitem/users.html.twig', (array)$viewData);
  }

  public function showSubGroups(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('parents')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/groups');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myGroups = [];
    foreach ($myItem->parents as $parent)
    {
      $name = $parent->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/groups/', $parent->id);

      $entity = '';
      $entity_url = '';
      if ($parent->entity !== null)
      {
        $entity = $parent->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $parent->entity->id);
      }

      $comment = $parent->comment;

      $myGroups[$parent->id] = [
        'name'          => $name,
        'url'           => $url,
        'entity'        => $entity,
        'entity_url'    => $entity_url,
        'comment'       => $comment,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('groups', $myGroups);
    $viewData->addData('show', $this->choose);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/groups.html.twig', (array)$viewData);
  }
}
