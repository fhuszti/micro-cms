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
     * @var string
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

    public function setAuthor($author) {
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
}
