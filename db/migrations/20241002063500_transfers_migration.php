<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class TransfersMigration extends AbstractMigration
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
    $item = $this->table('transfers');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_transfers');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'name'                => $row['name'],
            'keep_ticket'         => $row['keep_ticket'],
            'keep_networklink'    => $row['keep_networklink'],
            'keep_reservation'    => $row['keep_reservation'],
            'keep_history'        => $row['keep_history'],
            'keep_device'         => $row['keep_device'],
            'keep_infocom'        => $row['keep_infocom'],
            'keep_dc_monitor'     => $row['keep_dc_monitor'],
            'clean_dc_monitor'    => $row['clean_dc_monitor'],
            'keep_dc_phone'       => $row['keep_dc_phone'],
            'clean_dc_phone'      => $row['clean_dc_phone'],
            'keep_dc_peripheral'  => $row['keep_dc_peripheral'],
            'clean_dc_peripheral' => $row['clean_dc_peripheral'],
            'keep_dc_printer'     => $row['keep_dc_printer'],
            'clean_dc_printer'    => $row['clean_dc_printer'],
            'keep_supplier'       => $row['keep_supplier'],
            'clean_supplier'      => $row['clean_supplier'],
            'keep_contact'        => $row['keep_contact'],
            'clean_contact'       => $row['clean_contact'],
            'keep_contract'       => $row['keep_contract'],
            'clean_contract'      => $row['clean_contract'],
            'keep_software'       => $row['keep_software'],
            'clean_software'      => $row['clean_software'],
            'keep_document'       => $row['keep_document'],
            'clean_document'      => $row['clean_document'],
            'keep_cartridgeitem'  => $row['keep_cartridgeitem'],
            'clean_cartridgeitem' => $row['clean_cartridgeitem'],
            'keep_cartridge'      => $row['keep_cartridge'],
            'keep_consumable'     => $row['keep_consumable'],
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'comment'             => $row['comment'],
            'keep_disk'           => $row['keep_disk'],
            'created_at'          => Toolbox::fixDate($row['date_mod']),
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
