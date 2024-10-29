<?php

namespace App\v1\Controllers\Rules;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use Slim\Routing\RouteContext;

class Common extends \App\v1\Controllers\Common
{
  protected $stop_on_first_match = false;

  ///Actions affected to this rule
  public $actionsDefinition     = [];
  ///Criterias affected to this rule
  public $criteriaDefinition    = [];

  public $regex_results = [];
  public $criterias_results = [];

  protected $criteriaDefinitionModel = null;
  protected $actionsDefinitionModel = null;

  public const RULE_WILDCARD           = '*';

  //Generic rules engine
  public const PATTERN_IS              = 0;
  public const PATTERN_IS_NOT          = 1;
  public const PATTERN_CONTAIN         = 2;
  public const PATTERN_NOT_CONTAIN     = 3;
  public const PATTERN_BEGIN           = 4;
  public const PATTERN_END             = 5;
  public const REGEX_MATCH             = 6;
  public const REGEX_NOT_MATCH         = 7;
  public const PATTERN_EXISTS          = 8;
  public const PATTERN_DOES_NOT_EXISTS = 9;
  public const PATTERN_FIND            = 10; // Global criteria
  public const PATTERN_UNDER           = 11;
  public const PATTERN_NOT_UNDER       = 12;
  public const PATTERN_IS_EMPTY        = 30; // Global criteria

  public const AND_MATCHING            = 'AND';
  public const OR_MATCHING             = 'OR';


  // Processing several rules : use result of the previous one to the current one
  protected $use_output_rule_process_as_next_input = false;

  public function processAllRules($input = [], $output = [], $params = [], $options = [])
  {
    $p['condition']     = 0;
    $p['only_criteria'] = null;

    if (is_array($options) && count($options))
    {
      foreach ($options as $key => $val)
      {
        $p[$key] = $val;
      }
    }

    // $input                      = $this->prepareInputDataForProcessWithPlugins($input, $params);
    $output["_no_rule_matches"] = true;
    //Store rule type being processed (for plugins)
    $params['rule_itemtype']    = get_class($this);

    $model = str_replace('App\v1\Controllers', '\App\Models', $params['rule_itemtype']);
    // Get rules
    $rules = $model::get();

    if (count($rules))
    {
      foreach ($rules as $rule)
      {
        //If the rule is active, process it

        if ($rule->is_active)
        {
          // Load criteria and actions
          // $this->actions = \App\Models\Rules\Action::where('rule_id', $rule->id)->get();

          $output["_rule_process"] = false;
          $this->process($rule, $input, $output, $params, $p);

          if ($output["_rule_process"] && $this->stop_on_first_match)
          {
            unset($output["_rule_process"]);
            $output["_ruleid"] = $rule->id;
            return $output;
            // return Toolbox::addslashes_deep($output);
          }
        }

        if ($this->use_output_rule_process_as_next_input)
        {
          // $output = $this->prepareInputDataForProcessWithPlugins($output, $params);
          $input  = $output;
        }
      }
    }
    // return Toolbox::addslashes_deep($output);
    // echo "FINISH";
    // print_r($output);
    return $output;
  }

  public function process($rule, &$input, &$output, &$params, &$options = [])
  {
    // if ($this->validateCriterias($options))
    // {
    $this->regex_results     = [];
    $this->criterias_results = [];
    // $input = $this->prepareInputDataForProcess($input, $params);

    if ($this->checkCriterias($rule, $input))
    {
      unset($output["_no_rule_matches"]);
      $refoutput = $output;
      $output    = $this->executeActions($rule, $output, $params, $input);

      // $this->updateOnlyCriteria($options, $refoutput, $output);
      //Hook
      // $hook_params["sub_type"] = $this->getType();
      // $hook_params["ruleid"]   = $rule->id;
      // $hook_params["input"]    = $input;
      // $hook_params["output"]   = $output;
      // Plugin::doHook("rule_matched", $hook_params);
      $output["_rule_process"] = true;
    }
    // }
  }

  public function executeActions($rule, $output, $params, array $input = [])
  {

    // Load actions from DB
    $actions = \App\Models\Rules\Ruleaction::where('rule_id', $rule->id)->get();
    // Load actions definitions
    $this->actionsDefinition = $this->actionsDefinitionModel::get();

    if (count($actions))
    {
      foreach ($actions as $action)
      {
        switch ($action->action_type)
        {
          case "assign":
            $output[$action->field] = $action->value;
              break;

          case "append":
            // print_r($action);



            $value = $action->value;
            if (
                isset($this->actionsDefinition[$action->field]["appendtoarray"]) &&
                isset($this->actionsDefinition[$action->field]["appendtoarrayfield"])
            )
            {
              $value = $this->actionsDefinition[$action->field]["appendtoarray"];
              $value[$this->actionsDefinition[$action->field]["appendtoarrayfield"]] = $action->value;
            }
            $output[$this->actionsDefinition[$action->field]["appendto"]][] = $value;
              break;

          case "regex_result":
          case "append_regex_result":
            //Regex result : assign value from the regex
            //Append regex result : append result from a regex
            if (isset($this->regex_results[0]))
            {
              // $res = RuleAction::getRegexResultById($action->value, $this->regex_results[0]);
            } else {
              $res = $action->value;
            }

            if ($action->action_type == "append_regex_result")
            {
              if (isset($params[$action->field]))
              {
                $res = $params[$action->field] . $res;
              } else {
                //keep rule value to append in a separate entry
                $output[$action->field . '_append'] = $res;
              }
            }

            $output[$action->field] = $res;
              break;

          default:
            //plugins actions
            // $executeaction = clone $this;
            // $output = $executeaction->executePluginsActions($action, $output, $params, $input);
              break;
        }
      }
    }
    return $output;
  }

