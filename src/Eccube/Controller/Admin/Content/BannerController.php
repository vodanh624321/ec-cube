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
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $banners = $app['eccube.repository.banner']->findBy(array(), array('id' => 'ASC'));

        $builder = $app['form.factory']
            ->createBuilder('admin_content_banner');

        $form = $builder->getForm();
        $images = array();
        foreach ($banners as $banner) {
            $images[] = $banner->getFileName();
        }
        $form['images']->setData($images);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $add_images = $form->get('add_images')->getData();
                $cnt = 1;
                foreach ($add_images as $add_image) {
                    $Banner = new \Eccube\Entity\Banner();
                    $Banner
                        ->setFileName($add_image)
                        ->setRank($cnt);
                    $cnt++;
                    $app['orm.em']->persist($Banner);

                    $file = new File($app['config']['image_temp_realdir'].'/'.$add_image);
                    $file->move($app['config']['image_save_realdir']);
                }

                $delete_images = $form->get('delete_images')->getData();
                foreach ($delete_images as $delete_image) {
                    $Banner = $app['eccube.repository.banner']
                        ->findOneBy(array('file_name' => $delete_image));
                    if ($Banner instanceof \Eccube\Entity\Banner) {
                        $app['orm.em']->remove($Banner);

                    }
                    if (!empty($delete_image) && file_exists($app['config']['image_save_realdir'].'/'.$delete_image)) {
                        $fs = new Filesystem();
                        $fs->remove($app['config']['image_save_realdir'].'/'.$delete_image);
                    }
                }
                $app['orm.em']->flush();
                $app->addSuccess("admin.content.banner.success", 'admin');

                return $app->redirect($app->url('admin_content_banner'));
            }
        }

        return $app->render('Content/banner.twig', array(
            'form' => $form->createView(),
            'banners' => $banners,
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