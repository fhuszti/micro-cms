<?php
namespace MicroCMS\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MicroCMS\Domain\Comment;
use MicroCMS\Domain\User;
use MicroCMS\Form\Type\CommentType;
use MicroCMS\Form\Type\UserType;
use MicroCMS\Form\Type\LoginType;
use MicroCMS\Form\Type\BasicUserDataType;
use MicroCMS\Form\Type\UserPasswordType;

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
        $comments = $app['dao.comment']->findAllByUser($id);
        $articles = array();
        foreach ($comments as $comment) {
            $articleId = $comment->getArticle()->getId();
            $articles[$articleId] = $app['dao.article']->find($articleId);
        }

        $basicUserDataForm = $app['form.factory']->createNamed('basicUserDataForm', BasicUserDataType::class, $user);
        $userPasswordForm = $app['form.factory']->createNamed('userPasswordForm', UserPasswordType::class, $user);

        if ($request->isMethod('POST')) {
            $basicUserDataForm->submit($request->request->get($basicUserDataForm->getName()), false);
            if ($basicUserDataForm->isSubmitted() && $basicUserDataForm->isValid()) {
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

            /*$userPasswordForm->submit($request->request->get($userPasswordForm->getName()), false);
            if ($userPasswordForm->isSubmitted() && $userPasswordForm->isValid()) {
                $plainPassword = $user->getPassword();

                // find the encoder for the user
                $encoder = $app['security.encoder_factory']->getEncoder($user);

                // compute the encoded password
                $password = $encoder->encodePassword($plainPassword, $user->getSalt());
                $user->setPassword($password);

                $app['dao.user']->save($user);
                $app['session']->getFlashBag()->add('success', 'Votre mot de passe a bien été mis à jour.');
            }*/
        }

        return $app['twig']->render('profile.html.twig', array(
            'title' => 'Profil',
            'basicUserDataForm' => $basicUserDataForm->createView(),
            'articles' => $articles,
            'comments' => $comments
        ));
    }
}
