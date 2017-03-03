<?php
namespace MicroCMS\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserEditBasicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', TextType::class, array('label' => 'Pseudo'));
        $builder->add('email', EmailType::class, array('label' => 'Adresse email'));
        //"ChoiceType" = liste déroulante
        $builder->add('role', ChoiceType::class, array(
                'choices' => array('Membre' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'),
                'label' => 'Rang'
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'MicroCMS\Domain\User'
        ));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array('userEditBasic')
        ));
    }

    public function getName(){
        return 'userEditBasic';
    }
}
