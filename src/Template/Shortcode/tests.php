<?php /* @var $wp WpTesting_WordPressFacade */ ?>
<?php /* @var $cssClasses string */ ?>
<?php /* @var $tests WpTesting_Model_Test[] */ ?>
<?php /* @var $listStyle string */ ?>

<ol style="list-style-type: <?php echo $listStyle ?>;" class="wp-testing shortcode tests <?php echo $cssClasses ?>">
<?php foreach ($tests as $test): ?>
    <li><a href="<?php echo $wp->getPermalink($test->toWpPost()) ?>"><?php echo $test->getTitle() ?></a></li>
<?php endforeach ?>
</ol>
