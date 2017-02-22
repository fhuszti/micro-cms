<?php
namespace MicroCMS\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', TextType::class, array('label' => 'Mot de passe actuel'));
        $builder->add('password', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'invalid_message' => 'Le contenu des deux champs de mot de passe doivent être identiques.',
                'options'         => array('required' => true),
                'first_options'   => array('label' => 'Nouveau mot de passe'),
                'second_options'  => array('label' => 'Vérification du mot de passe'),
        ));
    }

    public function getName(){
        return 'userPassword';
    }
}
