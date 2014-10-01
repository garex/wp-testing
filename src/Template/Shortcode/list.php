<ol><?php /* @var $wp WpTesting_WordPressFacade */ ?>
<?php foreach ($tests as $test): /* @var $test WpTesting_Model_Test */ ?>
    <li><a href="<?php echo $wp->getPermalink($test->toWpPost()) ?>"><?php echo $test->getTitle() ?></a></li>
<?php endforeach ?>
</ol>
