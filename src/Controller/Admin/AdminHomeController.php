<?php
namespace MicroCMS\Controller\Admin;

use Silex\Application;

class AdminHomeController {
    /**
     * Admin home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
        $articles = $app['dao.article']->findAll();
        $comments = $app['dao.comment']->findAll();
        $users = $app['dao.user']->findAll();

        return $app['twig']->render('admin.html.twig', array(
            'articles' => $articles,
            'comments' => $comments,
            'users' => $users
        ));
    }
}
