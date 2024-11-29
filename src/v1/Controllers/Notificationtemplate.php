<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class Notificationtemplate extends Common
{
  protected $model = '\App\Models\Notificationtemplate';

  public function getAll(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Notificationtemplate();
    return $this->commonGetAll($request, $response, $args, $item);
  }

  public function showItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Notificationtemplate();
    return $this->commonShowItem($request, $response, $args, $item);
  }

  public function updateItem(Request $request, Response $response, $args): Response
  {
    $item = new \App\Models\Notificationtemplate();
    return $this->commonUpdateItem($request, $response, $args, $item);
  }

  public function showSubTemplatetranslations(Request $request, Response $response, $args): Response
  {
    global $translator;

    $view = Twig::fromRequest($request);

    $template = \App\Models\Notificationtemplate::find($args['id']);
    $notificationctrl = new \App\v1\Controllers\Notification();
    $templatetrs = \App\Models\Notificationtemplatetranslation::where('notificationtemplate_id', $args['id'])->get();
    $dataTranslate = [];
    foreach ($templatetrs as $templatetr)
    {
      $datat = [
        'id'            => $templatetr->id,
        'lang'          => $templatetr->language,
        'subject'       => true,
        'subject_error' => '',
        'text'          => false,
        'text_error'    => '',
        'html'          => true,
        'html_error'    => '',
      ];
      if (empty($datat['lang']))
      {
        $datat['lang'] = $translator->translate('Default translation');
      }

      // verification of templates
      $className = $template->item_type;
      if ($className == 'Ticket')
      {
        $className = '\App\Models\Ticket';
      }
      $item = new $className();

      try
      {
        $notificationctrl->render($templatetr->subject, $item);
      }
      catch (\Throwable $th)
      {
        $datat['subject_error'] = $th->getMessage();
        $datat['subject'] = false;
      }
      try
      {
        $notificationctrl->render($templatetr->content_text, $item);
      }
      catch (\Throwable $th)
      {
        $datat['text_error'] = $th->getMessage();
        $datat['text'] = false;
      }
      try {
        $notificationctrl->render($templatetr->content_html, $item);
      }
      catch (\Throwable $th)
      {
        $datat['html_error'] = $th->getMessage();
        $datat['html'] = false;
      }

      $dataTranslate[] = $datat;
    }

    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/templatetranslation');

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($template, $request);

    $viewData->addRelatedPages($template->getRelatedPages($rootUrl));

    $viewData->addData('fields', $template->getFormData($template));
    $viewData->addData('translations', $dataTranslate);

    return $view->render($response, 'subitem/notificationtemplateslist.html.twig', (array)$viewData);
  }

  public function showSubTemplatetranslation(Request $request, Response $response, $args): Response
  {
    $view = Twig::fromRequest($request);
    $template = \App\Models\Notificationtemplate::find($args['id']);
    $templatetr = \App\Models\Notificationtemplatetranslation::find($args['translationid']);


    $rootUrl = $this->getUrlWithoutQuery($request);
    $rootUrl = rtrim($rootUrl, '/templatetranslation');

    // form data
    $viewData = new \App\v1\Controllers\Datastructures\Viewdata($templatetr, $request);

    $viewData->addRelatedPages($template->getRelatedPages($rootUrl));

    $viewData->addData('fields', $templatetr->getFormData($templatetr));
    // $viewData->addData('translations', $dataTranslate);

    return $view->render($response, 'genericForm.html.twig', (array)$viewData);
  }
}
