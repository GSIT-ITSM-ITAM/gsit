<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ContactsMigration extends AbstractMigration
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
    $item = $this->table('contacts');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_contacts');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'              => $row['id'],
            'entity_id'       => ($row['entities_id'] + 1),
            'is_recursive'    => $row['is_recursive'],
            'name'            => $row['name'],
            'firstname'       => $row['firstname'],
            'phone'           => $row['phone'],
            'phone2'          => $row['phone2'],
            'mobile'          => $row['mobile'],
            'fax'             => $row['fax'],
            'email'           => $row['email'],
            'contacttype_id'  => $row['contacttypes_id'],
            'comment'         => $row['comment'],
            'usertitle_id'    => $row['usertitles_id'],
            'address'         => $row['address'],
            'postcode'        => $row['postcode'],
            'town'            => $row['town'],
            'state'           => $row['state'],
            'country'         => $row['country'],
            'updated_at'      => Toolbox::fixDate($row['date_mod']),
            'created_at'      => Toolbox::fixDate($row['date_creation']),
            'deleted_at'      => self::convertIsDeleted($row['is_deleted']),
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
