<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class MailcollectorsMigration extends AbstractMigration
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
    $item = $this->table('mailcollectors');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_mailcollectors');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'name'                => $row['name'],
            'host'                => $row['host'],
            'login'               => $row['login'],
            'filesize_max'        => $row['filesize_max'],
            'is_active'           => $row['is_active'],
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'comment'             => $row['comment'],
            'passwd'              => $row['passwd'],
            'accepted'            => $row['accepted'],
            'refused'             => $row['refused'],
            'errors'              => $row['errors'],
            'use_mail_date'       => $row['use_mail_date'],
            'created_at'          => Toolbox::fixDate($row['date_creation']),
            'requester_field'     => $row['requester_field'],
            'add_cc_to_observer'  => $row['add_cc_to_observer'],
            'collect_only_unread' => $row['collect_only_unread'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('mailcollectors_id_seq', (SELECT MAX(id) FROM mailcollectors)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
