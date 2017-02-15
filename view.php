<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link href="style.css" rel="stylesheet" />
        <title>MicroCMS - Home</title>
    </head>
    <body>
        <header>
            <h1>MicroCMS</h1>
        </header>

        <?php foreach ($articles as $article): ?>
            <article>
                <h2><?php echo $article['art_title']; ?></h2>

                <h4>Ajouté le <time datetime="<?php echo $article['art_date']; ?>" pubdate="pubdate"><?php echo $datePosted; ?></time></h4>
                <?php if ($article['art_last_modif'] != NULL): ?>
                    <h4>Dernière modification le <time datetime="<?php echo $article['art_last_modif']; ?>"><?php echo $dateLastModif; ?></time></h4>
                <?php endif; ?>

                <p>
                    <?php echo $article['art_content']; ?>
                </p>
            </article>
        <?php endforeach; ?>

        <footer class="footer">
            <a href="https://github.com/fhuszti/micro-cms">MicroCMS</a> is a minimalistic CMS built as a showcase for modern PHP development.
        </footer>
    </body>
</html>
