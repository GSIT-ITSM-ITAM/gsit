<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystem extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Operatingsystem';
  protected $titles = ['Operating system', 'Operating systems'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
