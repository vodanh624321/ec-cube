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
use Eccube\Entity\Master\ProductListMax;
use Eccube\Repository\ProductRepository;

class TopController extends AbstractController
{

    public function index(Application $app)
    {
        return $app->render('index.twig');
    }

    public function indexB(Application $app)
    {
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

        return $app->render('index_b.twig', array('products' => $arrProduct));
    }

    public function indexC(Application $app)
    {
        return $app->render('index_c.twig');
    }
}
