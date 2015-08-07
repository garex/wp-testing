<?php
// Can be overriden in your theme as entry-content-wpt-test-fill-form.php

/* @var $answerIdName string */
/* @var $answerIndex integer */
/* @var $isShowContent boolean */
/* @var $formClasses string */
/* @var $content string */
/* @var $subTitle string */
/* @var $shortDescription string */
/* @var $test WpTesting_Model_Test */
/* @var $questions WpTesting_Model_Question[] */
/* @var $isFinal boolean */
/* @var $isMultipleAnswers boolean */
/* @var $submitButtonCaption string */
/* @var $stepsCounter string */
/* @var $wp WpTesting_WordPressFacade */
/* @var $hiddens array */
?>
<div class="wpt_test fill_form">

<?php if ($isShowContent): ?>
<div class="content">
    <?php echo $content ?>
</div>
<?php endif ?>

<div class="content"><form method="post" id="wpt-test-form" class="<?php echo $formClasses ?>">
<?php if ($subTitle): ?><h2 class="subtitle"><?php echo $subTitle ?></h2><?php endif ?>
<?php if ($shortDescription): ?><div class="short-description"><?php echo $wp->autoParagraphise($shortDescription) ?></div><?php endif ?>
<?php $wp->doAction('wp_testing_template_fill_form_questions_before') ?>
<?php foreach($questions as $q => $question): /* @var $question WpTesting_Model_Question */ ?>
    <?php $wp->doAction('wp_testing_template_fill_form_question_before', $question, $q) ?>
    <div class="question">

        <div class="title">
            <span class="number"><?php echo $q+1 ?>.</span><span class="title"><?php echo $question->getTitle() ?>
            <?php $wp->doAction('wp_testing_template_fill_form_label_end', array('required' => true)) ?></span>
        <?php if (!$isMultipleAnswers): ?>
            <input type="hidden" name="<?php echo $answerIdName ?>[<?php echo $answerIndex ?>]" value="" />
        <?php endif ?>
        </div>

    <?php foreach($question->buildAnswers() as $a => $answer): /* @var $answer WpTesting_Model_Answer */ ?>
        <?php $answerId = 'wpt-test-question-' . $question->getId() . '-answer-' . $answer->getId() ?>

        <div class="answer">

            <label for="<?php echo $answerId ?>">
                <input type="<?php echo $isMultipleAnswers ? 'checkbox' : 'radio' ?>" id="<?php echo $answerId ?>"
                    data-errormessage="<?php echo $isMultipleAnswers
                        ? __('Please select at least one answer.', 'wp-testing')
                        : __('Please choose only one answer.', 'wp-testing') ?>"
                    <?php if (0 == $a): ?>required="required" aria-required="true"<?php endif ?>
                    name="<?php echo $answerIdName ?>[<?php echo $answerIndex ?>]" value="<?php echo $answer->getId() ?>" />
                <?php echo $answer->getTitleOnce() ?>
            </label>

        </div>
        <?php if ($isMultipleAnswers) {$answerIndex++;} ?>
    <?php endforeach ?>

    </div>
    <?php $wp->doAction('wp_testing_template_fill_form_question_after', $question, $q) ?>
    <?php if (!$isMultipleAnswers) {$answerIndex++;} ?>
<?php endforeach ?>
<?php $wp->doAction('wp_testing_template_fill_form_questions_after') ?>

<?php if($isFinal): ?>
    <p>
        <input type="submit" class="button" value="<?php echo $submitButtonCaption ?>" />
        <?php if($stepsCounter): ?><span class="steps-counter"><?php echo $stepsCounter ?></span><?php endif ?>
    </p>
<?php else: ?>
    <div class="wpt_warning">
        <h4><?php echo __('Test is under construction', 'wp-testing') ?></h4>
        <p><?php echo __('You can not get any results from it yet.', 'wp-testing') ?></p>
    </div>
<?php endif ?>
<?php foreach($hiddens as $name => $value): ?><input type="hidden" name="<?php echo htmlspecialchars($name) ?>" value="<?php echo htmlspecialchars($value) ?>" /><?php endforeach ?>
</form></div>

</div>
