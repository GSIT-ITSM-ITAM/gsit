<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class NotificationtemplatetranslationsMigration extends AbstractMigration
{
  public function change()
  {
    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);
    if (in_array('old', $environments))
    {
      // Migration of database

      $config = Config::fromPhp('phinx.php');
      $environment = new Environment('old', $config->getEnvironment('old'));
      $pdo = $environment->getAdapter()->getConnection();
    } else {
      return;
    }
    $item = $this->table('notificationtemplatetranslations');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_notificationtemplatetranslations');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                      => $row['id'],
            'notificationtemplate_id' => $row['notificationtemplates_id'],
            'language'                => $row['language'],
            'subject'                 => Toolbox::convertHtmlToMarkdown($this->convertOldTags($row['subject'])),
            'content_text'            => Toolbox::convertHtmlToMarkdown($this->convertOldTags($row['content_text'])),
            'content_html'            => Toolbox::convertHtmlToMarkdown($this->convertOldTags($row['content_html'])),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('notificationtemplatetranslations_id_seq', (SELECT MAX(id) FROM " .
          "notificationtemplatetranslations)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  private function convertOldTags($text)
  {

    $pattern = '/##IF([\w.]+)=([A-Za-zÀ-ÖØ-öø-ÿ0-9 ]+)##/i';
    $replacement = '{% if ${1} == "${2}" %}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##IF([\w.]+)(<|>|<=|>=|!=)([A-Za-zÀ-ÖØ-öø-ÿ0-9 ]+)##/i';
    $replacement = '{% if ${1} ${2} "${3}" %}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##IF([\w.]+)##/i';
    $replacement = '{% if ${1} is defined %}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##ENDIF(\w+)##/i';
    $replacement = '{% endif %}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##ENDIF(\w+).(\w+)##/i';
    $replacement = '{% endif %}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##ENDIF(\w+).(\w+).(\w+)##/i';
    $replacement = '{% endif %}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##FOREACHfollowups##/i';
    $replacement = '{% for followup in ticket.followups %}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##FOREACH(\w+)##/i';
    $replacement = '{% for ${1} in ${1} %}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##ENDFOREACH(\w+)##/i';
    $replacement = '{% endfor %}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##(\w+).(\w+)##/i';
    $replacement = '{{ ${1}.${2} }}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##(\w+).(\w+).(\w+)##/i';
    $replacement = '{{ ${1}.${2}.${3} }}';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern = '/##(\w+).(\w+).(\w+).(\w+)##/i';
    $replacement = '{{ ${1}.${2}.${3}.${4} }}';
    $text = preg_replace($pattern, $replacement, $text);

    $text = $this->convertFieldsForTicket($text);

    return $text;
  }

  private function convertFieldsForTicket($text)
  {

    // FOREACHauthors ?????
    // FOREACHchanges
    // FOREACHcosts
    // FOREACHdocuments
    // FOREACHfollowups
    $text = str_replace('{% for followups in', '{% for followup in', $text);
    // FOREACHitems
    // FOREACHlinkedtickets
    // FOREACHlog
    // FOREACHproblems
    $text = str_replace('{% for problems in', '{% for problem in', $text);
    // FOREACHsuppliers
    // FOREACHtasks
    $text = str_replace('{% for tasks in tasks %}', '{% for followup in followups %}', $text);
    // FOREACHtickets
    // FOREACHvalidations
    // author.category
    // author.email
    // author.id
    // author.location
    // author.mobile
    // author.name
    // author.phone
    // author.phone2
    // author.title
    // change.content
    // change.date
    // change.id
    // change.title
    // change.url
    // cost.budget
    // cost.comment
    // cost.costfixed
    // cost.costmaterial
    // cost.costtime
    // cost.datebegin
    // cost.dateend
    // cost.name
    // cost.time
    // cost.totalcost
    // document.downloadurl
    // document.filename
    // document.heading
    // document.id
    // document.name
    // document.url
    // document.weblink
    // followup.author
    // followup.date
    // followup.description
    $text = str_replace('{{ followup.description }}', '{{ followup.content }}', $text);
    // followup.isprivate
    // followup.requesttype
    // lang.author.category
    // lang.author.email
    // lang.author.id
    // lang.author.location
    // lang.author.mobile
    // lang.author.name
    // lang.author.phone
    // lang.author.phone2
    // lang.author.title
    // lang.cost.budget
    // lang.cost.comment
    // lang.cost.costfixed
    // lang.cost.costmaterial
    // lang.cost.costtime
    // lang.cost.datebegin
    // lang.cost.dateend
    // lang.cost.name
    // lang.cost.time
    // lang.cost.totalcost
    // lang.followup.author
    // lang.followup.date
    // lang.followup.description
    // lang.followup.isprivate
    // lang.followup.requesttype
    // lang.satisfaction.dateanswered
    // lang.satisfaction.datebegin
    // lang.satisfaction.description
    // lang.satisfaction.satisfaction
    // lang.satisfaction.text
    // lang.supplier.address
    // lang.supplier.comments
    // lang.supplier.country
    // lang.supplier.email
    // lang.supplier.fax
    // lang.supplier.id
    // lang.supplier.name
    // lang.supplier.phone
    // lang.supplier.postcode
    // lang.supplier.state
    // lang.supplier.town
    // lang.supplier.type
    // lang.supplier.website
    // lang.task.author
    // lang.task.begin
    // lang.task.category
    // lang.task.categorycomment
    // lang.task.categoryid
    // lang.task.date
    // lang.task.description
    // lang.task.end
    // lang.task.group
    // lang.task.isprivate
    // lang.task.status
    // lang.task.time
    // lang.task.user
    // lang.ticket.action
    // lang.ticket.assigntogroups
    // lang.ticket.assigntosupplier
    // lang.ticket.assigntousers
    // lang.ticket.attribution
    // lang.ticket.authors
    // lang.ticket.autoclose
    // lang.ticket.autoclosewarning
    // lang.ticket.category
    $text = str_replace('{{ lang.ticket.category }}', '{{ lang.category.name }}', $text);
    // lang.ticket.changes
    // lang.ticket.closedate
    // lang.ticket.content
    $text = str_replace('{{ lang.ticket.content }}', '{{ lang.content }}', $text);
    // lang.ticket.costfixed
    // lang.ticket.costmaterial
    // lang.ticket.costs
    // lang.ticket.costtime
    // lang.ticket.creationdate
    $text = str_replace('{{ lang.ticket.creationdate}}', '{{ lang.date }}', $text);
    // lang.ticket.days
    // lang.ticket.description
    $text = str_replace('{{ lang.ticket.description }}', '{{ lang.content }}', $text);
    // lang.ticket.duedate
    // lang.ticket.entity
    $text = str_replace('{{ lang.ticket.entity }}', '{{ lang.entity.name }}', $text);
    // lang.ticket.entity.address
    $text = str_replace('{{ lang.ticket.entity.address }}', '{{ lang.entity.address }}', $text);
    // lang.ticket.entity.country
    $text = str_replace('{{ lang.ticket.entity.country }}', '{{ lang.entity.country }}', $text);
    // lang.ticket.entity.email
    $text = str_replace('{{ lang.ticket.entity.email }}', '{{ lang.entity.email }}', $text);
    // lang.ticket.entity.fax
    $text = str_replace('{{ lang.ticket.entity.fax }}', '{{ lang.entity.fax }}', $text);
    // lang.ticket.entity.phone
    $text = str_replace('{{ lang.ticket.entity.phone }}', '{{ lang.ticket.entity.phonenumber }}', $text);
    // lang.ticket.entity.postcode
    $text = str_replace('{{ lang.ticket.entity.postcode }}', '{{ lang.entity.postcode }}', $text);
    // lang.ticket.entity.state
    $text = str_replace('{{ lang.ticket.entity.state }}', '{{ lang.entity.state }}', $text);
    // lang.ticket.entity.town
    $text = str_replace('{{ lang.ticket.entity.town }}', '{{ lang.entity.town }}', $text);
    // lang.ticket.entity.website
    $text = str_replace('{{ lang.ticket.entity.website }}', '{{ lang.entity.website }}', $text);
    // lang.ticket.globalvalidation
    // lang.ticket.groups
    // lang.ticket.id
    $text = str_replace('{{ lang.ticket.id }}', '{{ lang.id }}', $text);
    // lang.ticket.impact
    $text = str_replace('{{ lang.ticket.impact }}', '{{ lang.impact }}', $text);
    // lang.ticket.isdeleted
    // lang.ticket.item.contact
    // lang.ticket.item.contactnumber
    // lang.ticket.item.group
    // lang.ticket.item.location
    // lang.ticket.item.locationaltitude
    // lang.ticket.item.locationbuilding
    // lang.ticket.item.locationcomment
    // lang.ticket.item.locationlatitude
    // lang.ticket.item.locationlongitude
    // lang.ticket.item.locationroom
    // lang.ticket.item.model
    // lang.ticket.item.name
    // lang.ticket.item.otherserial
    // lang.ticket.item.serial
    // lang.ticket.item.user
    // lang.ticket.itemtype
    // lang.ticket.lastupdater
    $text = str_replace('{{ lang.ticket.lastupdater }}', '{{ lang.usersidlastupdater.completename }}', $text);
    // lang.ticket.linkedtickets
    // lang.ticket.location
    $text = str_replace('{{ lang.ticket.location }}', '{{ lang.location.name }}', $text);
    // lang.ticket.location.altitude
    // lang.ticket.location.building
    // lang.ticket.location.comment
    // lang.ticket.location.latitude
    // lang.ticket.location.longitude
    // lang.ticket.location.room
    // lang.ticket.log
    // lang.ticket.nocategoryassigned
    // lang.ticket.numberofchanges
    // lang.ticket.numberofcosts
    // lang.ticket.numberofdocuments
    // lang.ticket.numberoffollowups
    // lang.ticket.numberofitems
    // lang.ticket.numberoflinkedtickets
    // lang.ticket.numberofproblems
    // lang.ticket.numberoftasks
    // lang.ticket.numberofunresolved
    // lang.ticket.observergroups
    // lang.ticket.observerusers
    // lang.ticket.ola_tto::Complete name::
    // lang.ticket.ola_ttr
    // lang.ticket.openbyuser
    // lang.ticket.priority
    $text = str_replace('{{ lang.ticket.priority }}', '{{ lang.priority }}', $text);
    // lang.ticket.problems
    // lang.ticket.requesttype
    // lang.ticket.sla
    // lang.ticket.sla_tto
    // lang.ticket.sla_ttr
    // lang.ticket.solution.approval.author
    // lang.ticket.solution.approval.date
    // lang.ticket.solution.approval.description
    // lang.ticket.solution.description
    // lang.ticket.solution.type
    // lang.ticket.solvedate
    // lang.ticket.status
    // lang.ticket.suppliers
    // lang.ticket.tasks
    // lang.ticket.time
    // lang.ticket.title
    $text = str_replace('{{ lang.ticket.title }}', '{{ lang.ticket.name }}', $text);
    // lang.ticket.totalcost
    // lang.ticket.type
    // lang.ticket.urgency
    $text = str_replace('{{ lang.ticket.urgency }}', '{{ lang.urgency }}', $text);
    // lang.ticket.url
    // lang.validation.author
    // lang.validation.commentsubmission
    // lang.validation.commentvalidation
    // lang.validation.status
    // lang.validation.submissiondate
    // lang.validation.validationdate
    // lang.validation.validator
    // linkedticket.content
    // linkedticket.id
    // linkedticket.link
    // linkedticket.title
    // linkedticket.url
    // problem.content
    // problem.date
    // problem.id
    // problem.title
    $text = str_replace('{{ problem.title }}', '{{ problem.name }}', $text);
    // problem.url
    // satisfaction.dateanswered
    // satisfaction.datebegin
    // satisfaction.description
    // satisfaction.satisfaction
    // satisfaction.type
    // supplier.address
    // supplier.comments
    // supplier.country
    // supplier.email
    // supplier.fax
    // supplier.id
    // supplier.name
    // supplier.phone
    // supplier.postcode
    // supplier.state
    // supplier.town
    // supplier.type
    // supplier.website
    // task.author
    // task.begin
    // task.category
    // task.categorycomment
    // task.categoryid
    // task.date
    // task.description
    // task.end
    // task.group
    // task.isprivate
    // task.status
    // task.time
    // task.user
    // ticket.action
    // ticket.assigntogroups
    // ticket.assigntosupplier
    // ticket.assigntousers
    // ticket.authors
    // ticket.autoclose
    // ticket.category
    $text = str_replace('{{ ticket.category }}', '{{ category.name }}', $text);
    // ticket.closedate
    // ticket.content
    $text = str_replace('{{ ticket.content }}', '{{ content }}', $text);
    // ticket.costfixed
    // ticket.costmaterial
    // ticket.costtime
    // ticket.creationdate
    $text = str_replace('{{ ticket.creationdate}}', '{{ date }}', $text);
    // ticket.description
    $text = str_replace('{{ ticket.description }}', '{{ content }}', $text);
    // ticket.duedate
    // ticket.entity => entity.completename???
    // ticket.entity.address
    $text = str_replace('{{ ticket.entity.address }}', '{{ entity.address }}', $text);
    // ticket.entity.country
    $text = str_replace('{{ ticket.entity.country }}', '{{ entity.country }}', $text);
    // ticket.entity.email
    $text = str_replace('{{ ticket.entity.email }}', '{{ entity.email }}', $text);
    // ticket.entity.fax
    $text = str_replace('{{ ticket.entity.fax }}', '{{ entity.fax }}', $text);
    // ticket.entity.phone
    $text = str_replace('{{ ticket.entity.phone }}', '{{ entity.phonenumber }}', $text);
    // ticket.entity.postcode
    $text = str_replace('{{ ticket.entity.postcode }}', '{{ entity.postcode }}', $text);
    // ticket.entity.state
    $text = str_replace('{{ ticket.entity.state }}', '{{ entity.state }}', $text);
    // ticket.entity.town
    $text = str_replace('{{ ticket.entity.town }}', '{{ entity.town }}', $text);
    // ticket.entity.website
    $text = str_replace('{{ ticket.entity.website }}', '{{ entity.website }}', $text);
    // ticket.globalvalidation
    // ticket.groups
    // ticket.id
    $text = str_replace('{{ ticket.id }}', '{{ id }}', $text);
    // ticket.impact
    $text = str_replace('{{ ticket.impact }}', '{{ impact }}', $text);
    // ticket.isdeleted
    // ticket.item.contact
    // ticket.item.contactnumber
    // ticket.item.group
    // ticket.item.location
    // ticket.item.locationaltitude
    // ticket.item.locationbuilding
    // ticket.item.locationcomment
    // ticket.item.locationlatitude
    // ticket.item.locationlongitude
    // ticket.item.locationroom
    // ticket.item.model
    // ticket.item.name
    // ticket.item.otherserial
    // ticket.item.serial
    // ticket.item.user
    // ticket.itemtype
    // ticket.lastupdater
    $text = str_replace('{{ ticket.lastupdater }}', '{{ usersidlastupdater.completename }}', $text);
    // ticket.location
    $text = str_replace('{{ ticket.location }}', '{{ location.name }}', $text);
    // ticket.location.altitude
    // ticket.location.building
    // ticket.location.comment
    // ticket.location.latitude
    // ticket.location.longitude
    // ticket.location.room
    // ticket.log.content
    // ticket.log.date
    // ticket.log.field
    // ticket.log.user
    // ticket.numberofchanges
    // ticket.numberofcosts
    // ticket.numberofdocuments
    // ticket.numberoffollowups
    $text = str_replace('{{ ticket.numberoffollowups }}', '{{ followups|length }}', $text);
    // ticket.numberofitems
    // ticket.numberoflinkedtickets
    // ticket.numberoflogs
    // ticket.numberofproblems
    $text = str_replace('{{ ticket.numberofproblems }}', '{{ problems|length }}', $text);
    // ticket.numberoftasks
    // ticket.numberofunresolved
    // ticket.observergroups
    // ticket.observerusers
    // ticket.ola_tto
    // ticket.ola_ttr
    // ticket.openbyuser
    $text = str_replace('{{ ticket.openbyuser }}', '{{ usersidrecipient.completename }}', $text);
    // ticket.priority
    $text = str_replace('{{ ticket.priority }}', '{{ priority }}', $text);
    // ticket.requesttype
    // ticket.shortentity
    $text = str_replace('{{ ticket.shortentity }}', '{{ entity.name }}', $text);
    // ticket.sla
    // ticket.sla_tto
    // ticket.sla_ttr
    // ticket.solution.approval.author
    // ticket.solution.approval.date
    // ticket.solution.approval.description
    // ticket.solution.description
    // ticket.solution.type
    // ticket.solvedate
    // ticket.status
    $text = str_replace('{{ ticket.status }}', '{{ status }}', $text);
    // ticket.storestatus
    // ticket.suppliers
    // ticket.time
    // ticket.title
    $text = str_replace('{{ ticket.title }}', '{{ name }}', $text);
    // ticket.totalcost
    // ticket.type
    // ticket.urgency
    $text = str_replace('{{ ticket.urgency }}', '{{ urgency }}', $text);
    // ticket.url
    // ticket.urlapprove
    // ticket.urldocument
    // ticket.urlsatisfaction
    // ticket.urlvalidation
    // validation.answer.title
    // validation.author
    // validation.commentsubmission
    // validation.commentvalidation
    // validation.status
    // validation.submission.title
    // validation.submissiondate
    // validation.validationdate
    // validation.validationstatus
    // validation.validator

    // From plugin notifications
    // ##FOREACHactivitymessages##
    $text = str_replace('{% for activitymessages in activitymessages %}', '{% for followup in followups %}', $text);
    // '##activitymessage.type##'        => 'followup',
    // '##activitymessage.isprivate##'   => Dropdown::getYesNo($followup['is_private']),
    // '##activitymessage.author##'      => Html::clean(getUserName($followup['users_id'])),
    // '##activitymessage.requesttype##' => Dropdown::getDropdownName('glpi_requesttypes',
    //                                      $followup['requesttypes_id']),
    // '##activitymessage.date##'        => Html::convDateTime($followup['date']),
    $text = str_replace('{{ activitymessage.date }}', '{{ followup.date }}', $text);
    // '##activitymessage.description##' => Html::clean($followup['content']),
    $text = str_replace('{{ activitymessage.description }}', '{{ followup.content }}', $text);
    // '##activitymessage.category##'    => '',
    // '##task.time##'                   => ''
    // ##ticket.numberofactivitymessages##
    $text = str_replace('{{ ticket.numberofactivitymessages }}', '{{ followups|length }}', $text);

    return $text;
  }
}
