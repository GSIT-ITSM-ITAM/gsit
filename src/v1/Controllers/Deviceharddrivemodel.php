<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;

final class Deviceharddrivemodel extends Common
{
  protected $model = '\App\Models\Deviceharddrivemodel';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Deviceharddrivemodel();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Deviceharddrivemodel();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Deviceharddrivemodel();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
