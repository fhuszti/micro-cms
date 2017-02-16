<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

// enable the debug mode
$app['debug'] = true;

require __DIR__.'/../app/routes.php';

$app->run();







//converting dates to european format
//not doing it in the SQL request so we can display machine readable date in <time> tag
$datePosted = (new DateTime($article['art_date']))->format('d/m/Y');
$dateLastModif = (new DateTime($article['art_last_modif']))->format('d/m/Y');
