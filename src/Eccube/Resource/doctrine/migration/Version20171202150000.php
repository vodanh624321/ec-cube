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
use Eccube\Entity\Plugin;
use Eccube\Entity\PluginEventHandler;
use Eccube\Util\Cache;
use Eccube\Service\PluginService;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171202150000 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $t = $schema->getTable('dtb_category');
        if (!$t->hasColumn('category_image')) {
            $t->addColumn('category_image', 'text', array('NotNull' => false));
        }

        // Install plugin
        $app = \Eccube\Application::getInstance();
        $app->removePluginConfigCache();
        Cache::clear($app, false);
        /** @var Plugin $plugin */
        $plugin = $app['eccube.repository.plugin']->findOneBy(array('code' => 'RelatedProduct', 'del_flg' => 0));
        if ($plugin) {
            return;
        }

        $pluginService = $app['eccube.service.plugin'];
        $dir = $app['config']['plugin_realdir'].'/RelatedProduct/';
        $config = $pluginService->readYml($dir.PluginService::CONFIG_YML);
        $event = $pluginService->readYml($dir.PluginService::EVENT_YML);
        $pluginService->registerPlugin($config, $event, 0); // dbにプラグイン登録
        $app->writePluginConfigCache();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
