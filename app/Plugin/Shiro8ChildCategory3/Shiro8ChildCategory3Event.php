<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\Shiro8ChildCategory3;

use Eccube\Event\EventArgs;
use Symfony\Component\DomCrawler\Crawler;
use Eccube\Event\TemplateEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Filesystem\Filesystem;
use Eccube\Util\Str;

class Shiro8ChildCategory3Event
{
    /**
     * @var \Eccube\Application
     */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    //フロント-商品一覧画面
    public function onRenderProductListBefore(FilterResponseEvent $event)
    {
        $app = $this->app;


        $request = $event->getRequest();
        $response = $event->getResponse();
        
        //$id = $request->attributes->get('category_id');
		$id = $request->query->get('category_id');

        // category_idがない場合、レンダリングを変更しない
        if (is_null($id) || $id == "") {
            $Categories = $app['eccube.repository.category']->getList();
            $categoryName = "全てのカテゴリ";
        } else {
        	$Category = $app['eccube.repository.category']->find($id);
        	$Categories = $Category->getChildren();
        	$categoryName = $Category->getName();
        }

        //        dump($Category);        
        //                dump($Categories);        
        $ChildCategoryCount = count($Categories);        

        // DomCrawlerにHTMLを食わせる
        $html = $response->getContent();
        $crawler = new Crawler($html);
        
        $twig = $app->renderView(
            'Shiro8ChildCategory3/Resource/template/plg_shiro8_child_category.twig',
            array('CategoryName' => $categoryName, 
                  'Categories' => $Categories,
                  'ChildCategoryCount' => $ChildCategoryCount
            )
        );
        
        
        $oldCrawler = $crawler
            ->filter('#topicpath')
            ->first();

        //$html = $crawler->html();
        $oldHtml = '';
        $newHtml = '';
        $search = '/<div id="topicpath" class="row">.*?<\/div>/s';
        if (count($oldCrawler) > 0) {
            $oldHtml = '<div id="topicpath" class="row">' . $oldCrawler->html() . '</div>';
            $newHtml = $oldHtml . "\n" . $twig;
        }

		$html = preg_replace($search, $newHtml, $html);

        $response->setContent($html);
        $event->setResponse($response);
    }
}
