<?php
use Symfony\Component\HttpFoundation\Request;
use MicroCMS\Domain\Comment;
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
