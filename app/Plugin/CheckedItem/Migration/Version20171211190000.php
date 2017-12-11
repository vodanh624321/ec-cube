<?php
/*
* Plugin Name : CheckedItem
*
* Copyright (C) 2015 BraTech Co., Ltd. All Rights Reserved.
* http://www.bratech.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Application;
use Plugin\CheckedItem\Entity\Config;

class Version20171211190000 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $app = Application::getInstance();
        /** @var Config $config */
        $config = $app['eccube.checkeditem.repository.config']->findOneBy(array('name' => 'delete'));

        if ($config) {
            $config->setValue(1);
            $app['orm.em']->persist($config);
            $app['orm.em']->flush();
        }


    }

    public function down(Schema $schema)
    {
    }
}
