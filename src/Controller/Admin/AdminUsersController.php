<?php
namespace MicroCMS\Controller\Admin;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use MicroCMS\Domain\User;
use MicroCMS\Form\Type\UserType;

class AdminUsersController {
    /**
     * Encode a plain password
     *
     * @param MicroCMS\Domain\User $user User registering
     * @param Application $app Silex application
     */
    private function encodePassword(User $user, Application $app) {
        $plainPassword = $user->getPassword();

        // find the default encoder
        $encoder = $app['security.encoder.bcrypt'];

        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
    }





    /**
     * Add user controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function addUserAction(Request $request, Application $app) {
        $user = new User();

        $userForm = $app['form.factory']->create(UserType::class, $user);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // generate a random salt value
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);

            //encode the plain password
            $this->encodePassword($user, $app);

            // initialize a ban status for the user
            $user->setIsActive(true);

            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'Le membre a été créé avec succès.');
        }

        return $app['twig']->render('user_form.html.twig', array(
            'title' => 'Nouveau membre',
            'userForm' => $userForm->createView()
        ));
    }

    /**
     * Edit user controller.
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editUserAction($id, Request $request, Application $app) {
        $user = $app['dao.user']->find($id);

        $userForm = $app['form.factory']->create(UserType::class, $user);

        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            //encode the plain password
            $this->encodePassword($user, $app);

            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'Le membre a été modifié avec succès.');
        }

        return $app['twig']->render('user_form.html.twig', array(
            'title' => 'Modifier le membre',
            'userForm' => $userForm->createView()
        ));
    }

    /**
     * Ban user controller.
     *
     * @param integer $id User id
     * @param Application $app Silex application
     */
    public function banUserAction($id, Application $app) {
        // ban the user
        $app['dao.user']->ban($id);
        $app['session']->getFlashBag()->add('success', 'Le membre a été banni avec succès.');

        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
     * Unban user controller.
     *
     * @param integer $id User id
     * @param Application $app Silex application
     */
    public function unbanUserAction($id, Application $app) {
        // Unban the user
        $app['dao.user']->unban($id);
        $app['session']->getFlashBag()->add('success', 'Le membre a été réinstitué avec succès.');

        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
     * Delete user controller.
     *
     * @param integer $id User id
     * @param Application $app Silex application
     */
    public function deleteUserAction($id, Application $app) {
        // Delete the user
        $app['dao.user']->delete($id);
        $app['session']->getFlashBag()->add('success', 'Le membre a été supprimé avec succès.');

        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }
}
