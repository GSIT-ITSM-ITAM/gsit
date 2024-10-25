<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Phonetype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Phonetype';
  protected $titles = ['Phone type', 'Phone types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
