<?php
namespace MicroCMS\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MicroCMS\Domain\Comment;
use MicroCMS\Domain\User;
use MicroCMS\Domain\Flag;
use MicroCMS\Domain\ChangePassword;
use MicroCMS\Form\Type\CommentType;
use MicroCMS\Form\Type\UserType;
use MicroCMS\Form\Type\LoginType;
use MicroCMS\Form\Type\BasicUserDataType;
use MicroCMS\Form\Type\UserPasswordType;
use MicroCMS\Form\Type\UserDeleteType;

class HomeController {
    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
        $articles = $app['dao.article']->findLasts();

        return $app['twig']->render('index.html.twig', array('articles' => $articles));
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

            //set up the new flag
            $flag = new Flag();
            $flag->setUser($user);
            $flag->setComment($comment);
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

    /**
     * Posts list controller.
     *
     * @param Application $app Silex application
     */
    public function postsListAction(Application $app) {
        $articles = $app['dao.article']->findAll();

        return $app['twig']->render('postsList.html.twig', array('articles' => $articles));
    }

    /**
     * About page controller.
     *
     * @param Application $app Silex application
     */
    public function aboutAction(Application $app) {
        return $app['twig']->render('about.html.twig');
    }

    /**
     * Register user controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function registerAction(Request $request, Application $app) {
        $user = new User();

        $userForm = $app['form.factory']->create(UserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // generate a random salt value
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);

            $plainPassword = $user->getPassword();

            // find the default encoder
            $encoder = $app['security.encoder.bcrypt'];

            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password);

            // initialize a role for the user
            $user->setRole('ROLE_MEMBER');

            // initialize a ban status for the user
            $user->setIsActive(true);

            $app['dao.user']->save($user);

            // log the user in
            $token = new UsernamePasswordToken($user, null, 'secured', array('ROLE_USER'));
            $app['security.token_storage']->setToken($token);
            $app['session']->set('_security_main', serialize($token));

            // flash message to thank the user + home page redirect
            $app['session']->getFlashBag()->add('success', 'Merci, vous êtes désormais inscrit.');
            return $app->redirect($app['url_generator']->generate('home'));
        }

        return $app['twig']->render('register.html.twig', array(
            'title' => 'Rejoignez-nous',
            'userForm' => $userForm->createView()
        ));
    }

    /**
     * User login controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error' => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username')
        ));
    }

    /**
     * Profile page controller.
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function profileAction($id, Request $request, Application $app) {
        $user = $app['dao.user']->find($id);
        $changePasswordModel = new ChangePassword();
        // Allow to display all messages by user, sorted by article
        $comments = $app['dao.comment']->findAllByUser($id);
        $articles = array();
        foreach ($comments as $comment) {
            $articleId = $comment->getArticle()->getId();
            $articles[$articleId] = $app['dao.article']->find($articleId);
        }

        // Managing all necessary forms
        $basicUserDataForm = $app['form.factory']->createNamed('basicUserDataForm', BasicUserDataType::class, $user);
        $userPasswordForm = $app['form.factory']->createNamed('userPasswordForm', UserPasswordType::class, $changePasswordModel);
        $userDeleteForm = $app['form.factory']->createNamed('userDelete', UserDeleteType::class, $user);

        if ($request->isMethod('POST')) {
            //Managing the basic user data form submission
            $basicUserDataForm->submit($request->request->get($basicUserDataForm->getName()), false);
            if ($request->request->has($basicUserDataForm->getName())) {
                //update the current user
                $app['dao.user']->save($user);

                // relog the user in
                //logged out automatically following name/email change
                $oldToken = $app['security.token_storage']->getToken();
                $newToken = new UsernamePasswordToken($user, null, $oldToken->getProviderKey(),
    $oldToken->getRoles());
                $app['security.token_storage']->setToken($newToken);
                $app['session']->set('_security_main', serialize($newToken));

                $app['session']->getFlashBag()->add('success', 'Votre profil a bien été mis à jour.');
            }

            //Managing the user password form submission
            $userPasswordForm->submit($request->request->get($userPasswordForm->getName()), false);
            if ($request->request->has($userPasswordForm->getName())) {
                $plainOldPassword = $changePasswordModel->getOldPassword();
                $plainNewPassword = $changePasswordModel->getNewPassword();

                // find the encoder for the user
                $encoder = $app['security.encoder_factory']->getEncoder($user);

                // test the entered plain password with the current encoded password
                if ($encoder->isPasswordValid($user->getPassword(), $plainOldPassword, $user->getSalt())) {
                    $newPassword = $encoder->encodePassword($plainNewPassword, $user->getSalt());

                    $user->setPassword($newPassword);
                    $app['dao.user']->save($user);

                    $app['session']->getFlashBag()->add('success', 'Votre mot de passe a bien été mis à jour.');
                }
                else {
                    $app['session']->getFlashBag()->add('error', 'Votre mot de passe actuel ne correspond pas à votre entrée.');
                }
            }

            //Managing the delete user form submission
            $userPwd = $user->getPassword();
            $userDeleteForm->submit($request->request->get($userDeleteForm->getName()), false);
            if ($request->request->has($userDeleteForm->getName())) {
                $plainPassword = $user->getPassword();

                // find the encoder for the user
                $encoder = $app['security.encoder_factory']->getEncoder($user);

                // test the entered plain password with the current encoded password
                if ($encoder->isPasswordValid($userPwd, $plainPassword, $user->getSalt())) {
                    // Delete the user
                    $app['dao.user']->delete($user->getId());
                    $app['session']->getFlashBag()->add('success', 'Votre compte a été supprimé avec succès.');

                    // Redirect to home page
                    return $app->redirect($app['url_generator']->generate('home'));
                }
                else {
                    $app['session']->getFlashBag()->add('error', 'Votre mot de passe actuel ne correspond pas à votre entrée.');
                }
            }
        }

        return $app['twig']->render('profile.html.twig', array(
            'title' => 'Profil',
            'basicUserDataForm' => $basicUserDataForm->createView(),
            'userPasswordForm' => $userPasswordForm->createView(),
            'userDeleteForm' => $userDeleteForm->createView(),
            'articles' => $articles,
            'comments' => $comments
        ));
    }
}
