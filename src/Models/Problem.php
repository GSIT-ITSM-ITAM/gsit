<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Problem extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Problem';
  protected $titles = ['Problem', 'Problems'];
  protected $icon = 'drafting compass';

  protected $appends = [
    // 'category',
    // 'usersidlastupdater',
    // 'usersidrecipient',
    'entity',
    'notes',
    'changes',
    'costs',
    'items',
  ];

  protected $visible = [
    'id',
    'name',
    'created_at',
    'updated_at',
    'category',
    'usersidlastupdater',
    'usersidrecipient',
    'entity',
    'notes',
    'knowbaseitems',
    'requester',
    'requestergroup',
    'technician',
    'techniciangroup',
    'changes',
    'costs',
    'items',
  ];

  protected $with = [
    'category:id,name',
    'usersidlastupdater:id,name,firstname,lastname',
    'usersidrecipient:id,name,firstname,lastname',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'requester:id,name,firstname,lastname',
    'requestergroup:id,name,completename',
    'technician:id,name,firstname,lastname',
    'techniciangroup:id,name,completename',
    'changes',
    'costs',
    'items',
  ];

  public function category(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Category', 'category_id');
  }

  public function usersidlastupdater(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_lastupdater');
  }

  public function usersidrecipient(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_recipient');
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

  public function requester()
  {
    return $this->belongsToMany('\App\Models\User')->wherePivot('type', 1);
  }

  public function requestergroup()
  {
    return $this->belongsToMany('\App\Models\Group')->wherePivot('type', 1);
  }

  public function technician()
  {
    return $this->belongsToMany('\App\Models\User')->wherePivot('type', 2);
  }

  public function techniciangroup()
  {
    return $this->belongsToMany('\App\Models\Group')->wherePivot('type', 2);
  }

  public function changes(): BelongsToMany
  {
    return $this->belongsToMany('\App\Models\Change', 'change_problem', 'problem_id', 'change_id');
  }

  public function costs(): HasMany
  {
    return $this->hasMany('App\Models\Problemcost', 'problem_id');
  }

  public function items(): HasMany
  {
    return $this->hasMany('\App\Models\ItemProblem', 'problem_id');
  }
}
