<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class LocationsMigration extends AbstractMigration
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
    $item = $this->table('locations');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_locations');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'              => $row['id'],
            'entity_id'       => ($row['entities_id'] + 1),
            'is_recursive'    => $row['is_recursive'],
            'name'            => $row['name'],
            'location_id'     => $row['locations_id'],
            'completename'    => $row['completename'],
            'comment'         => $row['comment'],
            'level'           => $row['level'],
            'ancestors_cache' => $row['ancestors_cache'],
            'sons_cache'      => $row['sons_cache'],
            'address'         => $row['address'],
            'postcode'        => $row['postcode'],
            'town'            => $row['town'],
            'state'           => $row['state'],
            'country'         => $row['country'],
            'building'        => $row['building'],
            'room'            => $row['room'],
            'latitude'        => $row['latitude'],
            'longitude'       => $row['longitude'],
            'altitude'        => $row['altitude'],
            'updated_at'      => Toolbox::fixDate($row['date_mod']),
            'created_at'      => Toolbox::fixDate($row['date_creation']),
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
}
