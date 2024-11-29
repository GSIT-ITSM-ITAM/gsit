<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Tickettemplate extends Common
{
  protected $model = '\App\Models\Tickettemplate';
  protected $rootUrl2 = '/dropdowns/ticketemplates/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Tickettemplate();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Tickettemplate();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Tickettemplate();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubMandatoryFields(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('mandatoryfields')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/mandatoryfields');

    $myMandatoryFields = [];
    foreach ($myItem->mandatoryfields as $mandatoryfield)
    {
      $interface = '';
      // if ($mandatoryfield->interface !== null)
      // {
      //   $interface = $mandatoryfield->interface->name;
      // }

      $myMandatoryFields[] = [
        'name'        => $mandatoryfield->num,
        'interface'   => $interface,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('mandatoryfields', $myMandatoryFields);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('interface', $translator->translate('Interface'));

    return $view->render($response, 'subitem/tickettemplatemandatoryfields.html.twig', (array)$viewData);
  }

  public function showSubPredefinedFields(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('predefinedfields')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/predefinedfields');

    $myPredefinedFields = [];
    foreach ($myItem->predefinedfields as $predefinedfield)
    {
      $name = $myItem->num;

      $myPredefinedFields[] = [
        'name'      => $name,
        'value'     => $predefinedfield->value,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('predefinedfields', $myPredefinedFields);

    $viewData->addTranslation('name', $translator->translate('Name'));
    $viewData->addTranslation('value', 'Valeur');

    return $view->render($response, 'subitem/tickettemplatepredefinedfields.html.twig', (array)$viewData);
  }

  public function showSubHiddenFields(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $definitions = $item->getDefinitions();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('hiddenfields')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/hiddenfields');

    $myHiddenFields = [];
    foreach ($myItem->hiddenfields as $hiddenfield)
    {
      $name = $hiddenfield->num;

      $myHiddenFields[] = [
        'name'    => $name,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('hiddenfields', $myHiddenFields);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/tickettemplatehiddenfields.html.twig', (array)$viewData);
  }
}
