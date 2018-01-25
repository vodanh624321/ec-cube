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
use Eccube\Entity\Category;
use Eccube\Entity\Master\Tag;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController
{

    private $title;

    public function __construct()
    {
        $this->title = '';
    }

    public function index(Application $app, Request $request)
    {
        $BaseInfo = $app['eccube.repository.base_info']->get();

        // Doctrine SQLFilter
        if ($BaseInfo->getNostockHidden() === Constant::ENABLED) {
            $app['orm.em']->getFilters()->enable('nostock_hidden');
        }

        // handleRequestは空のqueryの場合は無視するため
        if ($request->getMethod() === 'GET') {
            $request->query->set('pageno', $request->query->get('pageno', ''));
        }

        // searchForm
        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createNamedBuilder('', 'search_product');
//        $builder->setAttribute('freeze', true);
//        $builder->setAttribute('freeze_display_text', false);
        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_INITIALIZE, $event);

        /* @var $searchForm \Symfony\Component\Form\FormInterface */
        $searchForm = $builder->getForm();
        $searchForm->get('recommend_id')->setData($request->get('recommend_id'));
        $searchForm->get('disp_number')->setData(10);
        $searchForm->handleRequest($request);

        // paginator
        $searchData = $searchForm->getData();
        $searchData['disp_number'] = 10;
        /** @var ProductRepository $productRepo */
        $productRepo = $app['eccube.repository.product'];
        $qb = $productRepo->getQueryBuilderBySearchData($searchData);

        $event = new EventArgs(
            array(
                'searchData' => $searchData,
                'qb' => $qb,
            ),
            $request
        );
//        dump($searchData);
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_SEARCH, $event);
        $searchData = $event->getArgument('searchData');

