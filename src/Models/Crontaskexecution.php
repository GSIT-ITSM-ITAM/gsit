<?php

namespace App\Models;

class Crontaskexecution extends Common
{
  protected $definition = '\App\Models\Definitions\Crontaskexecution';
  protected $titles = ['Execution', 'Executions'];
  protected $icon = 'cogs';
  protected $hasEntityField = false;

  public const STATE_START = 0;
  public const STATE_RUN   = 1;
  public const STATE_STOP  = 2;
  public const STATE_ERROR = 3;
}
