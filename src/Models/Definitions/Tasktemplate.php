<?php

namespace App\Models\Definitions;

class Tasktemplate
{
  public static function getDefinition()
  {
    global $translator;

    $MINUTE_TIMESTAMP = 60;
    $HOUR_TIMESTAMP = 3600;
    $DAY_TIMESTAMP = 86400;
    $WEEK_TIMESTAMP = 604800;
    $MONTH_TIMESTAMP = 2592000;

    $toadd = [];
    for ($i = 9; $i <= 100; $i++)
    {
       $toadd[] = $i * $HOUR_TIMESTAMP;
    }

    return [
      [
        'id'    => 14,
        'title' => $translator->translate('Name'),
        'type'  => 'input',
        'name'  => 'name',
        'fillable' => true,
      ],
      [
        'id'    => 4,
        'title' => $translator->translate('Content'),
        'type'  => 'textarea',
        'name'  => 'content',
        'fillable' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translatePlural('Task category', 'Task categories', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'category',
        'dbname' => 'taskcategory_id',
        'itemtype' => '\App\Models\Taskcategory',
        'fillable' => true,
      ],
      [
        'id'    => 10,
        'title' => $translator->translate('Status'),
        'type'  => 'dropdown',
        'name'  => 'state',
        'dbname'  => 'state',
        'values' => self::getStateArray(),
        'fillable' => true,
      ],
      [
        'id'    => 5,
        'title' => $translator->translate('Private'),
        'type'  => 'boolean',
        'name'  => 'is_private',
        'fillable' => true,
      ],
      [
        'id'    => 9,
        'title' => $translator->translate('Total duration'),
        'type'  => 'dropdown',
        'name'  => 'actiontime',
        'dbname'  => 'actiontime',
        'values' => self::getTimestampArray(
          [
            'min'             => 0,
            'max'             => 8 * $HOUR_TIMESTAMP,
            'addfirstminutes' => true,
            'inhours'         => true,
            'toadd'           => $toadd
          ]
        ),
        'fillable' => true,
      ],
      [
        'id'    => 7,
        'title' => $translator->translate('By'),
        'type'  => 'dropdown_remote',
        'name'  => 'users',
        'dbname' => 'user_id_tech',
        'itemtype' => '\App\Models\User',
        'fillable' => true,
      ],
      [
        'id'    => 8,
        'title' => $translator->translatePlural('Group', 'Groups', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'groups',
        'dbname' => 'group_id_tech',
        'itemtype' => '\App\Models\Group',
        'fillable' => true,
      ],
      //  [
      //    'id'    => 80,
      //    'title' => $translator->translatePlural('Entity', 'Entities', 1),
      //    'type'  => 'dropdown_remote',
      //    'name'  => 'completename',
      //    'itemtype' => '\App\Models\Entity',
      //  ],
      [
        'id'    => 86,
        'title' => $translator->translate('Child entities'),
        'type'  => 'boolean',
        'name'  => 'is_recursive',
        'fillable' => true,
      ],
      [
        'id'    => 19,
        'title' => $translator->translate('Last update'),
        'type'  => 'datetime',
        'name'  => 'updated_at',
        'readonly'  => 'readonly',
      ],
      [
        'id'    => 121,
        'title' => $translator->translate('Creation date'),
        'type'  => 'datetime',
        'name'  => 'created_at',
        'readonly'  => 'readonly',
      ],
    ];
  }

  public static function getStateArray()
  {
    global $translator;
    return [
      0 => [
        'title' => $translator->translatePlural('Information', 'Information', 1),
      ],
      1 => [
        'title' => $translator->translate('To do'),
      ],
      2 => [
        'title' => $translator->translate('Done'),
      ],
    ];
  }

  public static function getTimestampArray($options = [])
  {
    global $translator;

    $MINUTE_TIMESTAMP = 60;
    $HOUR_TIMESTAMP = 3600;
    $DAY_TIMESTAMP = 86400;
    $WEEK_TIMESTAMP = 604800;
    $MONTH_TIMESTAMP = 2592000;


    $params = [];
    $params['min']                 = 0;
    $params['max']                 = $DAY_TIMESTAMP;
    $params['step']                = 5 * $MINUTE_TIMESTAMP;
    $params['addfirstminutes']     = false;
    $params['toadd']               = [];
    $params['inhours']             = false;

    if (is_array($options) && count($options))
    {
      foreach ($options as $key => $val)
      {
          $params[$key] = $val;
      }
    }

    $params['min'] = floor($params['min'] / $params['step']) * $params['step'];

    if ($params['min'] == 0)
    {
      $params['min'] = $params['step'];
    }

    $values = [];

    if ($params['addfirstminutes'])
    {
      $max = max($params['min'], 10 * $MINUTE_TIMESTAMP);
      for ($i = $MINUTE_TIMESTAMP; $i < $max; $i += $MINUTE_TIMESTAMP)
      {
          $values[$i] = '';
      }
    }

    for ($i = $params['min']; $i <= $params['max']; $i += $params['step'])
    {
      $values[$i] = '';
    }

    if (count($params['toadd']))
    {
      foreach ($params['toadd'] as $key)
      {
          $values[$key] = '';
      }
      ksort($values);
    }

    foreach ($values as $i => $val)
    {
      if (empty($val))
      {
        if ($params['inhours'])
        {
          $day  = 0;
          $hour = floor($i / $HOUR_TIMESTAMP);
        }
        else
        {
          $day  = floor($i / $DAY_TIMESTAMP);
          $hour = floor(($i % $DAY_TIMESTAMP) / $HOUR_TIMESTAMP);
        }
        $minute     = floor(($i % $HOUR_TIMESTAMP) / $MINUTE_TIMESTAMP);
        if ($minute === '0')
        {
          $minute = '00';
        }
        $values[$i] = '';
        if ($day > 0)
        {
          if (($hour > 0) || ($minute > 0))
          {
            if ($minute < 10)
            {
              $minute = '0' . $minute;
            }

            //TRANS: %1$d is the number of days, %2$d the number of hours,
            //       %3$s the number of minutes : display 1 day 3h15
            $values[$i] = sprintf(
              $translator->translatePlural('%1$d day %2$dh%3$s', '%1$d days %2$dh%3$s', $day),
              $day,
              $hour,
              $minute
            );
          }
          else
          {
              $values[$i] = sprintf($translator->translatePlural('%d day', '%d days', $day), $day);
          }
        }
        elseif ($hour > 0 || $minute > 0)
        {
          if ($minute < 10)
          {
              $minute = '0' . $minute;
          }

          //TRANS: %1$d the number of hours, %2$s the number of minutes : display 3h15
          $values[$i] = sprintf($translator->translate('%1$dh%2$s'), $hour, $minute);
        }
      }
    }

    $tab = [];
    foreach (array_keys($values) as $key)
    {
      $tab[$key]['title'] = $values[$key];
    }
    return $tab;
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Task template', 'Task templates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
