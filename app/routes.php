<?php
$app->mount('/', new MicroCMS\Controller\BlogControllerProvider());

$app->mount('/admin', new MicroCMS\Controller\AdminControllerProvider());
