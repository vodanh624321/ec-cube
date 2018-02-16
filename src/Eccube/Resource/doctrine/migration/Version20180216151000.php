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
 * Class Version20180216151000
 * @package DoctrineMigrations
 */
class Version20180216151000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $table = $schema->getTable('dtb_product_image');
        if (!$table->hasColumn('comment')) {
            $table->addColumn('comment', 'string', array('NotNull' => false, 'length' => 255));
        }
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}