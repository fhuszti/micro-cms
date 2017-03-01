<?php
namespace MicroCMS\Domain;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class User implements AdvancedUserInterface, \Serializable {
    /**
     * User id.
     *
     * @var integer
     */
    private $id;

    /**
     * User name.
     *
     * @var string
     */
    private $username;

    /**
     * User email.
     *
     * @var string
     */
    private $email;

    /**
     * User password.
     *
     * @var string
     */
    private $password;

    /**
     * Salt that was originally used to encode the password.
     *
     * @var string
     */
    private $salt;

    /**
     * Role.
     * Values : ROLE_USER or ROLE_ADMIN.
     *
     * @var string
     */
    private $role;

    /**
     * User banned or not.
     *
     * @var boolean
     */
    private $is_active;




    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {
        return $this->salt;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
        return $this;
    }

    public function getIsActive() {
        return $this->is_active;
    }

    public function setIsActive($is_active) {
        $this->is_active = $is_active;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles() {
        return array($this->getRole());
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials() {
        // Nothing to do here
    }

    /**
     * @inheritDoc
     */
    public function isAccountNonExpired() {
        return $this->isEnabled();
    }

    /**
     * @inheritDoc
     */
    public function isAccountNonLocked() {
        return $this->isEnabled();
    }

    /**
     * @inheritDoc
     */
    public function isCredentialsNonExpired() {
        return $this->isEnabled();
    }

    /**
     * @inheritDoc
     */
    public function isEnabled() {
        return $this->is_active;
    }




    /** @see \Serializable::serialize() */
    public function serialize() {
        return serialize(array(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->salt,
            $this->role,
            $this->is_active
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized) {
        list (
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            $this->salt,
            $this->role,
            $this->is_active
        ) = unserialize($serialized);
    }

    //Check whether the username and email are unique
    private function checkUnique(ExecutionContextInterface $context) {

    }




    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        //ID
        $metadata->addPropertyConstraint('id', new Assert\Type(array(
            'type' => 'integer',
            'message' => 'L\'ID associé à l\'utilisateur doit être de type Integer ou null.'
        )));
        $metadata->addPropertyConstraint('id', new Assert\GreaterThanOrEqual(0));



        //Username
        $metadata->addPropertyConstraint('username', new Assert\NotBlank(array(
            'message' => 'Votre pseudo ne peut être vide.'
        )));
        $metadata->addPropertyConstraint('username', new Assert\Type(array(
            'type' => 'string',
            'message' => 'Votre pseudo doit être une chaîne de caractères.'
        )));
        $metadata->addPropertyConstraint('username', new Assert\Length(array(
            'min' => 2,
            'max' => 50,
            'minMessage' => 'Votre pseudo doit comporter 2 caractères au minimum.',
            'maxMessage' => 'Votre pseudo doit compoter 50 caractères au maximum.'
        )));



        //Email
        $metadata->addPropertyConstraint('email', new Assert\NotBlank(array(
            'message' => 'Votre adresse email ne peut être vide.'
        )));
        $metadata->addPropertyConstraint('email', new Assert\Email(array(
            'message' => 'Votre adresse email doit avoir un format valable.'
        )));
        $metadata->addPropertyConstraint('email', new Assert\Length(array(
            'min' => 2,
            'max' => 100,
            'minMessage' => 'Votre adresse email doit comporter 2 caractères au minimum.',
            'maxMessage' => 'Votre adresse email doit compoter 100 caractères au maximum.'
        )));



        //Password
        $metadata->addPropertyConstraint('password', new Assert\NotBlank(array(
            'message' => 'Votre mot de passe doit être renseigné.'
        )));
        $metadata->addPropertyConstraint('password', new Assert\Type(array(
            'type' => 'string',
            'message' => 'Votre mot de passe doit être une chaîne de caractères.'
        )));
        $metadata->addPropertyConstraint('password', new Assert\Length(array(
            'min' => 8,
            'max' => 120,
            'minMessage' => 'Votre pseudo doit comporter 8 caractères au minimum.',
            'maxMessage' => 'Votre pseudo doit compoter 120 caractères au maximum.'
        )));



        //Salt
        $metadata->addPropertyConstraint('salt', new Assert\NotBlank(array(
            'message' => 'Le Salt doit être généré.'
        )));
        $metadata->addPropertyConstraint('salt', new Assert\Type(array(
            'type' => 'string',
            'message' => 'Le Salt doit être une chaîne de caractères.'
        )));
        $metadata->addPropertyConstraint('salt', new Assert\Length(array(
            'min' => 1,
            'minMessage' => 'Le Salt doit comporter 1 caractère au minimum.'
        )));



        //Role
        $metadata->addPropertyConstraint('role', new Assert\NotBlank(array(
            'message' => 'Le rang doit être renseigné.'
        )));
        $metadata->addPropertyConstraint('role', new Assert\Type(array(
            'type' => 'string',
            'message' => 'Le rang doit être une chaîne de caractères.'
        )));
        $metadata->addPropertyConstraint('role', new Assert\Regex(array(
            'pattern' => '/^ROLE_/',
            'message' => 'Le rang doit commencer par "ROLE_".'
        )));



        //Is active ?
        $metadata->addPropertyConstraint('is_active', new Assert\NotBlank(array(
            'message' => 'Le statut de ban de l\'utilisateur doit être renseigné.'
        )));
        $metadata->addPropertyConstraint('is_active', new Assert\Type(array(
            'type' => 'bool',
            'message' => 'Le statut de ban de l\'utilisateur doit être un Boolean.'
        )));
    }
}
