<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class DocumentsItemsMigration extends AbstractMigration
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

      $chunkSize = 15000;
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $chunkSize = 5900;
      }
    } else {
      return;
    }
    $item = $this->table('document_item');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_documents_items')->fetchColumn();
      $nbLoops = ceil($nbRows / $chunkSize);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_documents_items ORDER BY id LIMIT ' . $chunkSize . ' OFFSET ' .
          ($i * $chunkSize));

        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'id'                => $row['id'],
            'document_id'       => $row['documents_id'],
            'item_id'           => $row['items_id'],
            'item_type'         => 'App\\Models\\' . $row['itemtype'],
            'entity_id'         => ($row['entities_id'] + 1),
            'is_recursive'      => $row['is_recursive'],
            'updated_at'        => Toolbox::fixDate($row['date_mod']),
            'user_id'           => $row['users_id'],
            'timeline_position' => $row['timeline_position'],
            'created_at'        => Toolbox::fixDate($row['date_creation']),
            'date'              => Toolbox::fixDate($row['date']),
          ];
        }
        print_r($data[196]);
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('document_item_id_seq', (SELECT MAX(id) FROM document_item)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
