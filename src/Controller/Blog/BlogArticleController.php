<?php
namespace MicroCMS\Controller\Blog;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MicroCMS\Domain\Comment;
use MicroCMS\Domain\Flag;
use MicroCMS\Form\Type\CommentType;

class BlogArticleController {
    /**
     * Article details controller.
     *
     * @param integer $id Article id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function articleAction($id, Request $request, Application $app) {
        $article = $app['dao.article']->find($id);

        $commentFormView = null;
        $flags = array();
        // A user is fully authenticated : he can add and flag comments
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['user'];

            //we fill the flags array with the comments the user might have already flagged
            $flags = $app['dao.flag']->findAllByUser($user->getId());

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

        //fetch all comments in two separate arrays
        //depending on it being a first comment or a children comment
        $parents = $app['dao.comment']->findAllParentsByArticle($id);
        $children = $app['dao.comment']->findAllChildrenByArticle($id);

        return $app['twig']->render('article.html.twig', array(
            'article' => $article,
            'parents' => $parents,
            'children' => $children,
            'flags' => $flags,
            'commentForm' => $commentFormView
        ));
    }

    /**
     * Comment flagging controller.
     *
     * @param Request $request POST request sent
     * @param Application $app Silex application
     */
    public function commentFlagAction(Request $request, Application $app) {
        // A user is fully authenticated : he can flag comments
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $commentId = $request->request->get('id');

            $comment = $app['dao.comment']->find($commentId);
            $user = $app['user'];
            $article = $app['dao.article']->find($comment->getArticle()->getId());

            //set up the new flag
            $flag = new Flag();
            $flag->setUser($user);
            $flag->setComment($comment);
            $flag->setArticle($article);
            $flag->setIp($_SERVER['REMOTE_ADDR']);

            //save the new flag
            $app['dao.flag']->save($flag);

            //Everything after this shouldn't get called because this method should be called using AJAX and preventing its normal behavior
            //but Silex wants a return on actions, so I leave it there just so it works
            // + it's cool to have that in case the user starts getting redirected anyway
            $app['session']->getFlashBag()->add('success', 'Le commentaire a été signalé.');
        }

        // Redirect to home page
        return $app->redirect($app['url_generator']->generate('home'));
    }
}
