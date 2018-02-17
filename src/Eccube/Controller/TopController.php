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


namespace Eccube\Controller;

use Eccube\Application;
use Eccube\Entity\Category;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\ProductListMax;
use Eccube\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class TopController extends AbstractController
{
    const COOKIE_KEY = 'top_save';
    const INDEX_B = 'index-b';
    const INDEX_C = 'index-c';

    public function index(Application $app, Request $request)
    {
        if ($request->get('is')) {
            $cookie = 'index';
        } else {
            $cookie = $this->getDefaultIndexPage($request);
            switch ($cookie) {
                case self::INDEX_B:
                    return $app->redirect($app->url('homepage_b'));
                    break;
                case self::INDEX_C:
                    return $app->redirect($app->url('homepage_c'));
                    break;
            }
        }

        return $this->render($app,'index.twig', array(), $cookie);
    }

    public function indexB(Application $app, Request $request)
    {
        if ($request->get('is')) {
            $cookie = 'index-b';
        } else {
            $cookie = $this->getDefaultIndexPage($request);
            switch ($cookie) {
                case self::INDEX_B || null:
                    break;
                case self::INDEX_C:
                    return $app->redirect($app->url('homepage_c'));
                    break;
                default:
                    return $app->redirect($app->url('homepage'));
                    break;
            }
        }

        $Categories = $app['eccube.repository.category']->getList(null, false, Category::TYPE_B);

        $arrProduct = array();
        /** @var ProductRepository $productRepo */
        $productRepo = $app['eccube.repository.product'];
        /** @var ProductListMax $display */
        $display = $app['eccube.repository.master.product_list_max']->findOneBy(array(), array('rank' => 'ASC'));
        $max = !empty($display) ? $display->getId() : 50;
        /** @var Category $category */
        foreach ($Categories as $category) {
            $qb = $productRepo->getQueryBuilderBySearchData(array('category_id' => $category));
            // paginator
            $pagination = $app['paginator']()->paginate($qb, 1, $max);
            $arrProduct[$category->getId()]['name'] = $category->getName();
            $arrProduct[$category->getId()]['product'] = $pagination;
        }

        return $this->render($app, 'index_b.twig', array('products' => $arrProduct), $cookie);
    }

    public function indexC(Application $app, Request $request)
    {
        if ($request->get('is')) {
            $cookie = 'index-c';
        } else {
            $cookie = $this->getDefaultIndexPage($request);
            switch ($cookie) {
                case self::INDEX_B:
                    return $app->redirect($app->url('homepage_b'));
                    break;
                case self::INDEX_C || null:
                    break;
                default:
                    return $app->redirect($app->url('homepage'));
                    break;
            }
        }

        return $this->render($app, 'index_c.twig', array(), $cookie);
    }

    /**
     * @param Request $request
     * @return mixed|null
     */
    protected function getDefaultIndexPage(Request $request)
    {
        $cookie = $request->cookies->get(self::COOKIE_KEY);

//        dump($request->cookies);
//        dump($cookie);

        return $cookie;
    }

    /**
     * @param Application $app
     * @param string $template
     * @param array $option
     * @param $cookie
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function render(Application $app, $template = 'index.twig', $option = array(), $cookie = null)
    {
        $time = time() + 60*60*24*30;
        $urlpath = $app['config']['root_urlpath'];

        $response = $app->render($template, $option);
        if ($cookie) {
            $response->headers->setCookie(new Cookie(self::COOKIE_KEY, $cookie, $time, $urlpath));
        }
        return $response;
    }
}
