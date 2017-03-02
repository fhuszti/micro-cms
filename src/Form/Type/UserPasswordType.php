<?php
namespace MicroCMS\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('oldPassword', PasswordType::class, array('label' => 'Mot de passe actuel'));
        $builder->add('newPassword', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'invalid_message' => 'Les contenus des deux champs de mot de passe doivent être identiques.',
                'options'         => array('required' => true),
                'first_options'   => array('label' => 'Nouveau mot de passe'),
                'second_options'  => array('label' => 'Vérification du mot de passe'),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'MicroCMS\Domain\ChangePassword',
        ));
    }

    public function getName(){
        return 'userPassword';
    }
}
