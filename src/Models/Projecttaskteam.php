<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projecttaskteam extends Common
{
  protected $definition = '\App\Models\Definitions\Projecttaskteam';
  protected $titles = ['Project task team', 'Project task teams'];
  protected $icon = 'columns';
  protected $hasEntityField = false;
}
