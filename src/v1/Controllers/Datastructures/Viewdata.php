<?php

namespace App\v1\Controllers\Datastructures;

class Viewdata
{
  use Header;
  use Translation;
  use Information;

  // Attributes
  public object $header;
  public object $data;
  public array $relatedPages;
  public object $translation;
  public object $information;
  public array $message;
  public string $basePath;

  public function __construct($item, $request)
  {
    global $basePath, $translator;

    $this->header = (object)[];
    $this->data = (object)[];
    $this->relatedPages = [];
    $this->translation = (object)[];
    $this->information = (object)[];
    $this->message = [];

    $this->basePath = $basePath;

    $this->initHeaderData($item, $request);
    $this->initInformationData();

    $this->addTranslation('savebutton', $translator->translate('Save'));
    $this->addTranslation('newbutton', $translator->translate('New'));

    $session = new \SlimSession\Helper();
    if ($session->exists('message'))
    {
      $this->message = $session->message;
      $session->delete('message');
    }
  }

  public function addData($key, $value)
  {
    $this->data->{$key} = $value;
  }

  public function addRelatedPages($data)
  {
    $this->relatedPages = $data;
  }

  // public function addMessage($message)
  // {
  //   $this->message[] = $message;
  // }
}
