<?php

namespace App\v1\Controllers\Rules;

final class Criterium
{
  /**
   * Try to match a defined rule
   *
   * @param $criterion         RuleCriteria object
   * @param $field              the field to match
   * @param &$criterias_results
   * @param &$regex_result
   *
   * @return true if the field match the rule, false if it doesn't match
  **/
  public static function match($criterion, $field, &$criterias_results, &$regex_result)
  {

    $condition = $criterion->condition;
    $pattern   = $criterion->pattern;
    $criteria  = $criterion->criteria;

    //If pattern is wildcard, don't check the rule and return true
    //or if the condition is "already present in GLPI" : will be processed later
    if (
        ($pattern == \App\v1\Controllers\Rules\Common::RULE_WILDCARD) ||
        ($condition == \App\v1\Controllers\Rules\Common::PATTERN_FIND)
    )
    {
      return true;
    }

    $pattern = trim($pattern);

    switch ($condition)
    {
      case \App\v1\Controllers\Rules\Common::PATTERN_EXISTS:
          return (!empty($field));

      case \App\v1\Controllers\Rules\Common::PATTERN_DOES_NOT_EXISTS:
          return (empty($field));

      case \App\v1\Controllers\Rules\Common::PATTERN_IS:
        if (is_array($field))
        {
          // Special case (used only by UNIQUE_PROFILE, for now)
          // $pattern is an ID
          if (in_array($pattern, $field))
          {
            $criterias_results[$criteria] = $pattern;
            return true;
          }
        } else {
          //Perform comparison with fields in lower case
          $field                        = \App\v1\Controllers\Toolbox::strtolower($field);
          $pattern                      = \App\v1\Controllers\Toolbox::strtolower($pattern);
          if ($field == $pattern)
          {
            $criterias_results[$criteria] = $pattern;
            return true;
          }
        }
          return false;

      case \App\v1\Controllers\Rules\Common::PATTERN_IS_NOT:
        //Perform comparison with fields in lower case
        $field   = \App\v1\Controllers\Toolbox::strtolower($field);
        $pattern = \App\v1\Controllers\Toolbox::strtolower($pattern);
        if ($field != $pattern)
        {
          $criterias_results[$criteria] = $pattern;
          return true;
        }
          return false;

      case \App\v1\Controllers\Rules\Common::PATTERN_UNDER:
        // $table  = getTableNameForForeignKeyField($criteria);
        // $values = getSonsOf($table, $pattern);
        // if (isset($values[$field]))
        // {
        //   return true;
        // }
          return false;

      case \App\v1\Controllers\Rules\Common::PATTERN_NOT_UNDER:
        // $table  = getTableNameForForeignKeyField($criteria);
        // $values = getSonsOf($table, $pattern);
        // if (isset($values[$field]))
        // {
        //   return false;
        // }
          return true;

      case \App\v1\Controllers\Rules\Common::PATTERN_END:
        $value = "/" . $pattern . "$/i";
        if (preg_match($value, $field) > 0)
        {
          $criterias_results[$criteria] = $pattern;
          return true;
        }
          return false;

      case \App\v1\Controllers\Rules\Common::PATTERN_BEGIN:
        if (empty($pattern))
        {
          return false;
        }
        if (is_null($field))
        {
          return false;
        }
        $value = mb_stripos($field, $pattern, 0, 'UTF-8');
        if (($value !== false) && ($value == 0))
        {
          $criterias_results[$criteria] = $pattern;
          return true;
        }
          return false;

      case \App\v1\Controllers\Rules\Common::PATTERN_CONTAIN:
        if (empty($pattern))
        {
          return false;
        }
        if (is_null($field))
        {
          return false;
        }
        $value = mb_stripos($field, $pattern, 0, 'UTF-8');
        if (($value !== false) && ($value >= 0))
        {
          $criterias_results[$criteria] = $pattern;
          return true;
        }
          return false;

      case \App\v1\Controllers\Rules\Common::PATTERN_NOT_CONTAIN:
        if (empty($pattern))
        {
          return false;
        }
        $value = mb_stripos($field, $pattern, 0, 'UTF-8');
        if ($value === false)
        {
          $criterias_results[$criteria] = $pattern;
          return true;
        }
          return false;

      case \App\v1\Controllers\Rules\Common::REGEX_MATCH:
        $results = [];
        // Permit use < and >
        // $pattern = Toolbox::unclean_cross_side_scripting_deep($pattern);
        if (preg_match_all($pattern . "i", $field, $results) > 0)
        {
          // Drop $result[0] : complete match result
          array_shift($results);
          // And add to $regex_result array
          $res = [];
          foreach ($results as $data)
          {
            foreach ($data as $val)
            {
              $res[] = $val;
            }
          }
          $regex_result[]               = $res;
          $criterias_results[$criteria] = $pattern;
          return true;
        }
          return false;

      case \App\v1\Controllers\Rules\Common::REGEX_NOT_MATCH:
        // Permit use < and >
        // $pattern = Toolbox::unclean_cross_side_scripting_deep($pattern);
        if (preg_match($pattern . "i", $field) == 0)
        {
          $criterias_results[$criteria] = $pattern;
          return true;
        }
          return false;

      case \App\v1\Controllers\Rules\Common::PATTERN_FIND:
      case \App\v1\Controllers\Rules\Common::PATTERN_IS_EMPTY:
        // Global criteria will be evaluated later
          return true;
    }
    return false;
  }
}
