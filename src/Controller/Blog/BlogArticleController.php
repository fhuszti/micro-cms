<?php
namespace MicroCMS\Controller\Blog;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MicroCMS\Domain\Comment;
use MicroCMS\Domain\Flag;
use MicroCMS\Form\Type\CommentType;

class BlogArticleController {
    private function editFormsManager($comments, $app, $emptyComment) {
        $forms = array();

        foreach ($comments as $key => $comment) {
            $name = 'editForm-'.$key;
            $emptyComment = $comment;
            $forms[$key] = $app['form.factory']->createNamed($name, CommentType::class, $comment);
        }

        return $forms;
    }

    private function commentFormsManager($comments, $app, $emptyComment) {
        $forms = array();

        foreach ($comments as $key => $comment) {
            $name = 'commentForm-'.$key;
            $forms[$key] = $app['form.factory']->createNamed($name, CommentType::class, $emptyComment);
        }

        return $forms;
    }

    private function createFormViews($forms) {
        $views = array();

        foreach ($forms as $key => $form) {
            $views[$key] = $form->createView();
        }

        return $views;
    }

    /**
     * Article details controller.
     *
     * @param integer $id Article id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function articleAction($id, Request $request, Application $app) {
        $article = $app['dao.article']->find($id);

        $mainFormView = null;
        $commentFormViews = null;
        $user = null;
        $flags = array();
        // A user is fully authenticated : he can add and flag comments
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $app['user'];

            //we fill the flags array with the comments the user might have already flagged
            $flags = $app['dao.flag']->findAllByUser($user->getId());

            //Generate the potential new comment
            $comment = new Comment();
            $comment->setArticle($article);
            $comment->setAuthor($user);

            //Get all comments for the article
            $articleComments = $app['dao.comment']->findAllByArticle($id);

            //Generate the main comment form for the article
            $mainForm = $app['form.factory']->create(CommentType::class, $comment);

            //Generate all forms needed to respond to comments
            $commentForms = $this->commentFormsManager($articleComments, $app, $comment);

            //Generate all forms needed to edit comments
            $editForms = $this->editFormsManager($articleComments, $app, $comment);

            //Manage forms submission
            if ($request->isMethod('POST')) {
                //Main form submission
                $mainForm->submit($request->request->get($mainForm->getName()), false);
                if ($request->request->has($mainForm->getName())) {
                    if ($mainForm->isValid()) {
                        //This is a root level comment
                        $comment->setLevel(0);

                        $app['dao.comment']->save($comment);
                        $app['session']->getFlashBag()->add('success', 'Votre commentaire a été ajouté avec succès.');
                    }
                    else
                        $app['session']->getFlashBag()->add('error', 'Votre commentaire ne peut être vide.');
                }

                //Comment forms submission
                foreach ($commentForms as $key => $form) {
                    $form->submit($request->request->get($form->getName()), false);
                    if ($request->request->has($form->getName())) {
                        //we get the ID of the parent to the new comment
                        $parentId = explode('-', $form->getName())[1];

                        //if it's not from the main form, then it's a comment related to another one
                        //we set its parent_id attribute with the id in the form name
                        $comment->setParentId($parentId);

                        //We find the new comment's parent level in the comment tree
                        //then we increment it and set it
                        $parentLevel = $app['dao.comment']->find($parentId)->getLevel();
                        if ($parentLevel >= 3) {
                            $app['session']->getFlashBag()->add('error', 'Vous ne pouvez pas répondre à un commentaire de niveau 3.');
                        }
                        else {
                            if ($form->isValid()) {
                                $comment->setLevel($parentLevel + 1);

                                $app['dao.comment']->save($comment);
                                $app['session']->getFlashBag()->add('success', 'Votre commentaire a été ajouté avec succès.');
                            }
                            else
                                $app['session']->getFlashBag()->add('error', 'Votre commentaire ne peut être vide.');
                        }
                    }
                }
            }

            //Adding form view to the page
            //mainFormView == null is user not logged in
            $mainFormView = $mainForm->createView();
            $commentFormViews = empty($commentForms) ? null : $this->createFormViews($commentForms);
            $editFormViews = empty($editForms) ? null : $this->createFormViews($editForms);
        }

        //fetch all comments in two separate arrays
        //depending on it being a first comment or a children comment
        $parents = $app['dao.comment']->findAllParentsByArticle($id);
        $children = $app['dao.comment']->findAllChildrenByArticle($id);

        return $app['twig']->render('article.html.twig', array(
            'user' => $user,
            'article' => $article,
            'parents' => $parents,
            'children' => $children,
            'flags' => $flags,
            'mainForm' => $mainFormView,
            'commentForms' => $commentFormViews,
            'editForms' => $editFormViews
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
        else {
            $app['session']->getFlashBag()->add('error', 'Vous ne pouvez pas signaler les commentaires sans être connecté.');
        }

        // Redirect to home page
        return $app->redirect($app['url_generator']->generate('home'));
    }

    /**
     * Comment deleting controller.
     *
     * @param Request $request POST request sent
     * @param Application $app Silex application
     */
    public function deleteCommentAction(Request $request, Application $app) {
        // A user is fully authenticated : he can delete his own comments
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $commentId = $request->request->get('id');

            $comment = $app['dao.comment']->find($commentId);
            $user = $app['user'];

            //Is it one of the user's own comments ?
            if ($user->getId() === $comment->getAuthor()->getId()) {
                $comment->setIsDeleted(true);
                $comment->setContent("[Commentaire supprimé par son auteur]");

                //save the comment
                $app['dao.comment']->save($comment);

                //Everything after this shouldn't get called because this method should be called using AJAX and preventing its normal behavior
                //but Silex wants a return on actions, so I leave it there just so it works
                // + it's cool to have that in case the user starts getting redirected anyway
                $app['session']->getFlashBag()->add('success', 'Le commentaire a été supprimé.');
            }
            else {
                $app['session']->getFlashBag()->add('error', 'Vous ne pouvez pas supprimer les commentaires des autres.');
            }
        }
        else {
            $app['session']->getFlashBag()->add('error', 'Vous devez être connecté pour supprimer un commentaire.');
        }

        // Redirect to home page
        return $app->redirect($app['url_generator']->generate('home'));
    }

    /**
     * Comment editing controller.
     *
     * @param Request $request POST request sent
     * @param Application $app Silex application
     */
    public function editCommentAction(Request $request, Application $app) {
        // A user is fully authenticated : he can edit his own comments
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            $commentId = $request->request->get('id');
            $commentContent = $request->request->get('content');

            $comment = $app['dao.comment']->find($commentId);
            $user = $app['user'];

            //Is it one of the user's own comments ?
            if ($user->getId() === $comment->getAuthor()->getId()) {
                $comment->setContent($commentContent);

                //save the comment
                $app['dao.comment']->save($comment);

                //Everything after this shouldn't get called because this method should be called using AJAX and preventing its normal behavior
                //but Silex wants a return on actions, so I leave it there just so it works
                // + it's cool to have that in case the user starts getting redirected anyway
                $app['session']->getFlashBag()->add('success', 'Le commentaire a été modifié.');
            }
            else {
                $app['session']->getFlashBag()->add('error', 'Vous ne pouvez pas modifier les commentaires des autres.');
            }
        }
        else {
            $app['session']->getFlashBag()->add('error', 'Vous devez être connecté pour modifier un commentaire.');
        }

        // Redirect to home page
        return $app->redirect($app['url_generator']->generate('home'));
    }
}
