<?php
namespace DoctrineMigrations;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\Tools\SchemaTool;
use Eccube\Application;
use Doctrine\ORM\EntityManager;
/**
 * Class Version20171206223925.
 */
class Version20171206223925 extends AbstractMigration
{
    /**
     * @var string table name
     */
    const TABLE = 'dtb_banner';
    
    /**
     * @var array plugin entity
     */
    protected $entity = 'Eccube\Entity\Banner';
    /**
     * Up method
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->createBannerTable($schema);
    }
    /**
     * Down method
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
    /**
     * Create TABLE table.
     *
     * @param Schema $schema
     *
     * @return bool
     */
    protected function createBannerTable(Schema $schema)
    {
        if ($schema->hasTable(self::TABLE)) {
            return true;
        }
        $app = Application::getInstance();
        $em = $app['orm.em'];
        $classes = array(
            $em->getClassMetadata($this->entity),
        );
        $tool = new SchemaTool($em);
        $tool->createSchema($classes);
        return true;
    }
    /**
     * Get metadata.
     *
     * @param EntityManager $em
     *
     * @return array
     */
    protected function getMetadata(EntityManager $em)
    {
        return $em->getMetadataFactory()->getMetadataFor($this->entity);
    }
}
