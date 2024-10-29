<?php

namespace App\v1\Controllers\Forms;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Slim\Routing\RouteContext;

final class Answer extends \App\v1\Controllers\Common
{
  protected $model = '\App\Models\Forms\Answer';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Forms\Answer();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Forms\Answer();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Forms\Answer();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showAnswer(Request $request, Response $response, $args): Response
  {
    global $translator;

    $item = new \App\Models\Forms\Answer();
    $view = Twig::fromRequest($request);

    $myItem = $item::with('form', 'answerquestions')->find($args['id']);

    $answers = [
      'form_id' => $myItem->form->id,
      'form_name' => $myItem->form->name,
      'sections' => [],
    ];

    $item2 = new \App\Models\Forms\Form();
    $myItem2 = $item2::with('sections')->find($myItem->form->id);

    foreach ($myItem2->sections as $section) {
      $answers['sections'][$section->id] = [];
      $answers['sections'][$section->id]['id'] = $section->id;
      $answers['sections'][$section->id]['name'] = $section->name;
      $answers['sections'][$section->id]['questions'] = [];

      $item3 = new \App\Models\Forms\Section();
      $myItem3 = $item3::with('questions')->find($section->id);
      foreach ($myItem3->questions as $question) {
        $answer = '';
        foreach ($myItem->answerquestions as $answerquestion) {
          if ($answerquestion->question_id ==  $question->id) {
            $answer = $answerquestion->answer;
            break;
          }
        }

        $answers['sections'][$section->id]['questions'][$question->id] = [
          'id' => $question->id,
          'name' => $question->name,
          'fieldtype' => $question->fieldtype,
          'answer' => $answer,
        ];
      }
    }


    $rootUrl = $this->getUrlWithoutQuery($request);

    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($myItem, $request);

    $viewData->addRelatedPages($item->getRelatedPages($rootUrl));

    $viewData->addData('fields', $item->getFormData($myItem));
    $viewData->addData('answers', $answers);
    $viewData->addData('src', 'answer');

    return $view->render($response, 'subitem/answers.html.twig', (array)$viewData);
  }
}
