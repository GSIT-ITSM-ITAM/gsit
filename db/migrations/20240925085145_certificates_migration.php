<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class CertificatesMigration extends AbstractMigration
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
    $item = $this->table('certificates');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_certificates');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'name'                => $row['name'],
            'serial'              => $row['serial'],
            'otherserial'         => $row['otherserial'],
            'entity_id'           => ($row['entities_id'] + 1),
            'is_recursive'        => $row['is_recursive'],
            'comment'             => $row['comment'],
            'is_template'         => $row['is_template'],
            'template_name'       => $row['template_name'],
            'certificatetype_id'  => $row['certificatetypes_id'],
            'dns_name'            => $row['dns_name'],
            'dns_suffix'          => $row['dns_suffix'],
            'user_id_tech'        => $row['users_id_tech'],
            'group_id_tech'       => $row['groups_id_tech'],
            'location_id'         => $row['locations_id'],
            'manufacturer_id'     => $row['manufacturers_id'],
            'contact'             => $row['contact'],
            'contact_num'         => $row['contact_num'],
            'user_id'             => $row['users_id'],
            'group_id'            => $row['groups_id'],
            'is_autosign'         => $row['is_autosign'],
            'date_expiration'     => Toolbox::fixDate($row['date_expiration']),
            'state_id'            => $row['states_id'],
            'command'             => $row['command'],
            'certificate_request' => $row['certificate_request'],
            'certificate_item'    => $row['certificate_item'],
            'created_at'          => Toolbox::fixDate($row['date_creation']),
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'deleted_at'          => self::convertIsDeleted($row['is_deleted']),
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
