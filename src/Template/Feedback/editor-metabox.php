<div class="wpt-avatar">
    <div class="wpt-avatar-image"></div>
</div>

<div>
    <a class="button" target="_blank" href="<?php echo $reportIssueUrl ?>"><?php echo __('Report the problem', 'wp-testing') ?></a>
</div>
<div>
    <a class="button" target="_blank" href="<?php echo $getSupportUrl ?>"><?php echo __('Get the support', 'wp-testing') ?></a>
</div>
<div>
<?php if (!$isRateUsClicked): ?>
    <a class="wpt_rateus" href="<?php echo $rateUsUrl ?>" target="_blank"><?php echo __('Add review', 'wp-testing') ?></a>
<?php else: ?>
    <?php echo __('Thank you', 'wp-testing') ?>
<?php endif ?>
</div>
