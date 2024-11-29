<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Projecttask extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Projecttask';
  protected $titles = ['Project task', 'Project tasks'];
  protected $icon = 'columns';

  protected $appends = [
    'entity',
    'parent',
    'type',
    'state',
    'tickets',
    'notes',
    'documents',
  ];

  protected $visible = [
    'entity',
    'parent',
    'type',
    'state',
    'tickets',
    'notes',
    'documents',
  ];

  protected $with = [
    'entity:id,name,completename',
    'parent:id,name',
    'type:id,name',
    'state:id,name,color',
    'tickets:id,name',
    'notes:id',
    'documents:id,name',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function parent(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Projecttask', 'projecttask_id');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Projecttasktype', 'projecttasktype_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Projectstate', 'projectstate_id');
  }

  public function tickets(): BelongsToMany
  {
    return $this->belongsToMany('\App\Models\Ticket', 'projecttask_ticket', 'projecttask_id', 'ticket_id');
  }

  public function notes(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Notepad',
      'item',
    );
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
