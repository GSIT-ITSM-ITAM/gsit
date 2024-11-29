<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Entity extends Common
{
  protected $model = '\App\Models\Entity';
  protected $rootUrl2 = '/entities/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Entity();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Entity();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Entity();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubAddress(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/address');

    $address = [];
    foreach ($myItem as $os)
    {
      $address = [
        'phonenumber'   => $myItem->phonenumber,
        'fax'           => $myItem->fax,
        'website'       => $myItem->website,
        'email'         => $myItem->email,
        'address'       => $myItem->address,
        'postcode'      => $myItem->postcode,
        'town'          => $myItem->town,
        'state'         => $myItem->state,
        'country'       => $myItem->country,
        'longitude'     => $myItem->longitude,
        'latitude'      => $myItem->latitude,
        'altitude'      => $myItem->altitude,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $getDefs = $item->getSpecificFunction('getDefinitionAddress');
    $myItemData = [
      'phonenumber'   => $address['phonenumber'],
      'fax'           => $address['fax'],
      'website'       => $address['website'],
      'email'         => $address['email'],
      'address'       => $address['address'],
      'postcode'      => $address['postcode'],
      'town'          => $address['town'],
      'state'         => $address['state'],
      'country'       => $address['country'],
      'longitude'     => $address['longitude'],
      'latitude'      => $address['latitude'],
      'altitude'      => $address['altitude'],
    ];
    $myItemDataObject = json_decode(json_encode($myItemData));

    $viewData->addData('fields', $item->getFormData($myItemDataObject, $getDefs));

    return $view->render($response, 'subitem/adress.html.twig', (array)$viewData);
  }

  public function showSubEntities(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->model();
    $myItem2 = $item2::where('entity_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/entities');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myEntities = [];
    foreach ($myItem2 as $child)
    {
      $name = $child->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $child->id);

      $entity = '';
      $entity_url = '';
      if ($child->entity !== null)
      {
        $entity = $child->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $child->entity->id);
      }

      $comment = $child->comment;

      $myEntities[$child->id] = [
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
    $viewData->addData('entities', $myEntities);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/entities.html.twig', (array)$viewData);
  }

  public function showSubUsers(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('profilesusers')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/users');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myProfilesUsers = [];
    foreach ($myItem->profilesusers as $profileuser)
    {
      $user = \App\Models\User::find($profileuser->user_id);
      $profile = \App\Models\Profile::find($profileuser->profile_id);
      if (($user !== null) && ($profile !== null))
      {
        if (array_key_exists($profile->id, $myProfilesUsers) !== true)
        {
          $myProfilesUsers[$profile->id] = [
            'name'    => $profile->name,
            'users'   => [],
          ];
        }

        $auto = $profileuser->is_dynamic;
        if ($profileuser->is_dynamic == 1)
        {
          $auto_val = $translator->translate('Yes');
        }
        else
        {
          $auto_val = $translator->translate('No');
        }

        $recursive = $profileuser->is_recursive;
        if ($profileuser->is_recursive == 1)
        {
          $recursive_val = $translator->translate('Yes');
        }
        else
        {
          $recursive_val = $translator->translate('No');
        }

        $user_name = $this->genereUserName($user->name, $user->lastname, $user->firstname);

        $myProfilesUsers[$profile->id]['users'][$user->id] = [
          'name'             => $user_name,
          'auto'             => $auto,
          'auto_val'         => $auto_val,
          'recursive'        => $recursive,
          'recursive_val'    => $recursive_val,
        ];
      }
    }

    // tri ordre alpha
    uasort($myProfilesUsers, function ($a, $b)
    {
      return strtolower($a['name']) > strtolower($b['name']);
    });

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('profilesusers', $myProfilesUsers);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('is_dynamic', $translator->translate('Dynamic'));
    $viewData->addTranslation('is_recursive', $translator->translate('Recursive'));
    $viewData->addTranslation('profil', $translator->translatePlural('Profile', 'Profiles', 1));


    return $view->render($response, 'subitem/profilesusers.html.twig', (array)$viewData);
  }
}
