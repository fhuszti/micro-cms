<?php
namespace MicroCMS\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class BlogControllerProvider implements ControllerProviderInterface {
    public function connect(Application $app) {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];




        // Home page
        $controllers->get('/', "MicroCMS\Controller\Blog\BlogMainController::indexAction")->bind('home');

        //Posts list page
        $controllers->get('/chapitres', "MicroCMS\Controller\Blog\BlogMainController::postsListAction")->bind('chapters');

        //About page
        $controllers->get('/apropos', "MicroCMS\Controller\Blog\BlogMainController::aboutAction")->bind('about');

        //Login page
        $controllers->match('/connexion', "MicroCMS\Controller\Blog\BlogMainController::loginAction")->bind('login');




        //Individual article pages with comments
        //match() for POST+GET
        $controllers->match('/chapitre/{id}', "MicroCMS\Controller\Blog\BlogArticleController::articleAction")->bind('article');

        //Flag a comment in the database
        $controllers->match('/chapitre/commentaire/flag', "MicroCMS\Controller\Blog\BlogArticleController::commentFlagAction")->bind('user_comment_flag');

        //Delete a comment (via user)
        $controllers->match('/chapitre/commentaire/supprimer', "MicroCMS\Controller\Blog\BlogArticleController::deleteCommentAction")->bind('user_comment_delete');




        //Register page
        $controllers->match('/inscription', "MicroCMS\Controller\Blog\BlogRegisterController::registerAction")->bind('register');




        //Profile page
        $controllers->match('/profil/{id}', "MicroCMS\Controller\Blog\BlogProfileController::profileAction")->bind('profile');




        return $controllers;
    }
}
