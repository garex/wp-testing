<?php
// Can be overriden in your theme as entry-content-wpt-test-fill-form.php

/* @var $answerIdName string */
/* @var $content string */
/* @var $test WpTesting_Model_Test */
/* @var $questions WpTesting_Model_Question[] */
/* @var $isFinal boolean */
/* @var $isMultipleAnswers boolean */
/* @var $submitButtonCaption string */
?>
<div class="wpt_test fill_form">

<div class="content">
    <?php echo $content ?>
</div>

<div class="content"><form method="post" id="wpt-test-form">

<?php foreach($questions as $q => $question): /* @var $question WpTesting_Model_Question */ ?>
    <?php $answerIndex = ($isMultipleAnswers) ? '' : $q ?>
    <div class="question">

        <div class="title">
            <span class="number"><?php echo $q+1 ?>.</span><span class="title"><?php echo $question->getTitle() ?></span>
        <?php if (!$isMultipleAnswers): ?>
            <input type="hidden" name="<?php echo $answerIdName ?>[<?php echo $answerIndex ?>]" value="" />
        <?php endif ?>
        </div>

    <?php foreach($question->buildAnswers() as $answer): /* @var $answer WpTesting_Model_Answer */ ?>
        <?php $answerId = 'wpt-test-question-' . $question->getId() . '-answer-' . $answer->getId() ?>

        <div class="answer">

            <label for="<?php echo $answerId ?>">
                <input type="<?php echo $isMultipleAnswers ? 'checkbox' : 'radio' ?>" id="<?php echo $answerId ?>"
                    name="<?php echo $answerIdName ?>[<?php echo $answerIndex ?>]" value="<?php echo $answer->getId() ?>" />
                <?php echo $answer->getTitle() ?>
            </label>

        </div>

    <?php endforeach ?>

    </div>

<?php endforeach ?>

<?php if($isFinal): ?>
    <p><input type="submit" class="button" value="<?php echo $submitButtonCaption ?>" /></p>
<?php else: ?>
    <div class="wpt_warning">
        <h4><?php echo __('Test is under construction', 'wp-testing') ?></h4>
        <p><?php echo __('You can not get any results from it yet.', 'wp-testing') ?></p>
    </div>
<?php endif ?>

</form></div>

</div>
