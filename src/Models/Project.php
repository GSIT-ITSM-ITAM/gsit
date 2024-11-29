<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Project';
  protected $titles = ['Project', 'Projects'];
  protected $icon = 'columns';

  protected $appends = [
    'type',
    'state',
    'user',
    'group',
    'entity',
    'notes',
  ];

  protected $visible = [
    'type',
    'state',
    'user',
    'group',
    'entity',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
    'tasks',
    'costs',
    'parents',
  ];

  protected $with = [
    'type:id,name',
    'state',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tasks',
    'parents:id,name',
    'costs:id,name',
  ];


  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Projecttype', 'projecttype_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Projectstate', 'projectstate_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }

  public function group(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function notes(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Notepad',
      'item',
    );
  }

  public function knowbaseitems(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Knowbaseitem',
      'item',
      'knowbaseitem_item'
    )->withPivot(
      'knowbaseitem_id',
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

  public function contracts(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Contract',
      'item',
      'contract_item'
    )->withPivot(
      'contract_id',
    );
  }

  public function tasks(): HasMany
  {
    return $this->hasMany('\App\Models\Projecttask', 'project_id');
  }

  public function parents(): HasMany
  {
    return $this->hasMany('\App\Models\Project', 'project_id');
  }

  public function costs(): HasMany
  {
    return $this->hasMany('App\Models\Projectcost');
  }
}
