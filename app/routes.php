<?php
use Symfony\Component\HttpFoundation\Request;
use MicroCMS\Domain\Article;
use MicroCMS\Domain\Comment;
use MicroCMS\Form\Type\ArticleType;
use MicroCMS\Form\Type\CommentType;




// Home page
$app->get('/', function () use ($app) {
    $articles = $app['dao.article']->findAll();

    return $app['twig']->render('index.html.twig', array('articles' => $articles));
})->bind('home');




//Individual article pages with comments
//match() for POST+GET
$app->match('/article/{id}', function($id, Request $request) use ($app) {
    $article = $app['dao.article']->find($id);

    $commentFormView = null;
    // A user is fully authenticated : he can add comments
    if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
        $user = $app['user'];

        //Generate the comment
        $comment = new Comment();
        $comment->setArticle($article);
        $comment->setAuthor($user);

        //Generate the form going with the comment
        $commentForm = $app['form.factory']->create(CommentType::class, $comment);
        //Manage form submission
        $commentForm->handleRequest($request);
        if($commentForm->isSubmitted() && $commentForm->isValid()) {
            $app['dao.comment']->save($comment);
            $app['session']->getFlashBag()->add('success', 'Votre commentaire a été ajouté avec succès.');
        }

        //Addind form view to the page
        //variable == null is user not logged in
        $commentFormView = $commentForm->createView();
    }

    $comments = $app['dao.comment']->findAllByArticle($id);

    return $app['twig']->render('article.html.twig', array(
        'article' => $article,
        'comments' => $comments,
        'commentForm' => $commentFormView
    ));
})->bind('article');




//Login page
$app->get('/login', function(Request $request) use ($app) {
    return $app['twig']->render('login.html.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username')
    ));
})->bind('login');




//Administration page
$app->get('/admin', function() use ($app) {
    $articles = $app['dao.article']->findAll();
    $comments = $app['dao.comment']->findAll();
    $users = $app['dao.user']->findAll();

    return $app['twig']->render('admin.html.twig', array(
        'articles' => $articles,
        'comments' => $comments,
        'users' => $users
    ));
})->bind('admin');

// Add a new article
$app->match('/admin/article/add', function(Request $request) use ($app) {
    $article = new Article();

    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
    $articleForm->handleRequest($request);
    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'L\'article a été créé avec succès.');
    }

    return $app['twig']->render('article_form.html.twig', array(
        'title' => 'Créer un article',
        'articleForm' => $articleForm->createView()));
})->bind('admin_article_add');

// Edit an existing article
$app->match('/admin/article/{id}/edit', function($id, Request $request) use ($app) {
    $article = $app['dao.article']->find($id);

    $articleForm = $app['form.factory']->create(ArticleType::class, $article);
    $articleForm->handleRequest($request);
    if ($articleForm->isSubmitted() && $articleForm->isValid()) {
        $app['dao.article']->save($article);
        $app['session']->getFlashBag()->add('success', 'L\'article a été modifié avec succès.');
    }

    return $app['twig']->render('article_form.html.twig', array(
        'title' => 'Modifier l\'article',
        'articleForm' => $articleForm->createView()));
})->bind('admin_article_edit');

// Remove an article
$app->get('/admin/article/{id}/delete', function($id, Request $request) use ($app) {
    // Delete the article
    $app['dao.article']->delete($id);
    $app['session']->getFlashBag()->add('success', 'L\'article a été supprimé avec succès.');

    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_article_delete');
