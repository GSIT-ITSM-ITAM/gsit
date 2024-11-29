<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Change extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Change';
  protected $titles = ['Change', 'Changes'];
  protected $icon = 'paint roller';

  protected $appends = [
    'itilcategorie',
    'usersidlastupdater',
    'usersidrecipient',
    'entity',
    'notes',
    'costs',
    'items',
    'approvals',
  ];

  protected $visible = [
    'itilcategorie',
    'usersidlastupdater',
    'usersidrecipient',
    'entity',
    'notes',
    'knowbaseitems',
    'requester',
    'requestergroup',
    'technician',
    'techniciangroup',
    'costs',
    'items',
    'approvals',
  ];

  protected $with = [
    'itilcategorie:id,name',
    'usersidlastupdater:id,name,firstname,lastname',
    'usersidrecipient:id,name,firstname,lastname',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'requester:id,name,firstname,lastname',
    'requestergroup:id,name,completename',
    'technician:id,name,firstname,lastname',
    'techniciangroup:id,name,completename',
    'costs',
    'items',
    'approvals',
  ];

  public function itilcategorie(): BelongsTo
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

  public function costs(): HasMany
  {
    return $this->hasMany('App\Models\Changecost', 'change_id');
  }

  public function getFeeds($id)
  {
    global $translator;
    $feeds = [];

    return $feeds;
  }

  public function items(): HasMany
  {
    return $this->hasMany('\App\Models\ChangeItem', 'change_id');
  }

  public function approvals(): HasMany
  {
    return $this->hasMany('\App\Models\Changevalidation', 'change_id');
  }
}
