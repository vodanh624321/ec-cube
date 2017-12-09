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

namespace Plugin\CheckedItem;

use Eccube\Plugin\AbstractPluginManager;
use Eccube\Entity\Master\DeviceType;
use Symfony\Component\Filesystem\Filesystem;

class PluginManager extends AbstractPluginManager
{

    public function install($config, $app)
    {
        $this->migrationSchema($app, __DIR__ . '/Migration', $config['code']);

        $file = new Filesystem();
        try {
          $file->copy($app['config']['root_dir']. '/app/Plugin/CheckedItem/Resource/template/Block/checkeditem.twig', $app['config']['template_realdir']. '/Block/checkeditem.twig', true);
          return true;
        } catch (\Exception $e) {
          return false;
        }
    }

    public function uninstall($config, $app)
    {
        unlink($app['config']['template_realdir']. '/Block/checkeditem.twig');

        $this->migrationSchema($app, __DIR__ . '/Migration', $config['code'], 0);
    }

    public function enable($config, $app)
    {   
        $Block = new \Eccube\Entity\Block();
        $Block->setFileName('checkeditem');
        $Block->setName('最近チェックした商品');
        $Block->setLogicFlg(1);
        $Block->setDeletableFlg(0);
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);
        $Block->setDeviceType($DeviceType);
        $app['orm.em']->persist($Block);
        $app['orm.em']->flush();
    }

    public function disable($config, $app)
    {      
        $Block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'checkeditem'));
        if($Block){
             $BlockPositions = $app['orm.em']
            ->getRepository('Eccube\Entity\BlockPosition')
            ->findBy(array('Block' => $Block));
            foreach($BlockPositions as $BlockPosition){
                $app['orm.em']->remove($BlockPosition);
            }
            $app['orm.em']->remove($Block);
            $app['orm.em']->flush();
        }
    }

    public function update($config, $app)
    {
    }
}
