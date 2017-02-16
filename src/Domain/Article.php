<?php
namespace MicroCMS\Domain;

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
    private $date_modif;

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
        return $this->date_modif;
    }

    public function setLastModif($date_modif) {
        $this->date_modif = $date_modif;
        return $this;
    }

    public function getFormatDate() {
        return (new \DateTime($this->date))->format('d/m/Y');
    }

    public function getFormatLastModif() {
        return (new \DateTime($this->date_modif))->format('d/m/Y');
    }
}
