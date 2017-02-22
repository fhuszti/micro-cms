<?php
namespace MicroCMS\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', TextType::class, array('label' => 'Pseudo'));
        $builder->add('password', PasswordType::class, array('label' => 'Mot de passe'));
    }

    public function getName(){
        return 'userDelete';
    }
}
