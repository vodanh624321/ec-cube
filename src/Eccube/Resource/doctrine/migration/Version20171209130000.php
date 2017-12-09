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

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171209130000 extends AbstractMigration
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
        $this->addSql("UPDATE dtb_block SET block_name='Free area' WHERE  block_id=9;");
        $this->addSql("UPDATE dtb_block SET block_name='カテゴリ別製品' WHERE block_id=8;");
        $this->addSql("UPDATE dtb_block_position SET block_row=4 WHERE block_id=9;");
        $this->addSql("UPDATE dtb_block_position SET block_row=3 WHERE block_id=8 AND page_id=3;");

        $app = \Eccube\Application::getInstance();
        /** @var EntityManagerInterface $em */
        $em = $app["orm.em"];

        /** @var Block $block */
        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'pg_calendar'));
        $page = $app['eccube.repository.page_layout']->find(1);
        if (!$block) {
            $block = new Block();
            $DeviceType = $app['eccube.repository.master.device_type']->find(10);
            $block->setDeviceType($DeviceType);
            $block->setFileName('pg_calendar')
                ->setName('定休日カレンダー')
                ->setLogicFlg(0)
                ->setDeletableFlg(0);

            $em->persist($block);
        }
        if ($block) {
            /** @var BlockPosition $blockPosition */
            $blockPosition = new BlockPosition();
            $blockPosition->setPageLayout($page)->setPageId(1);
            $blockPosition->setTargetId(4);
            $blockPosition->setBlockId($block->getId());
            $blockPosition->setBlock($block);
            $blockPosition->setBlockRow(3);
            $blockPosition->setAnywhere(1);

            $block->addBlockPosition($blockPosition);

            $em->persist($blockPosition);
            $em->persist($block);
            $em->flush();
        }

        /** @var Block $block */
        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'checkeditem'));
        $page = $app['eccube.repository.page_layout']->find(3);
        if ($block) {
            /** @var BlockPosition $blockPosition */
            $blockPosition = new BlockPosition();
            $blockPosition->setPageLayout($page)->setPageId(3);
            $blockPosition->setTargetId(6);
            $blockPosition->setBlockId($block->getId());
            $blockPosition->setBlock($block);
            $blockPosition->setBlockRow(2);
            $blockPosition->setAnywhere(0);

            $block->addBlockPosition($blockPosition);

            $em->persist($blockPosition);
            $em->persist($block);
            $em->flush();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
