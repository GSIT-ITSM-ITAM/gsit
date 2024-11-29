<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Projecttasktemplate extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Projecttasktemplate';
  protected $titles = ['Project task template', 'Project task templates'];
  protected $icon = 'edit';

  protected $appends = [
    'state',
    'type',
    'projecttasks',
    'entity',
  ];

  protected $visible = [
    'state',
    'type',
    'projecttasks',
    'entity',
    'documents',
  ];

  protected $with = [
    'state:id,name',
    'type:id,name',
    'projecttasks:id,name',
    'entity:id,name,completename',
    'documents:id,name',
  ];

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Projectstate', 'projectstate_id');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Projecttasktype', 'projecttasktype_id');
  }

  public function projecttasks(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Projecttask', 'projecttask_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
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
