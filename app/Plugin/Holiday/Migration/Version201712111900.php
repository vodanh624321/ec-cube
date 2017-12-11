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
use Plugin\Holiday\Entity\HolidayWeek;

class Version201712111900 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $app = Application::getInstance();
        /** @var HolidayWeek $configWeek */
        $configWeek = $app['eccube.plugin.holiday.repository.holidayweek']->find(1);

        if (!$configWeek) {
            $configWeek = new HolidayWeek();
        }
        $fixWeek = array(
            0 => 0
        );

        $configWeek->setWeek(serialize($fixWeek));
        $app['orm.em']->persist($configWeek);
        $app['orm.em']->flush();
    }

    public function down(Schema $schema)
    {
    }
}
