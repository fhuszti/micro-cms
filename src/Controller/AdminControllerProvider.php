<?php
namespace MicroCMS\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class AdminControllerProvider implements ControllerProviderInterface {
    public function connect(Application $app) {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];




        //Administration page
        $controllers->get('/', "MicroCMS\Controller\Admin\AdminHomeController::indexAction")->bind('admin');




        // Add a new article
        $controllers->match('/article/add', "MicroCMS\Controller\Admin\AdminArticlesController::addArticleAction")->bind('admin_article_add');

        // Edit an existing article
        $controllers->match('/article/{id}/edit', "MicroCMS\Controller\Admin\AdminArticlesController::editArticleAction")->bind('admin_article_edit');

        // Remove an article
        $controllers->get('/article/{id}/delete', "MicroCMS\Controller\Admin\AdminArticlesController::deleteArticleAction")->bind('admin_article_delete');




        // Edit an existing comment
        $controllers->match('/comment/{id}/edit', "MicroCMS\Controller\Admin\AdminCommentsController::editCommentAction")->bind('admin_comment_edit');

        // Remove a comment
        $controllers->get('/comment/{id}/delete', "MicroCMS\Controller\Admin\AdminCommentsController::deleteCommentAction")->bind('admin_comment_delete');




        // Add a user
        $controllers->match('/user/add', "MicroCMS\Controller\Admin\AdminUsersController::addUserAction")->bind('admin_user_add');

        // Edit an existing user
        $controllers->match('/user/{id}/edit', "MicroCMS\Controller\Admin\AdminUsersController::editUserAction")->bind('admin_user_edit');

        // Ban a user
        $controllers->get('/user/{id}/ban', "MicroCMS\Controller\Admin\AdminUsersController::banUserAction")->bind('admin_user_ban');

        // Unban a user
        $controllers->get('/user/{id}/unban', "MicroCMS\Controller\Admin\AdminUsersController::unbanUserAction")->bind('admin_user_unban');

        // Remove a user
        $controllers->get('/user/{id}/delete', "MicroCMS\Controller\Admin\AdminUsersController::deleteUserAction")->bind('admin_user_delete');




        return $controllers;
    }
}
