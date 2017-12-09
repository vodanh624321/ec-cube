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

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CheckedItemEvent
{

    /**
     * @var \Eccube\Application
     */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onRenderProductDetail($event = null)
    {
        $app = $this->app;

        $request = $app['request'];

        $product_id = $request->get('id');
        $Product = $app['eccube.repository.product']->find($product_id);
        if ($Product) {
            if($event){
                $response = $event->getResponse();
                $response = $app['eccube.checkeditem.service.util']->setCheckedItem($product_id, $response);
            }else{
                $response = $app['eccube.checkeditem.service.util']->setCheckedItem($product_id);
                $response->send();
            }
        }
    }
}
