<div class="misc-pub-section wpt-publish-on-home misc-pub-wpt-publish-on-home misc-pub-section-last">
    <label>
        <input type="hidden"   value="0" name="wpt_publish_on_home" />
        <input type="checkbox" value="1" name="wpt_publish_on_home"
            <?php echo ($isPublishOnHome) ? 'checked="checked"' : '' ?> />
        <?php echo __('Publish on the home page', 'wp-testing') ?>
    </label>
</div>