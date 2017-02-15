<?php
//Data access
require 'model.php';

//fetching the articles
$articles = getArticles();

//converting dates to european format
//not doing it in the SQL request so we can display machine readable date in <time> tag
$datePosted = (new DateTime($article['art_date']))->format('d/m/Y');
$dateLastModif = (new DateTime($article['art_last_modif']))->format('d/m/Y');

//Data display
require 'view.php';
