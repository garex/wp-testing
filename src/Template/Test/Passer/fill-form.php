<?php
// Can be overriden in your theme as entry-content-wpt-test-fill-form.php

/* @var $content string */
/* @var $test WpTesting_Model_Test */
/* @var $questions WpTesting_Model_Question[] */
?>
<div class="wpt_test fill_form">

<div class="content">
    <?php echo $content ?>
</div>

<form method="post" id="wpt-test-form">

<?php foreach($questions as $q => $question): /* @var $question WpTesting_Model_Question */ ?>

    <div class="question">

        <div class="title">
            <span class="number"><?php echo $q+1 ?>.</span><span class="title"><?php echo $question->getTitle() ?></span>
        </div>

    <?php foreach($question->getAnswers() as $answer): /* @var $answer WpTesting_Model_Answer */ ?>
        <?php $answerId = 'wpt-test-question-' . $question->getId() . '-answer-' . $answer->getId() ?>

        <div class="answer">

            <input type="radio" id="<?php echo $answerId ?>"
                name="question[<?php echo $question->getId() ?>]" value="<?php echo $answer->getId() ?>" />

            <label for="<?php echo $answerId ?>"><?php echo $answer->getTitle() ?></label>

        </div>

    <?php endforeach ?>

    </div>

<?php endforeach ?>

    <input type="submit" class="button" value="<?php echo 'Get Test Results' ?>" />

</form>

</div>
