<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Service\PluginService;
use Eccube\Util\Cache;

class Version20171209110000 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Install plugin
        $app = \Eccube\Application::getInstance();
        $app->removePluginConfigCache();
        Cache::clear($app, false);
        $pluginService = $app['eccube.service.plugin'];
        $dir = $app['config']['plugin_realdir'].'/CheckedItem/';
        $config = $pluginService->readYml($dir.PluginService::CONFIG_YML);
        $event = $pluginService->readYml($dir.PluginService::EVENT_YML);
        $pluginService->registerPlugin($config, $event, 0); // dbにプラグイン登録

        $dir = $app['config']['plugin_realdir'].'/Holiday/';
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
