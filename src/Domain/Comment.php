<?php
namespace MicroCMS\Domain;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class Comment {
    /**
     * Comment id.
     *
     * @var integer
     */
    private $id;

    /**
     * Comment author.
     *
     * @var \MicroCMS\Domain\User
     */
    private $author;

    /**
     * Comment content.
     *
     * @var integer
     */
    private $content;

    /**
     * Comment date of publication.
     *
     * @var string
     */
    private $date;

    /**
     * Comment date of last modification.
     *
     * @var string
     */
    private $last_modif;

    /**
     * Associated article.
     *
     * @var \MicroCMS\Domain\Article
     */
    private $article;

    /**
     * Id of the parent comment.
     * Default at null
     *
     * @var integer
     */
    private $parent_id;

    /**
     * Level in the comment tree
     *
     * @var integer
     */
    private $level;

    /**
     * Is the comment deleted or not
     *
     * @var boolean
     */
    private $is_deleted;




    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor(User $author) {
        $this->author = $author;
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

    public function getArticle() {
        return $this->article;
    }

    public function setArticle(Article $article) {
        $this->article = $article;
        return $this;
    }

    public function getParentId() {
        return $this->parent_id;
    }

    public function setParentId($parent_id) {
        $this->parent_id = $parent_id;
        return $this;
    }

    public function getLevel() {
        return $this->level;
    }

    public function setLevel($level) {
        $this->level = $level;
        return $this;
    }

    public function getIsDeleted() {
        return $this->is_deleted;
    }

    public function setIsDeleted($is_deleted) {
        $this->is_deleted = $is_deleted;
        return $this;
    }




    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        //ID
        $metadata->addPropertyConstraint('id', new Assert\Type(array(
            'type' => 'integer',
            'message' => 'L\'ID associé au commentaire doit être de type Integer ou null.'
        )));



        //Content
        $metadata->addPropertyConstraint('content', new Assert\NotBlank(array(
            'message' => 'Votre message ne peut être vide.'
        )));



        //Author
        $metadata->addPropertyConstraint('author', new Assert\NotBlank(array(
            'message' => 'L\'auteur du commentaire doit être renseigné.'
        )));
        $metadata->addPropertyConstraint('author', new Assert\Type(array(
            'type' => 'object',
            'message' => 'L\'auteur associé doit être de type Object.'
        )));



        //Article
        $metadata->addPropertyConstraint('article', new Assert\NotBlank(array(
            'message' => 'L\'article associé au commentaire doit être renseigné.'
        )));
        $metadata->addPropertyConstraint('article', new Assert\Type(array(
            'type' => 'object',
            'message' => 'L\'article associé doit être de type Object.'
        )));



        //Parent ID
        $metadata->addPropertyConstraint('parent_id', new Assert\Type(array(
            'type' => 'integer',
            'message' => 'L\'ID du commentaire parent doit être de type Integer ou null.'
        )));



        //Comment level
        $metadata->addPropertyConstraint('level', new Assert\Type(array(
            'type' => 'integer',
            'message' => 'Le niveau associé au commentaire doit être de type Integer.'
        )));
        $metadata->addPropertyConstraint('level', new Assert\Range(array(
            'min' => 0,
            'max' => 3,
            'minMessage' => 'Le niveau associé au commentaire doit être supérieur ou égal à 0.',
            'maxMessage' => 'Le niveau associé au commentaire doit être inférieur ou égal à 3.'
        )));



        //Is Deleted ?
        $metadata->addPropertyConstraint('is_deleted', new Assert\Type(array(
            'type' => 'bool',
            'message' => 'La valeur fournit pour décider du statut de suppression du commentaire doit être un Boolean.'
        )));
    }
}
