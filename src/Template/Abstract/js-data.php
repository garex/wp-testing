<?php /* @var array $Wpt */ /* @var WpTesting_Component_Json $j */ ?>
<script type="text/javascript">
/* <![CDATA[ */
var Wpt = Wpt || {};
<?php foreach ($Wpt as $key => $value): ?>
<?php if (!is_array($value)): ?>
Wpt[<?php echo $j->encode($key) ?>] = <?php echo $j->encode($value) ?>;
<?php else: ?>
Wpt[<?php echo $j->encode($key) ?>] = Wpt[<?php echo $j->encode($key) ?>] || <?php if(is_string(key($value))): ?>{}<?php else: ?>[]<?php endif ?>;
<?php foreach ($value as $subKey => $subValue): ?>
Wpt[<?php echo $j->encode($key) ?>][<?php echo $j->encode($subKey) ?>] = <?php echo $j->encode($subValue) ?>;
<?php endforeach ?>
<?php endif ?>
<?php endforeach ?>
/* ]]> */
</script>