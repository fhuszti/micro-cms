<?php
namespace MicroCMS\DAO;

use MicroCMS\Domain\Comment;

class CommentDAO extends DAO {
    /**
     * @var \MicroCMS\DAO\ArticleDAO
     */
    private $articleDAO;

    /**
     * @var \MicroCMS\DAO\UserDAO
     */
    private $userDAO;

    public function setArticleDAO(ArticleDAO $articleDAO) {
        $this->articleDAO = $articleDAO;
    }

    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }



    /**
     * Returns a comment matching the supplied id.
     *
     * @param integer $id The comment id
     *
     * @return \MicroCMS\Domain\Comment|throws an exception if no matching comment is found
     */
    public function find($id) {
        $sql = "SELECT * FROM t_comments WHERE com_id = ?";
        $row = $this->getDb()->fetchAssoc($sql, array($id));

        if ($row)
            return $this->buildDomainObject($row);
        else
            throw new \Exception("Aucun commentaire ne correspond à l'ID : " . $id);
    }

    /**
     * Returns a list of all comments, sorted by date (most recent first).
     *
     * @return array A list of all comments.
     */
    public function findAll() {
        $sql = "SELECT * FROM t_comments ORDER BY com_id DESC";
        $result = $this->getDb()->fetchAll($sql);

        //Convert query results to an array of Domain objects
        $comments = array();
        foreach ($result as $row) {
            $commentId = $row['com_id'];
            $comments[$commentId] = $this->buildDomainObject($row);
        }

        return $comments;
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
        //so the article won't be retrieved during buildDomainObject()
        $sql = "SELECT com_id, com_content, com_date, com_last_modif, com_level, usr_id, parent_id FROM t_comments WHERE art_id = ? ORDER BY com_date";
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
     * Return a list of all comments for a user, sorted by article.
     *
     * @param integer $user The user id.
     *
     * @return array A list of all comments for a user.
     */
    public function findAllByUser($userId) {
        //Find and set the associated user
        $user = $this->userDAO->find($userId);

        $sql = "SELECT  com_id, com_content, com_date, com_last_modif, com_level, art_id, parent_id FROM t_comments WHERE usr_id = ? ORDER BY art_id, com_date DESC";
        $result = $this->getDb()->fetchAll($sql, array($userId));

        //Convert the query result into an array of domain objects
        $comments = array();
        foreach ($result as $row) {
            $commentId = $row['com_id'];
            $comment = $this->buildDomainObject($row);

            //The associated user is defined for the current comment in the loop
            $comment->setAuthor($user);

            $comments[$commentId] = $comment;
        }

        return $comments;
    }

    /**
     * Return a list of all parent comments for an article, sorted by date (most recent last).
     *
     * @param integer $articleId The article id.
     *
     * @return array A list of all parent comments for the article.
     */
    public function findAllParentsByArticle($articleId) {
        //Find and set the associated article
        $article = $this->articleDAO->find($articleId);

        //art_id is not selected by the query
        //so the article won't be retrived during buildDomainObject()
        $sql = "SELECT com_id, com_content, com_date, com_last_modif, com_level, usr_id, parent_id FROM t_comments WHERE art_id = ? AND parent_id IS NULL ORDER BY com_date";
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
     * Return a list of all children comments for an article, sorted by date (most recent last).
     *
     * @param integer $articleId The article id.
     *
     * @return array A list of all children comments for the article.
     */
    public function findAllChildrenByArticle($articleId) {
        //Find and set the associated article
        $article = $this->articleDAO->find($articleId);

        //art_id is not selected by the query
        //so the article won't be retrived during buildDomainObject()
        $sql = "SELECT com_id, com_content, com_date, com_last_modif, com_level, usr_id, parent_id FROM t_comments WHERE art_id = ? AND parent_id IS NOT NULL ORDER BY com_date";
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
        $comment->setContent($row['com_content']);
        $comment->setDate($row['com_date']);
        $comment->setLastModif($row['com_last_modif']);
        $comment->setParentId($row['parent_id']);
        $comment->setLevel($row['com_level']);

        //Conditional so we build a given Article object only once in findAllByArticle()
        if (array_key_exists('art_id', $row)) {
            //Find and set the associated article
            $articleId = $row['art_id'];
            $article = $this->articleDAO->find($articleId);
            $comment->setArticle($article);
        }
        if (array_key_exists('usr_id', $row)) {
            //Find and set the associated article
            $userId = $row['usr_id'];
            $user = $this->userDAO->find($userId);
            $comment->setAuthor($user);
        }

        return $comment;
    }

    /**
     * Saves a comment into the database.
     *
     * @param \MicroCMS\Domain\Comment $comment The comment to save
     */
    public function save(Comment $comment) {
        $commentData = array(
            'art_id' => $comment->getArticle()->getId(),
            'usr_id' => $comment->getAuthor()->getId(),
            'com_content' => $comment->getContent(),
            'parent_id' => $comment->getParentId(),
            'com_level' => $comment->getLevel()
        );

        if ($comment->getId()) {
            // The comment has already been saved : update it
            $commentData['com_date'] = $comment->getDate();
            $commentData['com_last_modif'] = (new \DateTime())->format('Y-m-d H:i:s');

            $this->getDb()->update('t_comments', $commentData, array('com_id' => $comment->getId()));
        }
        else {
            // The comment has never been saved : insert it
            $commentData['com_date'] = (new \DateTime())->format('Y-m-d H:i:s');
            $commentData['com_last_modif'] = '0000-00-00 00:00:00';

            $this->getDb()->insert('t_comments', $commentData);

            // Get the id of the newly created comment and set it on the entity.
            $id = $this->getDb()->lastInsertId();
            $comment->setId($id);
        }
    }

    /**
     * Removes a comment from the database.
     *
     * @param integer $id The comment id
     */
    public function delete($id) {
        $this->getDb()->delete('t_comments', array('com_id' => $id));
    }
}
