<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Cluster extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Cluster';
  protected $titles = ['Cluster', 'Clusters'];
  protected $icon = 'project diagram';

  protected $appends = [
    'type',
    'state',
    'userstech',
    'groupstech',
    'entity',
    'appliances',
  ];

  protected $visible = [
    'type',
    'state',
    'userstech',
    'groupstech',
    'entity',
    'appliances',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
  ];

  protected $with = [
    'type:id,name',
    'state:id,name',
    'userstech:id,name,firstname,lastname',
    'groupstech:id,name,completename',
    'entity:id,name,completename',
    'appliances:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
  ];

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Clustertype', 'clustertype_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
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

  public function appliances(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Appliance',
      'item',
      'appliance_item'
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

  public function tickets(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Ticket',
      'item',
      'item_ticket'
    )->withPivot(
      'ticket_id',
    );
  }

  public function problems(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Problem',
      'item',
      'item_problem'
    )->withPivot(
      'problem_id',
    );
  }

  public function changes(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Change',
      'item',
      'change_item'
    )->withPivot(
      'change_id',
    );
  }
}
