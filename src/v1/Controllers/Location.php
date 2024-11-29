<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Location extends Common
{
  protected $model = '\App\Models\Location';
  protected $rootUrl2 = '/dropdowns/locations/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Location();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Location();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Location();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubLocations(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item->find($args['id']);

    $item2 = new $this->model();
    $myItem2 = $item2->where('location_id', $args['id'])->get();

    $rootUrl = $this->genereRootUrl($request, '/locations');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $myLocations = [];
    foreach ($myItem2 as $current_location)
    {
      $name = $current_location->name;

      $url = $this->genereRootUrl2Link($rootUrl2, '/dropdowns/locations/', $current_location->id);

      $entity = '';
      $entity_url = '';
      if ($current_location->entity !== null)
      {
        $entity = $current_location->entity->completename;
        $entity_url = $this->genereRootUrl2Link($rootUrl2, '/entities/', $current_location->entity->id);
      }

      $address = $current_location->address;

      $postcode = $current_location->postcode;

      $town = $current_location->town;

      $state = $current_location->state;

      $country = $current_location->country;

      $building = $current_location->building;

      $room = $current_location->room;

      $latitude = $current_location->latitude;

      $longitude = $current_location->longitude;

      $altitude = $current_location->altitude;

      $comment = $current_location->comment;

      $myLocations[$current_location->id] = [
        'name'             => $name,
        'url'              => $url,
        'entity'           => $entity,
        'entity_url'       => $entity_url,
        'address'          => $address,
        'postcode'         => $postcode,
        'town'             => $town,
        'state'            => $state,
        'country'          => $country,
        'building'         => $building,
        'room'             => $room,
        'latitude'         => $latitude,
        'longitude'        => $longitude,
        'altitude'         => $altitude,
        'comment'          => $comment,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('locations', $myLocations);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('entity', $translator->translatePlural('Entity', 'Entities', 1));
    $viewData->addTranslation('address', $translator->translate('Address'));
    $viewData->addTranslation('postcode', $translator->translate('Postal code'));
    $viewData->addTranslation('town', $translator->translate('City'));
    $viewData->addTranslation('state', $translator->translate('location' . "\004" . 'State'));
    $viewData->addTranslation('country', $translator->translate('Country'));
    $viewData->addTranslation('building', $translator->translate('Building number'));
    $viewData->addTranslation('room', $translator->translate('Room number'));
    $viewData->addTranslation('latitude', $translator->translate('Latitude'));
    $viewData->addTranslation('longitude', $translator->translate('Longitude'));
    $viewData->addTranslation('altitude', $translator->translate('Altitude'));
    $viewData->addTranslation('comment', $translator->translatePlural('Comment', 'Comments', 2));

    return $view->render($response, 'subitem/locations.html.twig', (array)$viewData);
  }
}
