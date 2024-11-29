<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Devicesensor extends Common
{
  protected $model = '\App\Models\Devicesensor';
  protected $rootUrl2 = '/devices/devicesensors/';
  protected $choose = 'devicesensors';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicesensor();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicesensor();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicesensor();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
