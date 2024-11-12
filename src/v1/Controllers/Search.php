<?php

namespace App\v1\Controllers;

use Illuminate\Database\Eloquent\Builder;

final class Search extends Common
{
  public function getData($item, $uri, $page = 1, $filters = [])
  {
    $itemtype = get_class($item);
    $prefs = \App\Models\Displaypreference::getForTypeUser($itemtype, $GLOBALS['user_id']);
    $itemDef = $item->getDefinitions();
    $where = $this->manageFilters($itemDef, $filters);

    $profileright = \App\Models\Profileright::
        where('profile_id', $GLOBALS['profile_id'])
      ->where('model', $itemtype)
      ->first();
    if (is_null($profileright))
    {
      return;
    }

    $hasEntity = $item->isEntity();

    // columns
    $newItemDef = [];
    foreach ($itemDef as $field)
    {
      if (!isset($field['display']) || !$field['display'])
      {
        continue;
      }
      if (in_array($field['id'], $prefs))
      {
        $newItemDef[] = $field;
      }
      // add name field if not in preferences
      if ($field['name'] == 'name' && !in_array($field['id'], $prefs))
      {
        $newItemDef[] = $field;
      }
      // Same for entity
      if ($GLOBALS['entity_recursive'] && $field['name'] == 'entity' && !in_array($field['id'], $prefs))
      {
        $newItemDef[] = $field;
      }
    }

    $start = 0;
    $limit = 15;
    $start = ($page - 1) * $limit;

    // Apply filters
    foreach ($where as $key => $value)
    {
      if (is_array($value))
      {
        $item = $item->where($key, $value[0], $value[1]);
      } else {
        $item = $item->where($key, $value);
      }
    }

    if ($hasEntity)
    {
      $item = $item->with('entity')->whereHas('entity', function (Builder $query)
      {
        if ($GLOBALS['entity_recursive'])
        {
          $query->where('treepath', 'LIKE', $GLOBALS['entity_treepath'] . '%');
        } else {
          $query->where('treepath', $GLOBALS['entity_treepath']);
        }
      });
    }

    if ($itemtype == "App\Models\Ticket")
    {
      if ($profileright->readmyitems && !$profileright->read)
      {
        $item = $item->where('user_id_recipient', $GLOBALS['user_id']);
      }
      $item = $item->orderBy('id', 'desc');
    }

    $cnt = $item->count();

    $items = $item->offset($start)->take($limit)->get();
    $itemDbData = $this->prepareValues($newItemDef, $items, $uri);

    $itemDbData['paging'] = [
      'total'   => $cnt,
      'pages'   => ceil($cnt / $limit),
      'current' => $page,
      'linkpage' => $uri . '?page=',
    ];
    return $itemDbData;
  }

  private function prepareValues($itemDef, $data, $uri)
  {
    $header = ['id'];
    foreach ($itemDef as $field)
    {
      $header[] = $field['title'];
    }

    $allData = [];
    foreach ($data as $item)
    {
      $myData = [];
      $myData['id'] = [
        'value' => $item->id,
        'link'  => $uri . '/' . $item['id'],
      ];

      foreach ($itemDef as $field)
      {
        if ($field['type'] == 'dropdown_remote')
        {
          if (is_null($item->{$field['name']}) || empty($item->{$field['name']}))
          {
            $myData[$field['name']] = [
              'value' => '',
            ];
          } else {
            if (isset($field['count']))
            {
              $elements = [];
              foreach ($item->{$field['name']} as $t)
              {
                $elements[] = $t->{$field['count']};
              }
              $myData[$field['name']] = [
                'value' => array_sum($elements),
              ];
            }
            elseif (isset($field['multiple']))
            {
              $elements = [];
              foreach ($item->{$field['name']} as $t)
              {
                $elements[] = $t->name;
              }
              $myData[$field['name']] = [
                'value' => implode(', ', $elements),
              ];
            } else {
              $myData[$field['name']] = [
                'value' => $item->{$field['name']}->name,
              ];
            }
          }
        }
        elseif ($field['type'] == 'dropdown')
        {
          if (isset($field['values'][$item->{$field['name']}]))
          {
            $myData[$field['name']] = [
              'value' => $field['values'][$item->{$field['name']}],
            ];
          } else {
            $myData[$field['name']] = [];
          }
        }
        elseif ($field['type'] == 'description')
        {
          $myData[$field['name']] = [
            'value' => $field['values'][$item->{$field['name']}],
          ];
        }
        else
        {
          $myData[$field['name']] = [
            'value' => $item->{$field['name']},
          ];
        }
      }
      $allData[] = $myData;
    }

    return [
      'header' => $header,
      'data'   => $allData,
      'allFields' => $itemDef,
    ];
  }

  private function manageFilters($itemDef, $params)
  {
    if (isset($params['field']) && is_numeric($params['field']))
    {
      foreach ($itemDef as $field)
      {
        if ($field['id'] == $params['field'])
        {
          if ($field['type'] == 'dropdown_remote')
          {
            return [$field['name'] => $params['value']];
          } else {
            return [$field['name'] => ['LIKE', '%' . $params['value'] . '%']];
          }
        }
      }
    }
    return [];
  }
}
