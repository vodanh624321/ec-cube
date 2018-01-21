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
use Eccube\Common\Constant;
use Eccube\Entity\News;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Repository\NewsRepository;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsController
{

    private $title;

    public function __construct()
    {
        $this->title = 'プレスリリース';
    }

    public function index(Application $app, Request $request)
    {
        if ($request->getMethod() === 'GET') {
            $request->query->set('pageno', $request->query->get('pageno', 1));
        }
        $pageNo = $request->get('pageno', 1);
        /** @var NewsRepository $newsRepos */
        $newsRepos = $app['eccube.repository.news'];
        $qb = $newsRepos->createQueryBuilder('n');
        $qb->addOrderBy('n.rank', 'DESC')
        ->where('n.del_flg = '.Constant::DISABLED);

        $pagination = $app['paginator']()->paginate(
            $qb,
            $pageNo,
            10
        );

        return $app->render('News/list.twig', array(
            'breadcrumb' => $this->title,
            'pagination' => $pagination,
        ));
    }

    public function detail(Application $app, Request $request, $id)
    {
        /** @var News $News */
        $News = $app['eccube.repository.news']->find($id);
        if (!$News) {
            throw new NotFoundHttpException();
        }
        $url = $app->url('news_list');
        $breadcrumb = "<a href=\"{$url}\">プレスリリース</a>";
        $breadcrumb .= "<a href=\"{$url}\">".$News->getDate()->format('Y')."</a>";
        $breadcrumb .= $News->getTitle();
        $this->title = $breadcrumb;

        return $app->render('News/detail.twig', array(
            'breadcrumb' => $this->title,
            'News' => $News
        ));
    }
}
