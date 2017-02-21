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
            throw new \Exception("Aucun article ne correspond à l'ID : " . $id);
    }

    /**
     * Return a list of the last 5 articles, sorted by id (most recent first).
     *
     * @return array A list of all articles.
     */
    public function findLasts() {
        $sql = "SELECT * FROM t_articles ORDER BY art_id DESC LIMIT 0, 4";
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
     * Return a list of all articles, sorted by id.
     *
     * @return array A list of all articles.
     */
    public function findAll() {
        $sql = "SELECT * FROM t_articles ORDER BY art_id";
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

    /**
     * Saves an article into the database.
     *
     * @param \MicroCMS\Domain\Article $article The article to save
     */
    public function save(Article $article) {
        $articleData = array(
            'art_title' => $article->getTitle(),
            'art_content' => $article->getContent()
        );

        if ($article->getId()) {
            // The article has already been saved : update it
            $articleData['art_date'] = $article->getDate();
            $articleData['art_last_modif'] = (new \DateTime())->format('Y-m-d');

            $this->getDb()->update('t_articles', $articleData, array('art_id' => $article->getId()));
        }
        else {
            // The article has never been saved : insert it
            $articleData['art_date'] = (new \DateTime())->format('Y-m-d');
            $articleData['art_last_modif'] = '0000-00-00';

            $this->getDb()->insert('t_articles', $articleData);

            // Get the id of the newly created article and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $article->setId($id);
        }
    }

    /**
    * Removes an article from the database.
    *
    * @param integer $id The article id.
    */
    public function delete($id) {
        $this->getDb()->delete('t_articles', array('art_id' => $id));
    }
}
