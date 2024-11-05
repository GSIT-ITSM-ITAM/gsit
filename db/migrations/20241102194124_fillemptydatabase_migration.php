<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class FillemptydatabaseMigration extends AbstractMigration
{
  public function change(): void
  {
    if ($this->isMigratingUp())
    {
      $builderSelect = $this->getQueryBuilder('select');
      $statement = $builderSelect->select('*')->from('entities')->execute();
      $items = $statement->fetchAll('assoc');
      if (count($items) > 0)
      {
        return;
      }
      // Now Fill with default data

      $item = $this->table('entities');
      $data = [
        [
          'name' => 'gsit',
          'treepath' => 00001,
        ]
      ];
      $item->insert($data)
           ->saveData();

      // Create user
      $item = $this->table('users');
      $data = [
        [
          'name' => 'admin@foo.com',
          'entity_id' => 1,
          'lastname' => 'Administrator',
          'password' => '',
        ]
      ];
      $item->insert($data)
           ->saveData();

      // Create profile
      $item = $this->table('profiles');
      $data = [
        [
          'name' => 'super-admin',
        ]
      ];
      $item->insert($data)
           ->saveData();

      // Create profile rights
      $item = $this->table('profilerights');
      $profileright = new \App\v1\Controllers\Profile();
      $models = $profileright->getRigthCategories();
      $data = [];
      foreach ($models as $modellist)
      {
        foreach ($modellist as $model)
        {
          $data[] = [
            'model'       => $model,
            'profile_id'  => 1,
            'read'        => 1,
            'create'      => 1,
            'update'      => 1,
            'softdelete'  => 1,
            'delete'      => 1
          ];
        }
      }
      $item->insert($data)
           ->saveData();
    }
  }
}
