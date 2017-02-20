<?php
namespace MicroCMS\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', TextType::class, array('label' => 'Pseudo'));
        $builder->add('email', EmailType::class, array('label' => 'Adresse email'));
        $builder->add('password', RepeatedType::class, array(
                'type'            => PasswordType::class,
                'invalid_message' => 'Le contenu des deux champs de mot de passe doivent être identiques.',
                'options'         => array('required' => true),
                'first_options'   => array('label' => 'Mot de passe'),
                'second_options'  => array('label' => 'Vérification du mot de passe'),
        ));
        //"ChoiceType" = liste déroulante
        $builder->add('role', ChoiceType::class, array(
                'choices' => array('Membre' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'),
                'label' => 'Rang'
        ));
    }

    public function getName(){
        return 'user';
    }
}
