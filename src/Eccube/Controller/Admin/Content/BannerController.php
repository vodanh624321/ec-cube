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

namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Banner;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

/**
 * 新着情報のコントローラクラス
 */
class BannerController extends AbstractController
{
    /**
     * 新着情報一覧を表示する。
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $type = $request->get('type', Banner::BANNER);
        $banners = $app['eccube.repository.banner']->findBy(array('type' => $type), array('id' => 'ASC'));
        $builder = $app['form.factory']->createBuilder('admin_content_banner');
        $form = $builder->getForm();
        $images = array();
        $links = array();
        $big = array();
        $target = array();
        /** @var Banner $banner */
        foreach ($banners as $banner) {
            $images[] = $banner->getFileName();
            $links[] = $banner->getLink();
            $big[] = $banner->getBig();
            $target[] = (bool)$banner->getTarget();
        }
        $form['images']->setData($images);
        $form['links']->setData($links);
        $form['type']->setData($type);
        $form['big']->setData($big);
        $form['target']->setData($target);
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $add_images = $form->get('add_images')->getData();
                $old_images = $form->get('images')->getData();
                $links = $form->get('links')->getData();
                $bigs = $form->get('big')->getData();
                $targets = $form->get('target')->getData();
                $cnt = 1;
                foreach ($add_images as $key => $add_image) {
                    $Banner = new \Eccube\Entity\Banner();
                    $Banner
                        ->setFileName($add_image)
                        ->setRank($cnt)
                        ->setType($type)
                        ->setLink($links[$key]);
                    if (isset($targets[$key])) {
                        $Banner->setTarget($targets[$key]);
                    }
                    if ($type == Banner::BANNER) {
                        $Banner->setBig($bigs[$key]);
                    }
                    $cnt++;
                    $app['orm.em']->persist($Banner);

                    $file = new File($app['config']['image_temp_realdir'].'/'.$add_image);
                    $file->move($app['config']['image_save_realdir']);
                }

                $delete_images = $form->get('delete_images')->getData();
                foreach ($delete_images as $delete_image) {
                    $Banner = $app['eccube.repository.banner']->findOneBy(array('file_name' => $delete_image));
                    if ($Banner instanceof \Eccube\Entity\Banner) {
                        $app['orm.em']->remove($Banner);

                    }
                    if (!empty($delete_image) && file_exists($app['config']['image_save_realdir'].'/'.$delete_image)) {
                        $fs = new Filesystem();
                        $fs->remove($app['config']['image_save_realdir'].'/'.$delete_image);
                    }
                }

                if (!empty($old_images)) {
                    foreach ($old_images as $key => $old_image) {
                        /** @var Banner $Banner */
                        $Banner = $app['eccube.repository.banner']->findOneBy(array('file_name' => $old_image));
                        if ($Banner) {
                            $Banner->setLink($links[$key]);
                            if (isset($targets[$key])) {
                                $Banner->setTarget($targets[$key]);
                            }
                            if ($type == Banner::BANNER) {
                                $Banner->setBig($bigs[$key]);
                            }
                            $app['orm.em']->persist($Banner);
                        }
                    }
                }
                $app['orm.em']->flush();
                $app->addSuccess("admin.content.banner.success", 'admin');

                return $app->redirect($app->url('admin_content_banner', ['type' => $type]));
            }
        }

        return $app->render('Content/banner.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function addImage(Application $app, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('リクエストが不正です');
        }

        $images = $request->files->get('admin_content_banner');

        $files = array();
        if (count($images) > 0) {
            foreach ($images as $img) {
                foreach ($img as $image) {
                    $mimeType = $image->getMimeType();
                    if (0 !== strpos($mimeType, 'image')) {
                        throw new UnsupportedMediaTypeHttpException('ファイル形式が不正です');
                    }

                    $extension = $image->getClientOriginalExtension();
                    $filename = date('mdHis').uniqid('_').'.'.$extension;
                    $image->move($app['config']['image_temp_realdir'], $filename);
                    $files[] = $filename;
                }
            }
        }

        return $app->json(array('files' => $files), 200);
    }
}