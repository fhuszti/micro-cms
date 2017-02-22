<?php
namespace MicroCMS\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class BasicUserDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', TextType::class, array('label' => 'Pseudo'));
        $builder->add('email', EmailType::class, array('label' => 'Adresse email'));
    }

    public function getName(){
        return 'basicData';
    }
}
