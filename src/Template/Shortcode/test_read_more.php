<?php
/* @var $wp WpTesting_WordPressFacade */
/* @var $cssClasses string */
/* @var $title string */
/* @var $content string */
/* @var $url string */
/* @var $buttonCaption string */
?>
<div class="wp-testing shortcode test-read-more <?php echo $cssClasses ?>">

    <h2 class="title"><?php echo $title ?></h2>

    <div class="content"><?php echo $wp->applyFilters('the_content', $content) ?></div>

    <form action="<?php echo $url ?>" method="post">
        <input type="submit" class="button" value="<?php echo $buttonCaption ?>"/>
    </form>

</div>