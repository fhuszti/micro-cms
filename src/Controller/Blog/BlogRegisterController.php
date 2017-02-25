<?php
namespace MicroCMS\Controller\Blog;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MicroCMS\Domain\User;
use MicroCMS\Form\Type\UserType;

class BlogRegisterController {
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
}
