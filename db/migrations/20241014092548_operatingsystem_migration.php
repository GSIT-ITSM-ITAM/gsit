<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class OperatingsystemMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('item_operatingsystem');
    $table->addColumn('installationdate', 'timestamp', ['null' => true])
          ->addColumn('winowner', 'string', ['null' => true])
          ->addColumn('wincompany', 'string', ['null' => true])
          ->addColumn('oscomment', 'string', ['null' => true])
          ->addColumn('hostid', 'string', ['null' => true])
          ->update();

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
    $item = $this->table('item_operatingsystem');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query(
        'SELECT count(*) FROM glpi_plugin_fusioninventory_inventorycomputercomputers'
      )->fetchColumn();
      $nbLoops = ceil($nbRows / 5000);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query(
          'SELECT * FROM glpi_plugin_fusioninventory_inventorycomputercomputers ORDER BY id LIMIT 5000 OFFSET ' .
          ($i * 5000)
        );

        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $builder = $this->getQueryBuilder('update');
          $builder->update('item_operatingsystem')
                  ->set('installationdate', Toolbox::fixDate($row['operatingsystem_installationdate']))
                  ->set('winowner', $row['winowner'])
                  ->set('wincompany', $row['wincompany'])
                  ->set('oscomment', $row['oscomment'])
                  ->set('hostid', $row['hostid'])
                  ->where(['item_type' => 'App\\Models\\Computer', 'item_id' => $row['computers_id']])
                  ->execute();
        }
      }
    }
  }
}