  /**
   * Check criteria
   *
   * @param aray $input the input data used to check criteri
   *
   * @return boolean if criteria match
  **/
  public function checkCriterias($rule, $input)
  {
    // Load criteria from DB
    $criteria = \App\Models\Rules\Rulecriterium::where('rule_id', $rule->id)->get();
    // Load criteria definitions
    $this->criteriaDefinition = $this->criteriaDefinitionModel::get();

    if ($rule->match == self::AND_MATCHING)
    {
      $doactions = true;

      foreach ($criteria as $criterium)
      {
        // $definition_criterion = $this->getCriteria($criterion->fields['criteria']);
        // if (!isset($definition_criterion['is_global']) || !$definition_criterion['is_global'])
        // {
        $doactions &= $this->checkCriteria($criterium, $input);
        if (!$doactions)
        {
          break;
        }
        // }
      }
    } else { // OR MATCHING
      $doactions = false;
      foreach ($criteria as $criterion)
      {
        // $definition_criterion = $this->getCriteria($criterion->fields['criteria']);

        // if (
        //     !isset($definition_criterion['is_global']) ||
        //     !$definition_criterion['is_global']
        // )
        // {
        $doactions |= $this->checkCriteria($criterion, $input);
        if ($doactions)
        {
          break;
        }
        // }
      }
    }

    //If all simple criteria match, and if necessary, check complex criteria
    if ($doactions)
    {
      return $this->findWithGlobalCriteria($input);
    }
    return false;
  }

  /**
   * Process a criteria of a rule
   *
   * @param $criterium  criterium to check
   * @param &$input     the input data used to check criteria
  **/
  public function checkCriteria($criterion, &$input)
  {
    $partial_regex_result = [];
    // Undefine criteria field : set to blank
    if (!isset($input[$criterion->criteria]))
    {
      $input[$criterion->criteria] = '';
    }

    // If the value is not an array
    if (!is_array($input[$criterion->criteria]))
    {
      $value = $this->getCriteriaValue(
        $criterion->criteria,
        $criterion->condition,
        $input[$criterion->criteria],
      );

      $res = \App\v1\Controllers\Rules\Criterium::match(
        $criterion,
        $value,
        $this->criterias_results,
        $partial_regex_result
      );
    } else {
      //If the value is, in fact, an array of values
      // Negative condition : Need to match all condition (never be)
      if (
          in_array(
            $criterion->condition,
            [
              self::PATTERN_IS_NOT,
              self::PATTERN_NOT_CONTAIN,
              self::REGEX_NOT_MATCH,
              self::PATTERN_DOES_NOT_EXISTS
            ]
          )
      )
      {
        $res = true;
        foreach ($input[$criterion->criteria] as $tmp)
        {
          $value = $this->getCriteriaValue($criterion->criteria, $criterion->condition, $tmp);

          // $res &= \App\v1\Controllers\Rules\Criterium::match(
          //   $criteria,
          //   $value,
          //   $this->criterias_results,
          //   $partial_regex_result
          // );
          if (!$res)
          {
            break;
          }
        }
      } else {
        // Positive condition : Need to match one
        $res = false;
        foreach ($input[$criterion->criteria] as $crit)
        {
          $value = $this->getCriteriaValue($criterion->criteria, $criterion->condition, $crit);

          // $res |= \App\v1\Controllers\Rules\Criterium::match(
          //   $criteria,
          //   $value,
          //   $this->criterias_results,
          //   $partial_regex_result
          // );
        }
      }
    }

    // Found regex on this criteria
    if (count($partial_regex_result))
    {
      // No regex existing : put found
      if (!count($this->regex_results))
      {
        $this->regex_results = $partial_regex_result;
      } else { // Already existing regex : append found values
        $temp_result = [];
        foreach ($partial_regex_result as $new)
        {
          foreach ($this->regex_results as $old)
          {
            $temp_result[] = array_merge($old, $new);
          }
        }
        $this->regex_results = $temp_result;
      }
    }
    return $res;
  }

  /**
   * Return a value associated with a pattern associated to a criteria to display it
   *
   * @param $ID        the given criteria
   * @param $condition condition used
   * @param $value     the pattern
  **/
  public function getCriteriaValue($key, $condition, $value)
  {
    if (
        !in_array(
          $condition,
          [
            self::PATTERN_DOES_NOT_EXISTS, self::PATTERN_EXISTS,
            self::PATTERN_IS, self::PATTERN_IS_NOT,
            self::PATTERN_NOT_UNDER, self::PATTERN_UNDER
          ]
        )
    )
    {
      // if (!isset($this->criteriaDefinition[$key]))
      // {
      //   return $value;
      // }
      $crit = $this->criteriaDefinition[$key];
      if (isset($crit['type']))
      {
        switch ($crit['type'])
        {
          case "dropdown":
            $item = new $crit['model']();
            $myItem = $item->find($value);
            if (is_null($myItem))
            {
              return '';
            }
              return $myItem->name;

          case "dropdown_assign":
          case "dropdown_users":
              return $value;
              // return getUserName($value);

          case "yesonly":
          case "yesno":
              return $value;
              // return Dropdown::getYesNo($value);

          case "dropdown_impact":
            $impacts = \App\Models\Definitions\Ticket::getImpactArray();
              return $impacts[$value]['title'];

          case "dropdown_urgency":
            $urgencies = \App\Models\Definitions\Ticket::getUrgencyArray();
              return $urgencies[$value]['title'];

          case "dropdown_priority":
              $priorities = \App\Models\Definitions\Ticket::getPriorityArray();
              return $priorities[$value]['title'];
        }
      }
    }
    return $value;
  }

  /**
   * @param $input
  **/
  public function findWithGlobalCriteria($input)
  {
    return true;
  }
}
