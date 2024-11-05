<?php

namespace App\v1\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

final class Crontaskexecution extends Common
{
  protected $model = '\App\Models\Crontaskexecution';
  protected $startTime = 0;

  public function createExecution(\App\Models\Crontask $crontask)
  {
    $crontaskexecution = new \App\Models\Crontaskexecution();
    $crontaskexecution->crontask_id = $crontask->id;
    $crontaskexecution->state = \App\Models\Crontaskexecution::STATE_RUN;
    $crontaskexecution->save();

    $this->startTime = time();
    return $crontaskexecution->id;
  }

  public function endExecution($id)
  {
    $crontaskexecution = \App\Models\Crontaskexecution::find($id);
    $crontaskexecution->execution_duration = (time() - $this->startTime);
    $crontaskexecution->state = \App\Models\Crontaskexecution::STATE_STOP;
    $crontaskexecution->save();
  }
}
