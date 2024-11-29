<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeItem extends Common
{
  protected $definition = '\App\Models\Definitions\ChangeItem';
  protected $titles = ['Change Item', 'Change Items'];
  protected $icon = 'edit';
  protected $table = 'change_item';
  protected $hasEntityField = false;
}
