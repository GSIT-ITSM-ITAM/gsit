<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Domain extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Domain';
  protected $titles = ['Domain', 'Domains'];
  protected $icon = 'globe americas';

  protected $appends = [
    'type',
    'userstech',
    'groupstech',
    'entity',
    'certificates',
  ];

  protected $visible = [
    'type',
    'userstech',
    'groupstech',
    'entity',
    'certificates',
    'documents',
    'contracts',
    'records',
  ];

  protected $with = [
    'type:id,name',
    'userstech:id,name',
    'groupstech:id,name',
    'entity:id,name',
    'certificates:id,name',
    'documents:id,name',
    'contracts:id,name',
    'records:id,name',
  ];

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Domaintype', 'domaintype_id');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function certificates(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Certificate',
      'item',
      'certificate_item'
    )->withPivot(
      'certificate_id',
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

  public function records(): HasMany
  {
    return $this->hasMany('App\Models\Domainrecord');
  }
}
