<?php
namespace MicroCMS\Domain;

class Flag {
    /**
     * Flag id.
     *
     * @var integer
     */
    private $id;

    /**
     * Flag date of publication.
     *
     * @var string
     */
    private $date;

    /**
     * IP of the flagging user.
     *
     * @var string
     */
    private $ip;

    /**
     * User flagging.
     *
     * @var \MicroCMS\Domain\User
     */
    private $user;

    /**
     * Flagged comment.
     *
     * @var \MicroCMS\Domain\Comment
     */
    private $comment;

    /**
     * Article of the flagged comment.
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

    public function getUser() {
        return $this->user;
    }

    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }

    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    public function getComment() {
        return $this->comment;
    }

    public function setComment(Comment $comment) {
        $this->comment = $comment;
        return $this;
    }

    public function getArticle() {
        return $this->article;
    }

    public function setArticle(Article $article) {
        $this->article = $article;
        return $this;
    }

    public function getIp() {
        return $this->ip;
    }

    public function setIp($ip) {
        $this->ip = $ip;
        return $this;
    }
}
