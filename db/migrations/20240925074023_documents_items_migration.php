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
    } else {
      return;
    }
    $item = $this->table('document_item');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_documents_items')->fetchColumn();
      $nbLoops = ceil($nbRows / 5900);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_documents_items ORDER BY id LIMIT 5900 OFFSET ' . ($i * 5900));

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
    } else {
      // rollback
      $item->truncate();
    }
  }
}
