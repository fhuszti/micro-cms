<?php
namespace MicroCMS\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class BlogControllerProvider implements ControllerProviderInterface {
    public function connect(Application $app) {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        // Home page
        $controllers->get('/', "MicroCMS\Controller\BlogController::indexAction")->bind('home');

        //Individual article pages with comments
        //match() for POST+GET
        $controllers->match('/chapitre/{id}', "MicroCMS\Controller\BlogController::articleAction")->bind('article');

        //Flag a comment in the database
        $controllers->match('/chapitre/commentaire/flag', "MicroCMS\Controller\BlogController::commentFlagAction")->bind('user_comment_flag');

        //Posts list page
        $controllers->get('/chapitres', "MicroCMS\Controller\BlogController::postsListAction")->bind('chapters');

        //About page
        $controllers->get('/apropos', "MicroCMS\Controller\BlogController::aboutAction")->bind('about');

        //Login page
        $controllers->match('/connexion', "MicroCMS\Controller\BlogController::loginAction")->bind('login');

        //Register page
        $controllers->match('/inscription', "MicroCMS\Controller\BlogController::registerAction")->bind('register');

        //Profile page
        $controllers->match('/profil/{id}', "MicroCMS\Controller\BlogController::profileAction")->bind('profile');

        return $controllers;
    }
}
