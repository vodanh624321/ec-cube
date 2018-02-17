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

namespace Plugin\PlgExpandProductColumns;

use Eccube\Common\Constant;
use Eccube\Event\TemplateEvent;
use Plugin\PlgExpandProductColumns\Controller\PlgExpandProductColumnsCsvImportController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class Event
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 拡張項目の保存
     *
     * 商品の新規登録時はURLにProductのidが存在しないので
     * 登録後のイベントで保存することにする
     *
     * @param FilterResponseEvent $event
     */
    public function saveExColValue(FilterResponseEvent $event)
    {
        
        $app = $this->app;

        if ('POST' === $app['request']->getMethod()) {

            // ProductControllerの登録成功時のみ処理を通す
            // RedirectResponseかどうかで判定する.
            $response = $event->getResponse();
            if (!$response instanceof RedirectResponse) {
                return;
            }

            // 保存したい値が入っているか確認
            if (!(isset($app['plgExpandProductColumnsValue_temp'])
                && is_array($app['plgExpandProductColumnsValue_temp']))
            ) {
                return;
            }

            /* @var $Product \Eccube\Entity\Product */
            $Product = $this->getTargetProduct($event);
            $save_data = $app['plgExpandProductColumnsValue_temp'];
            $repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue');

            foreach ($save_data as $data) {
                /*
                 * 値が入っていないとDelete対象になる
                 */
                $repository->save(
                    $Product->getId(),
                    $data['column_id'],
                    $data['value']
                );
            }

            unset($app['plgExpandProductColumnsValue_temp']);
            
        }
    }

    private function getTargetProduct($event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        if ($request->attributes->get('id')) {
            $id = $request->attributes->get('id');
        } else {
            $location = explode('/', $response->headers->get('location'));
            $url = explode('/', $this->app->url('admin_product_product_edit', array('id' => '0')));
            $diffs = array_values(array_diff($location, $url));
            $id = $diffs[0];
        }

        $Product = $this->app['eccube.repository.product']->find($id);

        return $Product;
    }

    public function setListOnRenderFront(TemplateEvent $event)
    {
        // twigパラメータを編集する方法
        $app = $this->app;
        $value_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue');
        $column_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumns');
        $parameters = $event->getParameters();
        $__ex_product_list = array();

        // 検索対象名を取得
        $names = @file_get_contents(__DIR__.'/target.json');
        if (empty($names)) {
            $names = array('pagination');
        } else {
            $names = json_decode($names, true);
        }

        foreach ($names as $name) {
            if (!empty($parameters[$name])) {
                $pagination = $parameters[$name];
            } else {
                // TODO 現状こっちに入るとMySQL5.7ではエラーになる http://jmatsuzaki.com/archives/18846
                // $pagination = $this->getPagination($app);
                $pagination = array();
            }

            foreach ($pagination as $Product) {
                if (!isset($__ex_product_list[$Product->getId()])) {
                    $__ex_product_list[$Product->getId()] =
                        $this->getProductExt($Product->getId(), $value_repository, $column_repository);
                }
            }
        }
        
        $parameters['__EX_PRODUCT_LIST'] = $__ex_product_list;
        $event->setParameters($parameters);
    }

    public function setProductOnRenderFront(TemplateEvent $event)
    {
        // twigパラメータを編集する方法
        $app = $this->app;
        $value_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue');
        $column_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumns');
        $parameters = $event->getParameters();

        $id = $app['request']->attributes->get('id');
        $__ex_product = $this->getProductExt($id, $value_repository, $column_repository);

        $parameters['__EX_PRODUCT'] = $__ex_product;
        $event->setParameters($parameters);
    }

    public function onRenderAdminCsvImport(TemplateEvent $event)
    {
        
        /**
         * twigコードにソースを挿入
         */
        // 独自TWIGを追加する
        $snipet = 
<<<EOD
{% for header in ex_headers %}
    <td id="file_format_box__{{ header.id }}">{{ header.description|raw }}</td> 
{% endfor %}
EOD;
        $search = '<td id="file_format_box__category_delete_flg">設定されていない場合<br>0を登録</td>';
        $replace = $search.$snipet;
        $source = str_replace($search, $replace, $event->getSource());
        $event->setSource($source);

        // twigパラメータを編集する方法
        $parameters = $event->getParameters();
        $parameters['ex_headers'] = PlgExpandProductColumnsCsvImportController::getExColumnHeaders($this->app);
        $event->setParameters($parameters);
    }

    public function onRenderAdminProductNew(TemplateEvent $event)
    {
        
        /**
         * twigコードにソースを挿入
         */
        // 独自JSを追加する
        $snipet = html_entity_decode(file_get_contents(__DIR__. '/Resource/assets/js/product.js.twig'));
        $search = '{% endblock javascript %}';
        $replace = $snipet.$search;
        $source = str_replace($search, $replace, $event->getSource());

        // デフォルトJSを一部編集する(画像アップロードのDrop範囲を限定する)
        $snipet2 = 'dropZone: $("#drag-drop-area"),';
        $search2 = "$('#{{ form.product_image.vars.id }}').fileupload({";
        $replace2 = $search2.$snipet2;
        $source2 = str_replace($search2, $replace2, $source);

        $event->setSource($source2);

        // twigパラメータを編集する方法
        $parameters = $event->getParameters();
        $ex_images = array();
        if (!is_null($parameters['id'])) {
            $ex_images = $this->app['eccube.plugin.repository.plg_expand_product_columns_value']->getExProductImages($parameters['id']);
        }
        $parameters['ex_images'] = $ex_images;
        $event->setParameters($parameters);
    }

    public function addContentOnProductEdit(FilterResponseEvent $event)
    {
        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();
        $id = $request->attributes->get('id');

        $form = $app['form.factory']
            ->createBuilder('admin_product')
            ->getForm();

        /*
         * 入力済みのValueを取得
         */
        $input_values = array();
        if ($request->getMethod() === 'POST' && !$form->isValid()) {
            foreach ($request->request->get('admin_product')['admin_plg_expand_product_columns_value'] as $data) {
                foreach ($data as $key => $val) {
                    $col_id = explode('-', $key)[1];
                    $PlgExpandProductColumnsValue = new \Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue();
                    $PlgExpandProductColumnsValue->setColumnId($col_id);
                    $PlgExpandProductColumnsValue->setValue($val);
                    $input_values[$col_id] = $PlgExpandProductColumnsValue; 
                }
                
            }
        } else if ($id) {
            $Product = $app['eccube.repository.product']->find($id);
            $saved_values = $this->app['eccube.plugin.repository.plg_expand_product_columns_value']
                ->findBy(array('productId' => $Product->getId()));
            foreach ($saved_values as $saved_value) {
                $input_values[$saved_value->getColumnId()] = $saved_value;
            }
        }

        /*
         * 入力していないColumnは
         * ValueをNullの状態で作成する
         */
        $ex_columns_values = array();
        $ex_columns = $this->app['eccube.plugin.repository.plg_expand_product_columns']->findAll();
        foreach ($ex_columns as $ex_column) {
            $col_id = $ex_column->getColumnId();
            if (isset($input_values[$col_id])) {
                $ex_columns_values[] = $input_values[$col_id];
            } else {
                $PlgExpandProductColumnsValue = new \Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue();
                $PlgExpandProductColumnsValue->setColumnId($col_id);
                $ex_columns_values[] = $PlgExpandProductColumnsValue;

            }
        }
        
        $form->get('admin_plg_expand_product_columns_value')
            ->setData($ex_columns_values);
        
        $twig = $app->renderView(
            'PlgExpandProductColumns/Resource/template/Admin/expand_column.twig',
            array(
                'form' => $form->createView(),
            )
        );
        
        $search = '<div id="detail_box__footer" class="row hidden-xs hidden-sm">';

        $html = $response->getContent();
        if (strpos($search, $html) !== null) {
            $newHtml = $twig . $search;
            $html = str_replace($search, $newHtml, $html);
            $response->setContent(($html));
            $event->setResponse($response);
        }
    }

