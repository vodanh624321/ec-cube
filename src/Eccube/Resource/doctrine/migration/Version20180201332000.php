<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Block;
use Eccube\Entity\BlockPosition;
use Eccube\Entity\Category;
use Eccube\Entity\PageLayout;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180201332000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($this->connection->getDatabasePlatform()->getName() == "mysql") {
            $this->addSql("SET FOREIGN_KEY_CHECKS=0;");
            $this->addSql("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO';");
        }
        $app = \Eccube\Application::getInstance();
        /** @var EntityManagerInterface $em */
        $em = $app["orm.em"];

        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);

        /** @var PageLayout $pageHelp */
        $pageHelp = $app['eccube.repository.page_layout']->get($DeviceType, 35);

        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'guide_side'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($pageHelp)->setPageId($pageHelp->getId())
            ->setTargetId(4)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(1)
            ->setAnywhere(0);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);
        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
