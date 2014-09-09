<ol>
<?php foreach ($tests as $test): /* @var $test WpTesting_Model_Test */ ?>
    <li><?php echo $test->getTitle() ?></li>
<?php endforeach ?>
</ol>
