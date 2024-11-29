<?php

namespace App\v1\Controllers\Forms;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class Section extends \App\v1\Controllers\Common
{
  protected $model = '\App\Models\Forms\Section';
  protected $rootUrl2 = '/sections/';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Forms\Section();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = \App\Models\Forms\Section::find($args['id']);
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Forms\Section();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubQuestions(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('questions')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/questions');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $questions = [];
    foreach ($myItem->questions as $question)
    {
      $questions[] = [
        'id'    => $question->id,
        'name'  => $question->name,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('questions', $questions);
    $viewData->addData('src', 'section');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('question', $translator->translatePlural('Question', 'Questions', 1));

    return $view->render($response, 'subitem/questions.html.twig', (array)$viewData);
  }

  public function showSubForms(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new $this->model();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('forms')->find($args['id']);

    $rootUrl = $this->genereRootUrl($request, '/forms');
    $rootUrl2 = $this->genereRootUrl2($rootUrl, $this->rootUrl2 . $args['id']);

    $forms = [];
    foreach ($myItem->forms as $form)
    {
      $forms[] = [
        'id'    => $form->id,
        'name'  => $form->name,
      ];
    }

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);
    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('forms', $forms);
    $viewData->addData('src', 'section');
    $viewData->addData('rootUrl2', $rootUrl2);

    $viewData->addTranslation('name', $translator->translate('Name'));

    return $view->render($response, 'subitem/forms.html.twig', (array)$viewData);
  }

  protected function getInformationTop($item, $request)
  {
    global $translator;

    $myItem = $item::with('forms')->find($item->id);

    $tabInfos = [];
    foreach ($myItem->forms as $form)
    {
      $tabInfos[] =
        [
          'key'   => 'form_' . $form->id,
          'value' => $translator->translate('Formulaire impacté') . ' : ' . $form->name,
          'link'  => null,
        ];
    }

    return $tabInfos;
  }
}
