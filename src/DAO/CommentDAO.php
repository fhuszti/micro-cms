<?php
namespace MicroCMS\DAO;

use MicroCMS\Domain\Comment;

class CommentDAO extends DAO {
    /**
     * @var \MicroCMS\DAO\ArticleDAO
     */
    private $articleDAO;

    public function setArticleDAO(ArticleDAO $articleDAO) {
        $this->articleDAO = $articleDAO;
    }




    /**
     * Return a list of all comments for an article, sorted by date (most recent last).
     *
     * @param integer $articleId The article id.
     *
     * @return array A list of all comments for the article.
     */
    public function findAllByArticle($articleId) {
        //Find and set the associated article
        $article = $this->articleDAO->find($articleId);

        //art_id is not selected by the query
        //so the article won't be retrived during buildDomainObject()
        $sql = "SELECT com_id, com_content, com_author, com_date, com_last_modif FROM t_comments WHERE art_id = ? ORDER BY com_id";
        $result = $this->getDb()->fetchAll($sql, array($articleId));

        //Convert the query result into an array of domain objects
        $comments = array();
        foreach ($result as $row) {
            $commentId = $row['com_id'];
            $comment = $this->buildDomainObject($row);

            //The associated article is defined for the current comment in the loop
            $comment->setArticle($article);

            $comments[$commentId] = $comment;
        }

        return $comments;
    }

    /**
     * Creates a Comment object based on a DB row.
     *
     * @param array $row The DB row containing Comment data.
     * @return \MicroCMS\Domain\Comment
     */
    protected function buildDomainObject(array $row) {
        $comment = new Comment();

        $comment->setId($row['com_id']);
        $comment->setAuthor($row['com_author']);
        $comment->setContent($row['com_content']);
        $comment->setDate($row['com_date']);
        $comment->setLastModif($row['com_last_modif']);

        //Conditional so we build a given Article object only once in findAllByArticle()
        if (array_key_exists('art_id', $row)) {
            //Find and set the associated article
            $articleId = $row['art_id'];
            $article = $this->articleDAO->find($articleId);
            $comment->setArticle($article);
        }

        return $comment;
    }
}
