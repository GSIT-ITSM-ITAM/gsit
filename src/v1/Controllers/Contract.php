<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Contract extends Common
{
  protected $model = '\App\Models\Contract';
  protected $rootUrl2 = '/contracts/';
  protected $choose = 'contracts';
  protected $associateditems_model = '\App\Models\ContractItem';
  protected $associateditems_model_id = 'contract_id';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Contract();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Contract();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Contract();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }
}
