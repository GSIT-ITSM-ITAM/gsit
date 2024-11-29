<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ItemSoftwarelicence extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\ItemSoftwarelicence';
  protected $titles = ['Softwarelicence Item', 'Softwarelicence Items'];
  protected $icon = 'edit';
  protected $table = 'item_softwarelicense';
  protected $hasEntityField = false;
}
