<?php
/*
 * Plugin Name : CustomerRank
 *
 * Copyright (C) 2015 BraTech Co., Ltd. All Rights Reserved.
 * http://www.bratech.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CustomerRank\Event\WorkPlace;

use Eccube\Entity\Product;
use Eccube\Event\TemplateEvent;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class FrontProductDetail extends AbstractWorkPlace
{
    public function render(FilterResponseEvent $event)
    {
        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();

        $html = $response->getContent();

        $product_id = $request->get('id');
        /** @var Product $Product */
        $Product = $app['eccube.repository.product']->get($product_id);
        $hasStock = $Product->getStockFind();

        $CustomerRank = $app['eccube.customerrank.service.util']->getCustomerRank(true);

        if(!is_null($CustomerRank)){
            list($member_price, $member_price_inctax) = $app['eccube.customerrank.service.util']->getMemberPrice($Product, $CustomerRank);

            $twig = '';
            if(!empty($member_price)){
                $Product = array();
                $Product['member_price_Max'] = max($member_price);
                $Product['member_price_Min'] = min($member_price);
                $Product['member_price_incMax'] = max($member_price_inctax);
                $Product['member_price_incMin'] = min($member_price_inctax);
                $Product['stock_find'] = $hasStock;

                $twig = $app->renderView(
                    'Product/detail_member_price.twig',
                    array('member_title' => $CustomerRank->getName(),
                          'MemberPrice' => $Product,
                        )
                );
            }

            $html = preg_replace('/<!\-\-\s*member_price\s*\-\->/',$twig,$html);

        }

        $response->setContent($html);
        $event->setResponse($response);
    }

    public function createTwig(TemplateEvent $event)
    {
        $parameters = $event->getParameters();
        $Product = $parameters['Product'];

        $MemberPrices = $Product->getClassCategories();
        foreach($MemberPrices as &$MemberPrice){
            foreach($MemberPrice as &$item){
                unset($item['stock_find']);
                unset($item['name']);
                unset($item['price01']);
                unset($item['price02']);
                unset($item['product_code']);
                unset($item['product_type']);
                if(strlen($item['product_class_id']) > 0){
                    $ProductClass = $this->app['eccube.repository.product_class']->find(intval($item['product_class_id']));
                    $item['price03'] = number_format($this->app['eccube.customerrank.service.util']->getMemberPriceIncTaxForFront($ProductClass, true));
                }
            }
        }
        $parameters['MemberPrices'] = $MemberPrices;

        $source = $event->getSource();
        if(preg_match('/\{%\s*block\s*javascript\s*%\}/',$source, $result)){
            $start_tag = $result[0];
            $index = strpos($source, $start_tag);
            $search_source = substr($source, $index);
            $end_index = strpos($search_source, '{% endblock %}');
            $search = substr($source, $index, ($end_index));

            $snipet = file_get_contents($this->app['config']['plugin_realdir']. '/CustomerRank/Resource/template/default/Product/detail_js.twig');
            $replace = $search.$snipet;
            $source = str_replace($search, $replace, $source);
        }

        if($this->app['eccube.customerrank.service.util']->checkInstallPlugin('Point')){
            $calculator = $this->app['eccube.plugin.point.calculate.helper.factory'];
            $point = $calculator->getAddPointByProduct($Product);

            $search = $this->app->renderView(
                'Point/Resource/template/default/Event/ProductDetail/detail_point.twig',
                array(
                    'point' => $point,
                )
            );

            $calculator = $this->app['eccube.customerrank.calculate.helper.factory'];
            $point = $calculator->getAddPointByProduct($Product);

            $replace = $this->app->renderView(
                'Point/Resource/template/default/Event/ProductDetail/detail_point.twig',
                array(
                    'point' => $point,
                )
            );

            $source = str_replace($search, $replace, $source);
        }
        $event->setSource($source);
        $event->setParameters($parameters);
    }
}
