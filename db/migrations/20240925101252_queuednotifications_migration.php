<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class QueuednotificationsMigration extends AbstractMigration
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
    $item = $this->table('queuednotifications');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_queuednotifications');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                      => $row['id'],
            'item_type'               => 'App\\Models\\' . $row['itemtype'],
            'item_id'                 => $row['items_id'],
            'notificationtemplate_id' => $row['notificationtemplates_id'],
            'entity_id'               => ($row['entities_id'] + 1),
            'sent_try'                => $row['sent_try'],
            'created_at'              => $row['create_time'],
            'send_time'               => Toolbox::fixDate($row['send_time']),
            'sent_time'               => Toolbox::fixDate($row['sent_time']),
            'name'                    => $row['name'],
            'sender'                  => $row['sender'],
            'sendername'              => $row['sendername'],
            'recipient'               => $row['recipient'],
            'recipientname'           => $row['recipientname'],
            'replyto'                 => $row['replyto'],
            'replytoname'             => $row['replytoname'],
            'headers'                 => $row['headers'],
            'body_html'               => $row['body_html'],
            'body_text'               => $row['body_text'],
            'messageid'               => $row['messageid'],
            'documents'               => $row['documents'],
            'mode'                    => $row['mode'],
            'deleted_at'              => self::convertIsDeleted($row['is_deleted']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  public function convertIsDeleted($is_deleted)
  {
    if ($is_deleted == 1)
    {
      return date('Y-m-d H:i:s', time());
    }

    return null;
  }
}
