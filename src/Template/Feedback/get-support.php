<div class="wrap">
<h1><?php echo __('Get the support', 'wp-testing')?></h1>

<form id="get-support" method="post">
<table class="form-table"><tbody>
    <tr>
        <th><label for="title"><?php echo __('In short', 'wp-testing') ?></label></th>
        <td><input name="title" type="text" id="title" class="regular-text" placeholder="<?php echo __('Please use english language', 'wp-testing') ?>" />
    </tr>
    <tr>
        <th><label for="details"><?php echo __('Details', 'wp-testing') ?></label></th>
        <td>
            <p><textarea name="details" rows="10" cols="50" id="details" placeholder="<?php echo __('Please use english language', 'wp-testing') ?>"></textarea></p>
            <p class="description"><?php echo __('What do you want to get and why?', 'wp-testing') ?></p>
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
    <tr>
        <th><?php echo __('Priority', 'wp-testing') ?></th>
        <td>
            <label for="Asap">
                <input name="asap" id="Asap" type="checkbox" value="1" />
                <?php echo __('Get support as soon as possible', 'wp-testing') ?>
            </label>
        </td>
    </tr>
</tbody></table>

<p class="submit">
    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Next', 'wp-testing') ?>" />
</p>

</div>