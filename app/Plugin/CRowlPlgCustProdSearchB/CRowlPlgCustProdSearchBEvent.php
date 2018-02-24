<?php

/*
 * This file is part of the CRowlPlgCustProdSearchB
 *
 * Copyright (C) 2017 株式会社 C-Rowl
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CRowlPlgCustProdSearchB;

use Eccube\Application;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Eccube\Util\Str;
use Plugin\CRowlPlgCustProdSearchB\Entity\CRowlPlgCustProdSearchBCfg;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DomCrawler\Crawler;

class CRowlPlgCustProdSearchBEvent
{

    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;

        if (version_compare(\Eccube\Common\Constant::VERSION, '3.0.10', '>=')) {
            $this->PluginUrlPath = $this->app['config']['plugin_urlpath'];
        } else {
            // 3.0.9以前ではplugin_urlpathの定義がないため、固定で入力する
            $this->PluginUrlPath = '/plugin';
        }
    }

    /* Block/search_product.twig描画時処理 */
    public function onRenderBlockSearchProduct(TemplateEvent $event)
    {
        $cfg = $this->app['crowl_plg_cust_prod_search_b.repository.crowl_plg_cust_prod_search_b_cfg']->find(1);
        // レコードの読み出しができない、または価格非表示設定がOFFの場合は何もしない
        if (!$cfg || (!$cfg->getSearchOrWhere() && !$cfg->getSearchProductPrice() && !$cfg->getSearchProductTag())) {
            return;
        }
        $cc_plg_hide_price = $this->app['config']['CRowlPlgCustProdSearchB']['const']['CC_PLG_HIDE_PRICE'];
        $parameters = $event->getParameters();

        //ソースの取得
        $source = $event->getSource();
        // 改行コードを\nに変換する
        $source = Str::convertLineFeed($source);

        //JavaScriptの差し込み
        $addJs = <<<__EOL__
<script>
    var detailSwitch = function () {
         $(".cc_plg_detail_off").toggleClass('detail-bt-off');
         $(".cc_plg_detail_off").toggleClass('detail-bt-on');
         $(".cc-plg-extra-form").toggleClass('detail-off');
    }

    var ccPlgExtraFormReset = function () {
__EOL__;

        if($cfg->getSearchOrWhere()) {
            $addJs .= <<<__EOL__
        $('input[name="cc_plg_logical"][type="radio"]').val(['0']);
__EOL__;
        }
        if($cfg->getSearchProductPrice() && $cfg->getPriceRange()) {
            $addJs .= <<<__EOL__
        $('select[name="cc_plg_price_min"]').val(null);
        $('select[name="cc_plg_price_max"]').val(null);
__EOL__;
        }
        if($cfg->getSearchProductTag()) {
            $addJs .= <<<__EOL__
        $('input[name="cc_plg_tag[]"]').prop('checked', false);
__EOL__;
        }

        $addJs .= <<<__EOL__
    }

    $(function() {
        $(document).on('click', function(evt){
            if (($(".sp").css('display') == 'none') && !$(evt.target).closest('#search').length ){
                if (!$('.cc-plg-extra-form').hasClass("detail-off")) {
                    detailSwitch();
                }
            }
        });

        $("#cc_plg_detail_off, #cc_plg_detail_on").click(function () {
            detailSwitch();
        });
        var ena_logical = false;
        var ena_price_min = false;
        var ena_price_max = false;
        var ena_tag = false;
__EOL__;

        // 検索条件設定時に検索パネルを自動で表示したくない場合はコンフィグで無効にする
        if($this->app['config']['CRowlPlgCustProdSearchB']['const']['CC_PLG_RESULT_AUTO_DISP']) {

        if($cfg->getSearchOrWhere()) {
            $addJs .= <<<__EOL__
        var radioList = document.getElementsByName("cc_plg_logical");
        if (!radioList[0].checked && !radioList[1].checked) {
            radioList[0].checked = true;
        }

        if (radioList[1].checked) {
            ena_logical = true;
        }
__EOL__;
        }

        if($cfg->getSearchProductPrice() && $cfg->getPriceRange()) {
            // 未ログイン時、価格非表示設定の場合はログイン状態でしかチェックBOXを表示しない
            if (($cc_plg_hide_price && $this->app->isGranted('ROLE_USER')) || !$cc_plg_hide_price) {
                $addJs .= <<<__EOL__
        if($("#cc_plg_price_min").val() !== '') {
            ena_price_min = true;
        }

        if($("#cc_plg_price_max").val()) {
            ena_price_max = true;
        }
__EOL__;
            }
        }

        if($cfg->getSearchProductTag()) {
            $addJs .= <<<__EOL__
        if($('#cc_plg_tag :checked').length) {
            ena_tag = true;
        }
__EOL__;
        }

        }

        $addJs .= <<<__EOL__
        if(ena_logical || ena_price_min || ena_price_max || ena_tag) {
            detailSwitch();
//            if($(".sp").css('display') == 'none') {
//                setTimeout('detailSwitch()', 4000);
//            }
        }
    });
</script>
__EOL__;

        // {% block javascript %}の存在確認
        $search = '{% block javascript %}';
        if (false === strpos($source, $search)) {
            // 存在しなければmainの上に差し込む
            //$search = '{% block main %}';
            $search = <<<__EOL__
<div class="drawer_block pc header_bottom_area">
__EOL__;
            $replace = '{% block javascript %}'.$addJs.'{% endblock %}'.$search;
        } else {
            $replace = $search.$addJs;
        }
        $source = str_replace($search, $replace, $source);

        // twigコードの差し込み
        // 検索対象を設定
        $search = <<<__EOL__
                <div class="input_search clearfix">
                    {{ form_widget(form.name, {'attr': { 'placeholder' : "キーワードを入力" }} ) }}
                    <button type="submit" class="bt_search"><svg class="cb cb-search"><use xlink:href="#cb-search" /></svg></button>
                </div>
__EOL__;
        // 差し込みコードを作成
        // OR検索(詳細検索オプション)、価格帯指定(詳細検索オプション)、商品タグ(詳細検索オプション)のいずれかが有効
        if (($cfg->getSearchOrWhere() || ($cfg->getSearchProductPrice() && $cfg->getPriceRange()) || $cfg->getSearchProductTag())) {
            // 差し込みコードを作成
            // +ボタン
            $addContents = <<<__EOL__
                <div id="cc_plg_detail_off" class="cc_plg_detail_off clearfix detail-bt-off">
                    <div class="sp detail-bt-off-comment"">詳細検索条件</div>
                    <div class="bt_search"><svg class="cb cb-search"><use xlink:href="#cb-plus" /></svg></div>
                </div>
                <div id="cc_plg_detail_on" class="cc_plg_detail_off clearfix detail-bt-on">
                    <div class="bt_search cc_plg_minus"><svg class="cb cb-search"><use xlink:href="#cb-minus" /></svg></div>
                </div>
            </div>
            <div class="cc-plg-extra-form detail-off">
                <div class="cc-plg-row-spacer"><div class="cc-plg-cell-spacer"></div></div>
                <div class="cc-plg-extra-opt">
                    <div class="cc-plg-cell-spacer"></div>
                    <div class="cc-plg-extra-opt-chk">
                        <a href="javascript:void(0)" onclick="ccPlgExtraFormReset()">選択をリセット</a>
                    </div>
                </div>
__EOL__;

            // OR検索(詳細検索オプション)
            if ($cfg->getSearchOrWhere()) {
            // AND/ORラジオボタン
            $addContents .= <<<__EOL__
                <div class="cc-plg-extra-form0">
                    <div class="cc-plg-extra-form-title">{{ form.cc_plg_logical.vars.label }}：</div>{{ form_widget(form.cc_plg_logical, { attr : { class: 'cc-plg-extra-form0-logical' } }) }}
                </div>
__EOL__;
            }

            // 価格帯指定(詳細検索オプション)
            if ($cfg->getSearchProductPrice() && $cfg->getPriceRange()) {
                $cc_plg_hide_price = $this->app['config']['CRowlPlgCustProdSearchB']['const']['CC_PLG_HIDE_PRICE'];
                // 未ログイン時、価格非表示設定の場合はログイン状態でしかチェックBOXを表示しない
                if (($cc_plg_hide_price && $this->app->isGranted('ROLE_USER')) || !$cc_plg_hide_price) {
                    $addContents .= <<<__EOL__
                <div class="cc-plg-extra-form1">
                        <div class="cc-plg-extra-form-title">{{ form.cc_plg_price_min.vars.label }}：</div><div class="cc-plg-extra-form1-price">{{ form_widget(form.cc_plg_price_min) }} ～ {{ form_widget(form.cc_plg_price_max) }}</div>
                </div>
__EOL__;
                }
            }

            // 商品タグ(詳細検索オプション)
            if ($cfg->getSearchProductTag()) {
                $addContents .= <<<__EOL__
                <div class="cc-plg-extra-form2">
                    <div class="cc-plg-extra-form-title">{{ form.cc_plg_tag.vars.label }}：</div>{{ form_widget(form.cc_plg_tag, {attr: {class: 'cc-plg-extra-form2-tag'}}) }}
                </div>
__EOL__;
            }
        } else {
            $addContents = '';
        }

        $replace = $search.$addContents;
        $source = str_replace($search, $replace, $source);

        $event->setSource($source);
    }

    /* FRONT_BLOCK_SEARCH_PRODUCT_INDEX_INITIALIZEイベント処理 */
    /* 商品検索ブロック描画時 */
    public function onFrontBlockSearchProductIndexInitialize(EventArgs $event)
    {
        $cfg = $this->app['crowl_plg_cust_prod_search_b.repository.crowl_plg_cust_prod_search_b_cfg']->find(1);
        // レコードの読み出しができない、追加検索条件が未設定の場合は何もしない
        if (!$cfg || (!$cfg->getSearchOrWhere() && !$cfg->getSearchProductPrice() && !$cfg->getSearchProductTag())) {
            return;
        }
        $tmp = $cfg->getPriceRange();
        if($tmp) {
            $price_ranges = array_map('trim', explode(',', $tmp));
            // 空の要素を削除する
            $price_ranges = array_values(array_filter($price_ranges));
            // ナンバーフォーマットに変換する
            if (count($price_ranges)) {
                $tmp_arr_price = array();
                foreach($price_ranges as $tmp_price) {
                    $tmp_arr_price[] = number_format($tmp_price);
                }
                $price_ranges = $tmp_arr_price;
            }
        } else {
            $price_ranges = null;
        }

        $builder = $event->getArgument('builder');
        if($cfg->getSearchOrWhere()) {
            $builder->add('cc_plg_logical', 'choice', array(
                'label' => 'キーワード',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'choices' => array('0' => 'AND', '1' => 'OR'),
                'data' => '0',
                'empty_value' => false,
            ));
        }
        if($cfg->getSearchProductPrice() && count($price_ranges)) {
            $builder->add('cc_plg_price_min', 'choice', array(
                'label' => '価格帯(税抜)',
                'required' => false,
                'choices' => $price_ranges,
            ));
            $builder->add('cc_plg_price_max', 'choice', array(
                'label' => '価格帯(税抜)',
                'required' => false,
                'choices' => $price_ranges,
            ));
        }
        if($cfg->getSearchProductTag()) {
            $builder->add('cc_plg_tag', 'choice', array(
                'label' => 'タグ',
                 'required' => false,
                 'multiple' => true,
                 'expanded' => true,
                 'choices' => $this->app['orm.em']->getRepository('\Eccube\Entity\Master\Tag')->findAll(),
                 'data' => null,
            ));
        }
    }

    /* FRONT_PRODUCT_INDEX_SEARCHイベント処理 */
    /* 商品一覧ページ描画時 */
    public function onFrontProductIndexSearch(EventArgs $event)
    {
        $cfg = $this->app['crowl_plg_cust_prod_search_b.repository.crowl_plg_cust_prod_search_b_cfg']->find(1);
        $search_product_code = $cfg->getSearchProductCode();
        $search_description_detail = $cfg->getSearchDescriptionDetail();
        $search_description_list = $cfg->getSearchDescriptionList();
        $search_free_area = $cfg->getSearchFreeArea();
        $search_product_id = $cfg->getSearchProductId();
        $search_or_where = $cfg->getSearchOrWhere();
        $search_product_price = $cfg->getSearchProductPrice();
        $search_product_tag = $cfg->getSearchProductTag();

        $request = $event->getRequest();

        // クエリパラメータの取得
        $category_id = $request->query->get('category_id');
        $name = $request->query->get('name');
        $cc_plg_logical = $request->query->get('cc_plg_logical');
        $cc_plg_price_min = $request->query->get('cc_plg_price_min');
        $cc_plg_price_max = $request->query->get('cc_plg_price_max');
        $cc_plg_tag = $request->query->get('cc_plg_tag');

        // 追加検索条件がない場合は何もしない
        if (!$search_product_code && !$search_description_detail && !$search_description_list && !$search_free_area &&
            !$search_product_id &&
            ((!$search_or_where && !$search_product_price && !$search_product_tag) ||
             (is_null($cc_plg_logical) && (is_null($cc_plg_price_min) || ($cc_plg_price_min === "")) && (is_null($cc_plg_price_max) || ($cc_plg_price_max === "")) && is_null($cc_plg_tag)))) {
            return;
        }

        // SQLインジェクション対策として数字以外は不可（無視）とする
        // 1:AND 2:OR
        if (!is_null($cc_plg_logical) && $cc_plg_logical == 0) {
            $is_and_where = 1; // AND
        } else {
            $is_and_where = 0; // OR
        }

        // 価格帯
        /** @var CRowlPlgCustProdSearchBCfg $cfg */
        $cfg = $this->app['crowl_plg_cust_prod_search_b.repository.crowl_plg_cust_prod_search_b_cfg']->find(1);
        $tmp = $cfg->getPriceRange();
        $price_ranges = array_map('trim', explode(',', $tmp));
        $arr_price = array_values(array_filter($price_ranges));

        // price1
        $tmp_price1 = '';
        if (!is_null($cc_plg_price_min) && ctype_digit($cc_plg_price_min) &&
            array_key_exists($cc_plg_price_min, $arr_price)) {
            $tmp_price1 = $arr_price[$cc_plg_price_min];
        }
        if (!$tmp_price1) {
            $tmp_price1 = 0;
        }
        // price2
        $tmp_price2 = '';
        if (!is_null($cc_plg_price_max) && ctype_digit($cc_plg_price_max) &&
            array_key_exists($cc_plg_price_max, $arr_price)) {
            $tmp_price2 = $arr_price[$cc_plg_price_max];
        }
        if (!$tmp_price2) {
            if ($tmp_price1) {
                $tmp_price2 = PHP_INT_MAX;
            } else {
                $tmp_price2 = 0;
            }
        }
        if ($tmp_price1 > $tmp_price2) {
            $price1 = $tmp_price2;
            $price2 = $tmp_price1;
        } else {
            $price1 = $tmp_price1;
            $price2 = $tmp_price2;
        }
        // タグ
        $arr_tag = array();
        if (!is_null($cc_plg_tag) && is_array($cc_plg_tag)) {
            $arr_tag = $_GET['cc_plg_tag'];
        }

        $qb = $event->getArgument('qb');
        $searchData = $event->getArgument('searchData');

        // innerJoin('p.ProductClasses', 'pc')
        // groupBy('p')
        // を常に追加する
        // join
        $join = $qb->getDQLPart('join');
        $find = false;
        if ($join) {
            foreach($join['p'] as $table) {
                if ('p.ProductClasses' == $table->getJoin()) {
                    $find = true;
                }
            }
        }
        if (!$find) {
            $qb->innerJoin('p.ProductClasses', 'pc');
        }

        // groupBy
        $groupBy = $qb->getDQLPart('groupBy');
        $find = false;
        if ($groupBy) {
            foreach($groupBy as $parts) {
                $groups = $parts->getParts();
                foreach($groups as $group) {
                    if('p' == $group) {
                        $find = true;
                    }
                }
            }
        }
        if (!$find) {
            $qb->groupBy('p');
        }

        $where_old = $qb->getDQLPart('where');
        $where_all = $where_old->getParts();

        // andWhereは作り直し
        $qb->resetDQLPart('where');

        // name
        $tmp_name_where = '';
        $tmp_name_where_index = 0;

        // バージョンで検索文字列を切り替える
        if (version_compare(\Eccube\Common\Constant::VERSION, '3.0.13', '>=')) {
            $search_key = 'NORMALIZE(p.name) LIKE NORMALIZE(:keyword';
            $pattern = '/^NORMALIZE\(p.name\) LIKE NORMALIZE\(:(?<keyword>\w+)\)\sOR.*/';
        } else {
            $search_key = 'p.name LIKE :keyword';
            $pattern = '/^p.name LIKE :(?<keyword>\w+)\sOR.*/';
        }

        foreach ($where_all as $where) {
            if (false !== strpos($where, $search_key)) {
                // keywordxxを取得する
                preg_match($pattern, $where, $matchs);
                $keyword = $matchs['keyword'];

                $where_item = '';
                if ($search_product_id) {
                    $tmp_parameters = $qb->getParameters();
                    $tmp_param = $tmp_parameters[$tmp_name_where_index];
                    if ($tmp_param->getName() == $keyword) {
                        $tmp_keyword = $tmp_param->getValue();
                        // キーワードは%%で囲まれているため、先頭、末尾の1文字ずつをカット
                        $tmp_keyword = substr($tmp_keyword, 1, -1);
                        if (ctype_digit($tmp_keyword)) {
                            $where_item = sprintf('p.id = :%s', $keyword);
                            $tmp_param->setValue($tmp_keyword);
                            $tmp_parameters[$tmp_name_where_index] = $tmp_param;
                            $qb->setParameters($tmp_parameters);
                        }
                    }
                }

                // 設定に応じて各種条件を付加する
                // 商品コード
                if ($search_product_code) {
                    if ($where_item) {
                        if (version_compare(\Eccube\Common\Constant::VERSION, '3.0.13', '>=')) {
                            $where_item .= sprintf(' OR NORMALIZE(pc.code) LIKE NORMALIZE(:%s)', $keyword);
                        } else {
                            $where_item .= sprintf(' OR pc.code LIKE :%s', $keyword);
                        }
                    } else {
                        if (version_compare(\Eccube\Common\Constant::VERSION, '3.0.13', '>=')) {
                            $where_item .= sprintf(' NORMALIZE(pc.code) LIKE NORMALIZE(:%s)', $keyword);
                        } else {
                            $where_item .= sprintf(' pc.code LIKE :%s', $keyword);
                        }
                    }
                }
                if (!$where_item) {
                    $where_item = $where;

                    // 商品説明
                    if ($search_description_detail) {
                        if (version_compare(\Eccube\Common\Constant::VERSION, '3.0.13', '>=')) {
                            $where_item .= sprintf(' OR NORMALIZE(p.description_detail) LIKE :%s', $keyword);
                        } else {
                            $where_item .= sprintf(' OR p.description_detail LIKE :%s', $keyword);
                        }
                    }
                    // 一覧コメント
                    if ($search_description_list) {
                        if (version_compare(\Eccube\Common\Constant::VERSION, '3.0.13', '>=')) {
                            $where_item .= sprintf(' OR NORMALIZE(p.description_list) LIKE :%s', $keyword);
                        } else {
                            $where_item .= sprintf(' OR p.description_list LIKE :%s', $keyword);
                        }
                    }
                    // フリーエリア
                    if ($search_free_area) {
                        if (version_compare(\Eccube\Common\Constant::VERSION, '3.0.13', '>=')) {
                            $where_item .= sprintf(' OR NORMALIZE(p.free_area) LIKE :%s', $keyword);
                        } else {
                            $where_item .= sprintf(' OR p.free_area LIKE :%s', $keyword);
                        }
                    }
                }
                if ($is_and_where) {
                    $qb->andWhere($where_item);
                } elseif (!$tmp_name_where) {
                    $tmp_name_where = $where_item;
                } else {
                    $tmp_name_where .= ' OR '.$where_item;
                }
                $tmp_name_where_index++;
            } else {
                $qb->andWhere($where);
            }
        }

        if (!$is_and_where && $tmp_name_where) {
            $qb->andWhere($tmp_name_where);
        }

        // 価格帯検索
        if ($search_product_price) {
            if ($price1 && $price2) {
                if ($price1 == $price2) {
                    $qb->andWhere('pc.price02 = '.$price1);
                } else {
                    $qb->andWhere('pc.price02 BETWEEN '.$price1.' AND '.$price2);
                }
            } elseif ($price1) {
                $qb->andWhere('pc.price02 >= '.$price1);
            } elseif ($price2) {
                $qb->andWhere('pc.price02 <= '.$price2);
            }
        }

        // タグ検索
        if ($search_product_tag && count($arr_tag)) {
            $qb->leftJoin('p.ProductTag', 'pt');
            $tmp_tag = '';
            foreach ($arr_tag as $tag) {
                if (!ctype_digit($tag)) {
                    // SQLインジェクション対策として数字以外は不可（無視）とする
                    continue;
                }
                if ($tmp_tag) {
                    $tmp_tag .= sprintf(' OR pt.Tag = %s', $tag + 1);
                } else {
                    $tmp_tag = sprintf('pt.Tag = %s', $tag + 1);
                }
            }
            $qb->andWhere($tmp_tag);
        }
    }

    /* FRONT_PRODUCT_INDEX_INITIALIZEイベント処理 */
    /* 商品一覧ページ描画時 */
    /* 商品検索ブロックと同じ名前のformを追加する */
    public function onFrontProductIndexInitialize(EventArgs $event)
    {
        $cfg = $this->app['crowl_plg_cust_prod_search_b.repository.crowl_plg_cust_prod_search_b_cfg']->find(1);
        // レコードの読み出しができない、追加検索条件が未設定の場合は何もしない
        if (!$cfg || (!$cfg->getSearchOrWhere() && !$cfg->getSearchProductPrice() && !$cfg->getSearchProductTag())) {
            return;
        }
        $tmp = $cfg->getPriceRange();
        if($tmp) {
            $price_ranges = array_map('trim', explode(',', $tmp));
            // 空の要素を削除する
            $price_ranges = array_values(array_filter($price_ranges));
            // ナンバーフォーマットに変換する
            if (count($price_ranges)) {
                $tmp_arr_price = array();
                foreach($price_ranges as $tmp_price) {
                    $tmp_arr_price[] = number_format($tmp_price);
                }
                $price_ranges = $tmp_arr_price;
            }
        } else {
            $price_ranges = null;
        }

        $request = $event->getRequest();
        $cc_plg_tag = $request->query->get('cc_plg_tag');

        $builder = $event->getArgument('builder');
        if($cfg->getSearchOrWhere()) {
            $builder->add('cc_plg_logical', 'choice', array(
                'label' => 'キーワード',
                'required' => true,
                'expanded' => false, // checkboxではなく、セレクトタグに変更
                'multiple' => false,
                'choices' => array('0' => 'AND', '1' => 'OR'),
                'data' => '0',
                'empty_value' => false,
            ));
        }
        if($cfg->getSearchProductPrice() && count($price_ranges)) {
            $builder->add('cc_plg_price_min', 'choice', array(
                'label' => '価格帯(税抜)',
                'required' => false,
                'choices' => $price_ranges,
            ));
            $builder->add('cc_plg_price_max', 'choice', array(
                'label' => '価格帯(税抜)',
                'required' => false,
                'choices' => $price_ranges,
            ));
        }
        if ($cfg->getSearchProductTag() && $cc_plg_tag != null && count($cc_plg_tag) && $cc_plg_tag[0] !== '') {
            $builder->add('cc_plg_tag', 'choice', array(
                 'label' => 'タグ',
                 'required' => false,
                 'multiple' => true,
                 'expanded' => false, // チェックボックスではなく、セレクトタグ(multiple 属性有)に変更
                 'choices' => $this->app['orm.em']->getRepository('\Eccube\Entity\Master\Tag')->findAll(),
            ));
        }
    }

    /**
     * 全画面共通のフック
     *
     * プラグインで追加したブロックを表示しているページでは
     * 専用のcssファイルを読み込む
     * 
     * @param  FilterResponseEvent  $event
     */
    public function onRenderFrontPage(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $html = $response->getContent();
        if ($html === false) {
            return;
        }
        $crawler = new Crawler($html);

        $element = $crawler->filter('div.cc-plg-extra-form');
        // 追加ブロックがなければCSSの読み込みは不要
        if (!count($element)) {
            return;
        }

        $oldElement = $crawler->filter('head');

        /* headタグが取得できない場合は処理終了 */
        if (!count($oldElement)) {
// For Debug
//            $this->app['monolog.CRowlPlgCustProdSearchB']->INFO('The current node list is empty. route=' . $route);
            return;
        }

        //スタイルの読み込み
        $addHead = "\n".'<link rel="stylesheet" href="'.$this->PluginUrlPath.'/CRowlPlgCustProdSearchB/'.$this->app['config']['template_code'].'/css/cust_prod_search_b.css">';

        $oldHtml = $oldElement->html();
        $oldHtml = html_entity_decode($oldHtml, ENT_NOQUOTES, 'UTF-8');
        /* 生成したタグをHEADの終端に結合する */
        $newHtml = $oldHtml . $addHead;
        $html = $this->getHtml($crawler);
        $html = str_replace($oldHtml, $newHtml, $html);

        $response->setContent($html);
        $event->setResponse($response);
    }

    /**
     * 解析用HTMLを取得
     *
     * @param Crawler $crawler
     * @return string
     */
    private function getHtml(Crawler $crawler)
    {
        $html = '';
        foreach ($crawler as $domElement) {
            $domElement->ownerDocument->formatOutput = true;
            $html .= $domElement->ownerDocument->saveHTML();
        }
        return html_entity_decode($html, ENT_NOQUOTES, 'UTF-8');
    }
}
