<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class StatesMigration extends AbstractMigration
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
    $item = $this->table('states');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_states');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                            => $row['id'],
            'name'                          => $row['name'],
            'entity_id'                     => ($row['entities_id'] + 1),
            'is_recursive'                  => $row['is_recursive'],
            'comment'                       => $row['comment'],
            'state_id'                      => $row['states_id'],
            'completename'                  => $row['completename'],
            'level'                         => $row['level'],
            'ancestors_cache'               => $row['ancestors_cache'],
            'sons_cache'                    => $row['sons_cache'],
            'is_visible_computer'           => $row['is_visible_computer'],
            'is_visible_monitor'            => $row['is_visible_monitor'],
            'is_visible_networkequipment'   => $row['is_visible_networkequipment'],
            'is_visible_peripheral'         => $row['is_visible_peripheral'],
            'is_visible_phone'              => $row['is_visible_phone'],
            'is_visible_printer'            => $row['is_visible_printer'],
            'is_visible_softwareversion'    => $row['is_visible_softwareversion'],
            'is_visible_softwarelicense'    => $row['is_visible_softwarelicense'],
            'is_visible_line'               => $row['is_visible_line'],
            'is_visible_certificate'        => $row['is_visible_certificate'],
            'is_visible_rack'               => $row['is_visible_rack'],
            'is_visible_passivedcequipment' => $row['is_visible_passivedcequipment'],
            'is_visible_enclosure'          => $row['is_visible_enclosure'],
            'is_visible_pdu'                => $row['is_visible_pdu'],
            'is_visible_cluster'            => $row['is_visible_cluster'],
            'is_visible_contract'           => $row['is_visible_contract'],
            'is_visible_appliance'          => $row['is_visible_appliance'],
            'updated_at'                    => Toolbox::fixDate($row['date_mod']),
            'created_at'                    => Toolbox::fixDate($row['date_creation']),
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
