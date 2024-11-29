<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeProblem extends Common
{
  protected $definition = '\App\Models\Definitions\ChangeProblem';
  protected $titles = ['Change Problem', 'Change Problem'];
  protected $icon = 'edit';
  protected $table = 'change_problem';
  protected $hasEntityField = false;
}