//    public function setExpandColumns(\Symfony\Component\EventDispatcher\Event $event)
//    {
//        
//
//        $app = $this->app;
//        $value_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue');
//        $column_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumns');
//
//        $route = $app['request']->attributes->get('_route');
//
//        switch ($route) {
//            case 'product_detail':
//                $id = $app['request']->attributes->get('id');
//                $__ex_product = $this->getProductExt($id, $value_repository, $column_repository);
//                $app['twig']->addGlobal('__EX_PRODUCT', $__ex_product);
//                break;
//            case 'product_list':
//                $__ex_product_list = array();
//                $pagination = $this->getPagination($app);
//                foreach ($pagination as $Product) {
//                    $__ex_product_list[$Product->getId()] = $this->getProductExt($Product->getId(), $value_repository, $column_repository);
//                }
//
//                $app['twig']->addGlobal('__EX_PRODUCT_LIST', $__ex_product_list);
//
//                /*$category_id = $app['request']->query->get('category_id');
//                if (empty($category_id)) {
//                    // 全件
//
//                } else {
//                    // カテゴリ
//                }*/
//                break;
//            case 'admin_product':
//                $__ex_product_list = array();
//                $pagination = $this->getPaginationForAdmin($app);
//                foreach ($pagination as $Product) {
//                    $__ex_product_list[$Product->getId()] = $this->getProductExt($Product->getId(), $value_repository, $column_repository);
//                }
//
//                $app['twig']->addGlobal('__EX_PRODUCT_LIST', $__ex_product_list);
//                break;
//
//        }
//    }

    private function getProductExt($id, $value_repository, $column_repository)
    {
        $product_ex = array();
        $columns = $column_repository->findAll();

        /** @var \Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumns $column */
        foreach ($columns as $column) {
            $value = $value_repository->findOneBy(array(
                'columnId' => $column->getColumnId(),
                'productId' => $id));
            /**
             * 配列系の値の場合、配列にしてから渡す
             */
            switch ($column->getColumnType()) {
                case EX_TYPE_IMAGE :
                case EX_TYPE_CHECKBOX :
                    if (empty($value)) {
                        $value = '';
                    } else {
                        $value = explode(',', $value->getValue());
                    }
                    break;
                default :
                    $value = empty($value) ? '' : $value->getValue();
            }
            
            $product_ex[$column->getColumnId()] = array(
                'id' => $column->getColumnId(),
                'name' => $column->getColumnName(),
                'value' => $value
            );
        }

        return $product_ex;
    }

