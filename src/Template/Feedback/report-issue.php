<div class="wrap">
<h1><?php echo __('Report the problem', 'wp-testing')?></h1>

<form id="report-issue" method="post">
<table class="form-table"><tbody>
    <tr>
        <th><?php echo __('Problem is repeated', 'wp-testing') ?></th>
        <td>
            <label for="IssueRepeatsOtherHosting">
                <input name="issue_repeats[other_hosting]" id="IssueRepeatsOtherHosting" type="checkbox" value="another hosting" />
                <?php echo __('when you use another hosting', 'wp-testing') ?>
            </label><br/>
            <label for="IssueRepeatsLocalComputer">
                <input name="issue_repeats[local_computer]" id="IssueRepeatsLocalComputer" type="checkbox" value="local computer" />
                <?php echo __('on the local computer', 'wp-testing') ?>
            </label><br/>
            <label for="IssueRepeatsDefaultTheme">
                <input name="issue_repeats[default_theme]" id="IssueRepeatsDefaultTheme" type="checkbox" value="default theme" />
                <?php echo __('with default theme', 'wp-testing') ?>
            </label><br/>
            <label for="IssueRepeatsOtherPluginsDisabled">
                <input name="issue_repeats[other_plugins_disabled]" id="IssueRepeatsOtherPluginsDisabled" type="checkbox" value="other plugins disabled" />
                <?php echo __('when all other plugins are disabled', 'wp-testing') ?>
            </label>
        </td>
    </tr>
    <tr>
        <th><label for="expected"><?php echo __('You expect', 'wp-testing') ?></label></th>
        <td><input name="expected" type="text" id="expected" class="regular-text" placeholder="<?php echo __('Please use english language', 'wp-testing') ?>" />
    </tr>
    <tr>
        <th><label for="actual"><?php echo __('Actually you get', 'wp-testing') ?></label></th>
        <td><input name="actual" type="text" id="actual" class="regular-text" placeholder="<?php echo __('Please use english language', 'wp-testing') ?>" /></td>
    </tr>
    <tr>
        <th><label for="screenshot"><?php echo __('URL of the screenshot', 'wp-testing') ?></label></th>
        <td><input name="screenshot" type="url" id="screenshot" class="regular-text" /></td>
    </tr>
    <tr>
        <th><label for="steps"><?php echo __('Steps to repeat the problem', 'wp-testing') ?></label></th>
        <td>
            <p><textarea name="steps" rows="10" cols="50" id="steps" placeholder="<?php echo __('Please use english language', 'wp-testing') ?>"></textarea></p>
        </td>
    </tr>
    <tr>
        <th><?php echo __('System information', 'wp-testing') ?></th>
        <td>
            <label for="AttachEnvironment">
                <input name="environment" id="AttachEnvironment" type="checkbox" value="1" />
                <?php echo __('Add technical details about your installation', 'wp-testing') ?>
            </label>
        </td>
    </tr>
</tbody></table>

<p class="submit">
    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Next', 'wp-testing') ?>" />
</p>
</form>

</div>