<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ItemEnclosure extends Common
{
  protected $definition = '\App\Models\Definitions\ItemEnclosure';
  protected $titles = ['Enclosure Item', 'Enclosure Items'];
  protected $icon = 'edit';
  protected $table = 'item_enclosure';
  protected $hasEntityField = false;
}
