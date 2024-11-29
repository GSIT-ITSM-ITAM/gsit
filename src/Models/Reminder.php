<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Reminder extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Reminder';
  protected $titles = ['Reminder', 'Reminders'];
  protected $icon = 'sticky note';
  protected $hasEntityField = false;

  protected $appends = [
    'user',
  ];

  protected $visible = [
    'user',
    'documents',
  ];

  protected $with = [
    'user:id,name,firstname,lastname',
    'documents:id,name',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }

  public function documents(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Document',
      'item',
      'document_item'
    )->withPivot(
      'document_id',
      'updated_at',
    );
  }
}
