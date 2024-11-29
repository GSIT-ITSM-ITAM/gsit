<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemProblem extends Common
{
  protected $definition = '\App\Models\Definitions\ItemProblem';
  protected $titles = ['Problem Item', 'Problem Items'];
  protected $icon = 'edit';
  protected $table = 'item_problem';
  protected $hasEntityField = false;
}
