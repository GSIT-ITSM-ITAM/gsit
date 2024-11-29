<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Softwarelicense extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Softwarelicense';
  protected $titles = ['License', 'Licenses'];
  protected $icon = 'key';

  protected $appends = [
    'location',
    'softwarelicensetype',
    'userstech',
    'groupstech',
    'user',
    'group',
    'state',
    'softwareversionsBuy',
    'softwareversionsUse',
    'manufacturer',
    'software',
    'entity',
    'certificates',
    'notes',
    'childs',
    'infocom',
  ];

  protected $visible = [
    'location',
    'softwarelicensetype',
    'userstech',
    'groupstech',
    'user',
    'group',
    'state',
    'softwareversionsBuy',
    'softwareversionsUse',
    'manufacturer',
    'software',
    'entity',
    'certificates',
    'notes',
    'knowbaseitems',
    'documents',
    'contracts',
    'tickets',
    'problems',
    'changes',
    'childs',
    'infocom',
  ];

  protected $with = [
    'location:id,name',
    'softwarelicensetype',
    'userstech:id,name,firstname,lastname',
    'groupstech:id,name,completename',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'state',
    'softwareversionsBuy',
    'softwareversionsUse',
    'manufacturer:id,name',
    'software:id,name',
    'entity:id,name,completename',
    'certificates:id,name',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'contracts:id,name',
    'tickets:id,name',
    'problems:id,name',
    'changes:id,name',
    'childs:id,name',
    'infocom',
  ];

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function softwarelicensetype(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Softwarelicensetype');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }

  public function group(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State', 'state_id');
  }

  public function softwareversionsBuy(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Softwareversion', 'softwareversion_id_buy');
  }

  public function softwareversionsUse(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Softwareversion', 'softwareversion_id_use');
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function software(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Software');
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

  public function childs(): HasMany
  {
    return $this->hasMany('\App\Models\Softwarelicense');
  }

  public function infocom(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Infocom',
      'item',
    );
  }
}
