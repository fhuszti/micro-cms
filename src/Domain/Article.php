<?php
namespace MicroCMS\Domain;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Article {
    /**
     * Article id.
     *
     * @var integer
     */
    private $id;

    /**
     * Article title.
     *
     * @var string
     */
    private $title;

    /**
     * Article content.
     *
     * @var string
     */
    private $content;

    /**
     * Article date of publication.
     *
     * @var string
     */
    private $date;

    /**
     * Article date of last modification.
     *
     * @var string
     */
    private $last_modif;




    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    public function getLastModif() {
        return $this->last_modif;
    }

    public function setLastModif($last_modif) {
        $this->last_modif = $last_modif;
        return $this;
    }




    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        //ID
        $metadata->addPropertyConstraint('id', new Assert\Type(array(
            'type' => 'integer',
            'message' => 'L\'ID associé à l\'article doit être de type Integer ou null.'
        )));



        //Title
        $metadata->addPropertyConstraint('title', new Assert\NotBlank(array(
            'message' => 'Le titre de l\'article ne peut être vide.'
        )));
        $metadata->addPropertyConstraint('title', new Assert\Type(array(
            'type' => 'string',
            'message' => 'Le titre de l\'article doit être une chaîne de caractères.'
        )));
        $metadata->addPropertyConstraint('title', new Assert\Length(array(
            'min' => 2,
            'max' => 120,
            'minMessage' => 'Le titre de l\'article doit comporter 2 caractères au minimum.',
            'maxMessage' => 'Le titre de l\'article doit compoter 120 caractères au maximum.'
        )));



        //Content
        $metadata->addPropertyConstraint('content', new Assert\NotBlank(array(
            'message' => 'Le contenu de l\'article ne peut être vide.'
        )));
        $metadata->addPropertyConstraint('content', new Assert\Type(array(
            'type' => 'string',
            'message' => 'Le contenu de l\'article doit être une chaîne de caractères.'
        )));
        $metadata->addPropertyConstraint('content', new Assert\Length(array(
            'min' => 2,
            'minMessage' => 'Le contenu de l\'article doit comporter 2 caractères au minimum.'
        )));



        //Date
        $metadata->addPropertyConstraint('date', new Assert\Date(array(
            'message' => 'La date de création de l\'article doit être un objet de type Date, ou une string valide au format YYYY-MM-DD.'
        )));



        //Last modif
        $metadata->addPropertyConstraint('last_modif', new Assert\Date(array(
            'message' => 'La date de dernière modification de l\'article doit être un objet de type Date, ou une string valide au format YYYY-MM-DD.'
        )));
    }
}
