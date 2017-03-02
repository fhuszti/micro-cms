<?php
namespace MicroCMS\Domain;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword {
    protected $oldPassword;

    protected $newPassword;




    public function getOldPassword() {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword) {
        $this->oldPassword = $oldPassword;
        return $this;
    }

    public function getNewPassword() {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword) {
        $this->newPassword = $newPassword;
        return $this;
    }




    public static function loadValidatorMetadata(ClassMetadata $metadata) {
        //Old password
        $metadata->addPropertyConstraint('oldPassword', new Assert\NotBlank(array(
            'message' => 'Votre mot de passe actuel doit être renseigné.'
        )));
        $metadata->addPropertyConstraint('oldPassword', new Assert\Type(array(
            'type' => 'string',
            'message' => 'Votre mot de passe actuel doit être une chaîne de caractères.'
        )));
        $metadata->addPropertyConstraint('oldPassword', new Assert\Length(array(
            'min' => 8,
            'max' => 120,
            'minMessage' => 'Votre mot de passe actuel doit comporter 8 caractères au minimum.',
            'maxMessage' => 'Votre mot de passe actuel doit compoter 120 caractères au maximum.'
        )));



        //New password
        $metadata->addPropertyConstraint('newPassword', new Assert\NotBlank(array(
            'message' => 'Votre nouveau mot de passe doit être renseigné.'
        )));
        $metadata->addPropertyConstraint('newPassword', new Assert\Type(array(
            'type' => 'string',
            'message' => 'Votre nouveau mot de passe doit être une chaîne de caractères.'
        )));
        $metadata->addPropertyConstraint('newPassword', new Assert\Length(array(
            'min' => 8,
            'max' => 120,
            'minMessage' => 'Votre nouveau mot de passe doit comporter 8 caractères au minimum.',
            'maxMessage' => 'Votre nouveau mot de passe doit compoter 120 caractères au maximum.'
        )));
    }
}
