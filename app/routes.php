<?php
// Home page
$app->get('/', "MicroCMS\Controller\HomeController::indexAction")->bind('home');

//Individual article pages with comments
//match() for POST+GET
$app->match('/article/{id}', "MicroCMS\Controller\HomeController::articleAction")->bind('article');

//Posts list page
$app->get('/chapitres', "MicroCMS\Controller\HomeController::postsListAction")->bind('chapters');

//About page
$app->get('/apropos', "MicroCMS\Controller\HomeController::aboutAction")->bind('about');

//Login page
$app->match('/connexion', "MicroCMS\Controller\HomeController::loginAction")->bind('login');

//Register page
$app->match('/inscription', "MicroCMS\Controller\HomeController::registerAction")->bind('register');




//Administration page
$app->get('/admin', "MicroCMS\Controller\AdminController::indexAction")->bind('admin');

// Add a new article
$app->match('/admin/article/add', "MicroCMS\Controller\AdminController::addArticleAction")->bind('admin_article_add');

// Edit an existing article
$app->match('/admin/article/{id}/edit', "MicroCMS\Controller\AdminController::editArticleAction")->bind('admin_article_edit');

// Remove an article
$app->get('/admin/article/{id}/delete', "MicroCMS\Controller\AdminController::deleteArticleAction")->bind('admin_article_delete');

// Edit an existing comment
$app->match('/admin/comment/{id}/edit', "MicroCMS\Controller\AdminController::editCommentAction")->bind('admin_comment_edit');

// Remove a comment
$app->get('/admin/comment/{id}/delete', "MicroCMS\Controller\AdminController::deleteCommentAction")->bind('admin_comment_delete');

// Add a user
$app->match('/admin/user/add', "MicroCMS\Controller\AdminController::addUserAction")->bind('admin_user_add');

// Edit an existing user
$app->match('/admin/user/{id}/edit', "MicroCMS\Controller\AdminController::editUserAction")->bind('admin_user_edit');

// Ban a user
$app->get('/admin/user/{id}/ban', "MicroCMS\Controller\AdminController::banUserAction")->bind('admin_user_ban');

// Unban a user
$app->get('/admin/user/{id}/unban', "MicroCMS\Controller\AdminController::unbanUserAction")->bind('admin_user_unban');

// Remove a user
$app->get('/admin/user/{id}/delete', "MicroCMS\Controller\AdminController::deleteUserAction")->bind('admin_user_delete');
