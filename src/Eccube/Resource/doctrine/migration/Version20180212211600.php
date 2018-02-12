<?php
/**
 * Created by PhpStorm.
 * User: lqdung1992@gmail.com
 * Date: 02/12/2018
 * Time: 9:16 PM
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20180112211600
 * @package DoctrineMigrations
 */
class Version20180212211600 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $table = $schema->getTable('dtb_customer');
        if (!$table->hasColumn('page')) {
            $table->addColumn('page', 'string', array('NotNull' => false, 'length' => 20));
        }
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}