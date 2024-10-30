<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Ticket extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Ticket';
  protected $titles = ['Ticket', 'Tickets'];
  protected $icon = 'hands helping';

  protected $appends = [
    'requester',
    'requestergroup',
    'watcher',
    'watchergroup',
    'technician',
    'techniciangroup',
    'usersidlastupdater',
    'usersidrecipient',
    'category',
    'entity',
    'problems',
    'changes',
    'linkedtickets',
  ];

  protected $visible = [
    'requester',
    'requestergroup',
    'watcher',
    'watchergroup',
    'technician',
    'techniciangroup',
    'usersidlastupdater',
    'usersidrecipient',
    'category',
    'entity',
    'problems',
    'changes',
    'linkedtickets',
    'knowbaseitems',
  ];

  protected $with = [
    'requester:id,name',
    'requestergroup:id,name',
    'watcher:id,name',
    'watchergroup:id,name',
    'technician:id,name',
    'techniciangroup:id,name',
    'usersidlastupdater:id,name',
    'usersidrecipient:id,name',
    'category:id,name',
    'location:id,name',
    'entity:id,name',
    'problems:id,name',
    'changes:id,name',
    'linkedtickets:id,name',
    'knowbaseitems:id,name',
  ];

  // For default values
  protected $attributes = [
    'status'    => 1,
    'urgency'   => 3,
    'impact'    => 3,
    'priority'  => 3,
    'entity_id' => 1,
  ];

  protected $fillable = [
    'name',
    'entity_id',
    'status',
    'user_id_recipient',
    'requesttype_id',
    'content',
    'urgency',
    'impact',
    'priority',
    'category_id',
    'type',
    'location_id',
  ];

  public static function boot()
  {
    parent::boot();

    static::creating(function ($model)
    {
    });

    static::updating(function ($model)
    {
      $currentItem = \App\Models\Ticket::find($model->id);
      // Clean new lines before passing to rules
      if (property_exists($model, 'content'))
      {
        $model->content = preg_replace('/\\\\r\\\\n/', "\n", $model->content);
        $model->content = preg_replace('/\\\\n/', "\n", $model->content);
      }


     // TODO finish
    });

    static::deleting(function ($model)
    {
    });

    // static::restoring(function ($model)
    // {
    // });
  }

  public function requester()
  {
    return $this->belongsToMany('\App\Models\User')->wherePivot('type', 1);
  }

  public function requestergroup()
  {
    return $this->belongsToMany('\App\Models\Group')->wherePivot('type', 1);
  }

  public function watcher()
  {
    return $this->belongsToMany('\App\Models\User')->wherePivot('type', 3);
  }

  public function watchergroup()
  {
    return $this->belongsToMany('\App\Models\Group')->wherePivot('type', 3);
  }

  public function technician()
  {
    return $this->belongsToMany('\App\Models\User')->wherePivot('type', 2);
  }

  public function techniciangroup()
  {
    return $this->belongsToMany('\App\Models\Group')->wherePivot('type', 2);
  }

  public function usersidlastupdater(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_lastupdater');
  }

  public function usersidrecipient(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_recipient');
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Category');
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function problems(): BelongsToMany
  {
    return $this->belongsToMany('\App\Models\Problem');
  }

  public function changes(): BelongsToMany
  {
    return $this->belongsToMany('\App\Models\Change');
  }

  public function linkedtickets(): BelongsToMany
  {
    return $this->belongsToMany('\App\Models\Ticket', 'ticket_ticket', 'ticket_id_1', 'ticket_id_2')->withPivot('link');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function getFeeds($id)
  {
    global $translator;
    $feeds = [];

    $statesDef = $this->definition::getStatusArray();

    // Get followups
    $followups = \App\Models\Followup::where('item_type', 'App\Models\Ticket')->where('item_id', $id)->get();
    foreach ($followups as $followup)
    {
      $usertype = 'user';
      if ($followup->is_tech)
      {
        $usertype = 'tech';
      }
      $username = '';
      if (!is_null($followup->user))
      {
        $username = $followup->user->completename;
      }

      $feeds[] = [
        "user"     => $username,
        "usertype" => $usertype,
        "type"     => "followup",
        "date"     => $followup->created_at,
        "summary"  => $translator->translate('added a followup'),
        "content"  => \App\v1\Controllers\Toolbox::convertMarkdownToHtml($followup['content']),
        "time"     => null,
      ];
    }
    // Get important events in logs :

    $logs = \App\Models\Log::
      where('item_type', 'App\Models\Ticket')
      ->where('item_id', $id)
      ->whereIn('id_search_option', [12, 5, 8])
      ->get();
    foreach ($logs as $log)
    {
      if ($log->id_search_option == 12)
      {
        $userActionSpl = explode(" (", $log->user_name);
        $stateDef = $statesDef[$log->new_value];
        $feeds[] = [
          "user"     => $userActionSpl[0],
          "usertype" => "tech",
          "type"     => "event",
          "date"     => $log->updated_at,
          "summary"  => $translator->translate('changed state to') . " <span class=\"ui " . $stateDef['color'] .
                        " text\"><i class=\"" . $stateDef['icon'] . " icon\"></i>" . $stateDef['title'] . "</span>",
          "content"  => "",
          "time"     => null,
        ];
      }
      elseif ($log->id_search_option == 5)
      {
        $userActionSpl = explode(" (", $log->user_name);
        $userSpl = explode(" (", $log->new_value);

        $feeds[] = [
          "user"     => $userActionSpl[0],
          "usertype" => "tech",
          "type"     => "event",
          "date"     => $log->updated_at,
          "summary"  => $translator->translate('add attribution to the user') . ' ' . $userSpl[0],
          "content"  => "",
          "time"     => null,
        ];
      }
      elseif ($log->id_search_option == 8)
      {
        $userActionSpl = explode(" (", $log->user_name);
        if (!is_null($log->new_value))
        {
          $groupSpl = explode(" (", $log->new_value);
          $feeds[] = [
            "user"     => $userActionSpl[0],
            "usertype" => "tech",
            "type"     => "event",
            "date"     => $log->updated_at,
            "summary"  => $translator->translate('add (+) attribution to the group') . ' ' . $groupSpl[0],
            "content"  => "",
            "time"     => null,
          ];
        } else {
          $groupSpl = explode(" (", $log->old_value);
          $feeds[] = [
            "user"     => $userActionSpl[0],
            "usertype" => "tech",
            "type"     => "event",
            "date"     => $log->updated_at,
            "summary"  => $translator->translate('delete (-) attribution to the group') . ' ' . $groupSpl[0],
            "content"  => "",
            "time"     => null,
          ];
        }
      }
    }

    // sort
    usort($feeds, function ($a, $b)
    {
      return $a['date'] > $b['date'];
    });
    return $feeds;
  }

  /**
   * Get the color of the status of the ticket
   */
  public function getColor()
  {
    $statesDef = $this->definition::getStatusArray();
    return $statesDef[$this->status]['color'];
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
}
