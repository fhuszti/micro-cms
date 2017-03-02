<?php
namespace MicroCMS\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserDeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('password', PasswordType::class, array('label' => 'Veuillez rentrer votre de passe pour supprimer votre compte'));
        $builder->add('modalSubmit', ButtonType::class, array(
            'label' => 'Supprimer le compte',
            'attr' => array('class' => 'btn btn-danger')
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'MicroCMS\Domain\User'
        ));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array('userDelete')
        ));
    }

    public function getName(){
        return 'userDelete';
    }
}
