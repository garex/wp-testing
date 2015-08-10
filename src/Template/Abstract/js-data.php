<?php /* @var array $Wpt */ ?>
<script type="text/javascript">
/* <![CDATA[ */
var Wpt = Wpt || {};
<?php foreach ($Wpt as $key => $value): ?>
<?php if (!is_array($value)): ?>
Wpt[<?php echo json_encode($key) ?>] = <?php echo json_encode($value) ?>;
<?php else: ?>
Wpt[<?php echo json_encode($key) ?>] = Wpt[<?php echo json_encode($key) ?>] || <?php if(is_string(key($value))): ?>{}<?php else: ?>[]<?php endif ?>;
<?php foreach ($value as $subKey => $subValue): ?>
Wpt[<?php echo json_encode($key) ?>][<?php echo json_encode($subKey) ?>] = <?php echo json_encode($subValue) ?>;
<?php endforeach ?>
<?php endif ?>
<?php endforeach ?>
/* ]]> */
</script>