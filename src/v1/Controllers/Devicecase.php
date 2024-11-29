<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Devicecase extends Common
{
  protected $model = '\App\Models\Devicecase';
  protected $rootUrl2 = '/devices/devicecases/';
  protected $choose = 'devicecases';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicecase();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicecase();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Devicecase();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
