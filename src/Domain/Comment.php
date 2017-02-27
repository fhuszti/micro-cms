<?php
namespace MicroCMS\Domain;

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
}