//        dump($searchData);
        $pagination = $app['paginator']()->paginate(
            $qb,
            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
            !empty($searchData['disp_number']) ? $searchData['disp_number'] : 10
        );

        // addCart form
        $forms = array();
        foreach ($pagination as $Product) {
            /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
            $builder = $app['form.factory']->createNamedBuilder('', 'add_cart', null, array(
                'product' => $Product,
                'allow_extra_fields' => true,
            ));
            $addCartForm = $builder->getForm();

            if ($request->getMethod() === 'POST' && (string)$Product->getId() === $request->get('product_id')) {
                $addCartForm->handleRequest($request);

                if ($addCartForm->isValid()) {
                    $addCartData = $addCartForm->getData();

                    try {
                        $app['eccube.service.cart']->addProduct($addCartData['product_class_id'], $addCartData['quantity'])->save();
                    } catch (CartException $e) {
                        $app->addRequestError($e->getMessage());
                    }

                    $event = new EventArgs(
                        array(
                            'form' => $addCartForm,
                            'Product' => $Product,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_PRODUCT_INDEX_COMPLETE, $event);

                    if ($event->getResponse() !== null) {
                        return $event->getResponse();
                    }

                    return $app->redirect($app->url('cart'));
                }
            }

            $forms[$Product->getId()] = $addCartForm->createView();
        }

        $Category = $searchForm->get('category_id')->getData();
        /** @var CategoryRepository $CateRepo */
        $CateRepo = $app['eccube.repository.category'];
        $categories = $CateRepo->getList();
        $arrCateC = array();
        $arrCateG = array();
        $cates = array();
        foreach ($categories as $cate) {
            $cates[$cate->getId()] = $cate->toArray();
            if ($cate->getChildren()->count() > 0) {
                $childs = $cate->getChildren();
                /** @var Category $child */
                foreach ($childs as $child) {
                    $arrCateC[$cate->getId()][] = $child->toArray();
                    if ($child->getChildren()->count() > 0) {
                        foreach ($child->getChildren() as $grandson) {
                            $arrCateG[$child->getId()][] = $grandson->toArray();
                        }
                    }
                }
            }
        }
        $Tag = $app['eccube.repository.master.tag']->findBy(array(), array('rank' => 'ASC'));

        return $app->render('Product/list.twig', array(
            'subtitle' => $this->getPageTitle($searchData),
            'pagination' => $pagination,
            'search_form' => $searchForm->createView(),
            'forms' => $forms,
            'Category' => $Category,
            'cate' => $cates,
            'cate_child' => $arrCateC,
            'cate_grandson' => $arrCateG,
            'breadcrumb' => '商品一覧',
            'recommend' =>  Tag::Recommend,
            'tags' => $Tag
        ));
    }

    public function detail(Application $app, Request $request, $id)
    {
        $BaseInfo = $app['eccube.repository.base_info']->get();
        if ($BaseInfo->getNostockHidden() === Constant::ENABLED) {
            $app['orm.em']->getFilters()->enable('nostock_hidden');
        }

        /* @var $Product \Eccube\Entity\Product */
        $Product = $app['eccube.repository.product']->get($id);
        if (!$request->getSession()->has('_security_admin') && $Product->getStatus()->getId() !== 1) {
            throw new NotFoundHttpException();
        }
        if (count($Product->getProductClasses()) < 1) {
            throw new NotFoundHttpException();
        }

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createNamedBuilder('', 'add_cart', null, array(
            'product' => $Product,
            'id_add_product_id' => false,
        ));

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Product' => $Product,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_PRODUCT_DETAIL_INITIALIZE, $event);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $addCartData = $form->getData();
                if ($addCartData['mode'] === 'add_favorite') {
                    if ($app->isGranted('ROLE_USER')) {
                        $Customer = $app->user();
                        $app['eccube.repository.customer_favorite_product']->addFavorite($Customer, $Product);
                        $app['session']->getFlashBag()->set('product_detail.just_added_favorite', $Product->getId());

                        $event = new EventArgs(
                            array(
                                'form' => $form,
                                'Product' => $Product,
                            ),
                            $request
                        );
                        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_PRODUCT_DETAIL_FAVORITE, $event);

                        if ($event->getResponse() !== null) {
                            return $event->getResponse();
                        }

                        return $app->redirect($app->url('product_detail', array('id' => $Product->getId())));
                    } else {
                        // 非会員の場合、ログイン画面を表示
                        //  ログイン後の画面遷移先を設定
                        $app->setLoginTargetPath($app->url('product_detail', array('id' => $Product->getId())));
                        $app['session']->getFlashBag()->set('eccube.add.favorite', true);
                        return $app->redirect($app->url('mypage_login'));
                    }
                } elseif ($addCartData['mode'] === 'add_cart') {

                    log_info('カート追加処理開始', array('product_id' => $Product->getId(), 'product_class_id' => $addCartData['product_class_id'], 'quantity' => $addCartData['quantity']));

                    try {
                        $app['eccube.service.cart']->addProduct($addCartData['product_class_id'], $addCartData['quantity'])->save();
                    } catch (CartException $e) {
                        log_info('カート追加エラー', array($e->getMessage()));
                        $app->addRequestError($e->getMessage());
                    }

                    log_info('カート追加処理完了', array('product_id' => $Product->getId(), 'product_class_id' => $addCartData['product_class_id'], 'quantity' => $addCartData['quantity']));

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Product' => $Product,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_PRODUCT_DETAIL_COMPLETE, $event);

                    if ($event->getResponse() !== null) {
                        return $event->getResponse();
                    }

                    return $app->redirect($app->url('cart'));
                }
            }
        } else {
            $addFavorite = $app['session']->getFlashBag()->get('eccube.add.favorite');
            if (!empty($addFavorite)) {
                // お気に入り登録時にログインされていない場合、ログイン後にお気に入り追加処理を行う
                if ($app->isGranted('ROLE_USER')) {
                    $Customer = $app->user();
                    $app['eccube.repository.customer_favorite_product']->addFavorite($Customer, $Product);
                    $app['session']->getFlashBag()->set('product_detail.just_added_favorite', $Product->getId());
                }
            }
        }

        $is_favorite = false;
        if ($app->isGranted('ROLE_USER')) {
            $Customer = $app->user();
            $is_favorite = $app['eccube.repository.customer_favorite_product']->isFavorite($Customer, $Product);
        }

        return $app->render('Product/detail.twig', array(
            'title' => $this->title,
            'subtitle' => $Product->getName(),
            'form' => $form->createView(),
            'Product' => $Product,
            'is_favorite' => $is_favorite,
        ));
    }

    /**
     * ページタイトルの設定
     *
     * @param  null|array $searchData
     * @return str
     */
    private function getPageTitle($searchData)
    {
        if (isset($searchData['name']) && !empty($searchData['name'])) {
            return '検索結果';
        } elseif (isset($searchData['category_id']) && $searchData['category_id']) {
            return $searchData['category_id']->getName();
        } else {
            return '全商品';
        }
    }
}
