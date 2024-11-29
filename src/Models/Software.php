<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Software extends Common
{
  use SoftDeletes;

  protected $table = 'softwares';
  protected $definition = '\App\Models\Definitions\Software';
  protected $titles = ['Software', 'Software'];
  protected $icon = 'cube';

  protected $appends = [
    // 'category',
    // 'manufacturer',
    // // 'nbinstallation',
    // 'versions',
    // 'groupstech',
    // 'userstech',
    // 'user',
    // 'group',
    // 'location',
    'entity',
    'infocom',
  ];

  protected $visible = [
    // 'category',
    // 'manufacturer',
    // // 'nbinstallation',
    // 'versions',
    // 'groupstech',
    // 'userstech',
    // 'user',
    // 'group',
    // 'location',
    'entity',
    'domains',
    'appliances',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
    'infocom',
    'reservations',
  ];

  protected $with = [
    // 'category:id,name',
    // 'manufacturer:id,name',
    // // 'nbinstallation.devices',
    'versions:id,name',
    // 'groupstech:id,name,completename',
    // 'userstech:id,name,firstname,lastname',
    // 'user:id,name,firstname,lastname',
    // 'group:id,name,completename',
    // 'location:id,name',
    'entity:id,name,completename',
    'domains:id,name',
    'appliances:id,name',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'infocom',
    'reservations',
  ];

  protected $fillable = [
    'name',
    'entity_id'
  ];

  public function category(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Softwarecategory', 'softwarecategory_id');
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  // public function nbinstallation(): HasMany
  // {
  //   return $this->hasMany('\App\Models\Softwareversion')->withCount('devices');
  // }

  public function versions(): HasMany
  {
    return $this->hasMany('\App\Models\Softwareversion');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }

  public function group(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group');
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function domains(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Domain',
      'item',
      'domain_item'
    )->withPivot(
      'domainrelation_id',
    );
  }

  public function appliances(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Appliance',
      'item',
      'appliance_item'
    );
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

  public function infocom(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Infocom',
      'item',
    );
  }

  public function reservations(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Reservationitem',
      'item',
    );
  }
}
