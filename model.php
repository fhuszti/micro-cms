<?php
//Return all articles
function getArticles() {
    $db = new PDO('mysql:host=localhost;dbname=microcms;charset=utf8', 'root', 'root');
    $articles = $db->query('SELECT * FROM t_article ORDER BY art_id DESC');

    return $articles;
}
