<?php
namespace MicroCMS\Controller\Blog;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MicroCMS\Domain\ChangePassword;
use MicroCMS\Form\Type\BasicUserDataType;
use MicroCMS\Form\Type\UserPasswordType;
use MicroCMS\Form\Type\UserDeleteType;

class BlogProfileController {
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