//    /**
//     *
//     * eccube/src/Eccube/Controller/Admin/Product/ProductController.php
//     * のinitメソッドのほぼコピー
//     *
//     * $page_noをちゃんと入れるのが課題
//     *
//     * @param $app
//     * @param null $page_no
//     * @return array
//     */
//    private function getPaginationForAdmin($app, $page_no = null)
//    {
//        $session = $app['session'];
//        $request = $app['request'];
//
//        $searchForm = $app['form.factory']
//            ->createBuilder('admin_search_product')
//            ->getForm();
//
//        $pagination = array();
//
//        $disps = $app['eccube.repository.master.disp']->findAll();
//        $pageMaxis = $app['eccube.repository.master.page_max']->findAll();
//        $page_count = $app['config']['default_page_count'];
//        $page_status = null;
//        $active = false;
//
//        if ('POST' === $request->getMethod()) {
//
//            $searchForm->handleRequest($request);
//
//            if ($searchForm->isValid()) {
//                $searchData = $searchForm->getData();
//
//                // paginator
//                $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);
//                $page_no = 1;
//                $pagination = $app['paginator']()->paginate(
//                    $qb,
//                    $page_no,
//                    $page_count,
//                    array('wrap-queries' => true)
//                );
//
//                // sessionのデータ保持
//                $session->set('eccube.admin.product.search', $searchData);
//            }
//        } else {
//            if (is_null($page_no)) {
//                // sessionを削除
//                $session->remove('eccube.admin.product.search');
//            } else {
//                // pagingなどの処理
//                $searchData = $session->get('eccube.admin.product.search');
//                if (!is_null($searchData)) {
//
//                    // 公開ステータス
//                    $status = $request->get('status');
//                    if (!empty($status)) {
//                        if ($status != $app['config']['admin_product_stock_status']) {
//                            $searchData['link_status'] = $app['eccube.repository.master.disp']->find($status);
//                            $searchData['status'] = null;
//                            $session->set('eccube.admin.product.search', $searchData);
//                        } else {
//                            $searchData['stock_status'] = Constant::DISABLED;
//                        }
//                        $page_status = $status;
//                    } else {
//                        $searchData['link_status'] = null;
//                        $searchData['stock_status'] = null;
//                    }
//                    // 表示件数
//                    $pcount = $request->get('page_count');
//
//                    $page_count = empty($pcount) ? $page_count : $pcount;
//
//                    $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);
//                    $pagination = $app['paginator']()->paginate(
//                        $qb,
//                        $page_no,
//                        $page_count,
//                        array('wrap-queries' => true)
//                    );
//
//                    // セッションから検索条件を復元
//                    if (!empty($searchData['category_id'])) {
//                        $searchData['category_id'] = $app['eccube.repository.category']->find($searchData['category_id']);
//                    }
//                    if (empty($status)) {
//                        if (count($searchData['status']) > 0) {
//                            $status_ids = array();
//                            foreach ($searchData['status'] as $Status) {
//                                $status_ids[] = $Status->getId();
//                            }
//                            $searchData['status'] = $app['eccube.repository.master.disp']->findBy(array('id' => $status_ids));
//                        }
//                        $searchData['link_status'] = null;
//                        $searchData['stock_status'] = null;
//                    }
//                    $searchForm->setData($searchData);
//                }
//            }
//        }
//
//        return $pagination;
//    }

//    private function getPagination($app)
//    {
//        $request = $app['request'];
//        $BaseInfo = $app['eccube.repository.base_info']->get();
//
//        // Doctrine SQLFilter
//        if ($BaseInfo->getNostockHidden() === Constant::ENABLED) {
//            $app['orm.em']->getFilters()->enable('nostock_hidden');
//        }
//
//        // handleRequestは空のqueryの場合は無視するため
//        if ($request->getMethod() === 'GET') {
//            $request->query->set('pageno', $request->query->get('pageno', ''));
//        }
//
//        // searchForm
//        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
//        $builder = $app['form.factory']->createNamedBuilder('', 'search_product');
//        $builder->setAttribute('freeze', true);
//        $builder->setAttribute('freeze_display_text', false);
//        if ($request->getMethod() === 'GET') {
//            $builder->setMethod('GET');
//        }
//        /* @var $searchForm \Symfony\Component\Form\FormInterface */
//        $searchForm = $builder->getForm();
//        $searchForm->handleRequest($request);
//
//        // paginator
//        $searchData = $searchForm->getData();
//        $qb = $app['eccube.repository.product']->getQueryBuilderBySearchData($searchData);
//        $pagination = $app['paginator']()->paginate(
//            $qb,
//            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
//            $searchData['disp_number']->getId(),
//            array('wrap-queries' => true)
//        );
//
//        return $pagination;
//    }
}
