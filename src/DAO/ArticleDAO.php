<?php
namespace MicroCMS\DAO;

use MicroCMS\Domain\Article;

class ArticleDAO extends DAO {
    /**
     * Returns an article matching the supplied id.
     *
     * @param integer $id
     *
     * @return \MicroCMS\Domain\Article|throws an exception if no matching article is found
     */
    public function find($id) {
        $sql = "SELECT * FROM t_articles WHERE art_id = ?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("No article matching ID " . $id);
    }

    /**
     * Return a list of all articles, sorted by date (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "SELECT * FROM t_articles ORDER BY art_id DESC";
        $result = $this->getDb()->fetchAll($sql);

        //Convert query results to an array of Domain objects
        $articles = array();
        foreach ($result as $row) {
            $articleId = $row['art_id'];
            $articles[$articleId] = $this->buildDomainObject($row);
        }

        return $articles;
    }

    /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing Article data.
     * @return \MicroCMS\Domain\Article
     */
    protected function buildDomainObject(array $row) {
        $article = new Article();

        $article->setId($row['art_id']);
        $article->setTitle($row['art_title']);
        $article->setContent($row['art_content']);
        $article->setDate($row['art_date']);
        $article->setLastModif($row['art_last_modif']);

        return $article;
    }
}
