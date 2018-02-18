<?php
/**
 * Created by PhpStorm.
 * User: lqdung1992@gmail.com
 * Date: 02/18/2018
 * Time: 09:16 PM
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20180218213700
 * @package DoctrineMigrations
 */
class Version20180218213700 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $table = $schema->getTable('dtb_product');
        if (!$table->hasColumn('unit')) {
            $table->addColumn('unit', 'string', array('NotNull' => false, 'length' => 255));
        }
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}