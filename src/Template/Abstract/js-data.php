<?php /* @var array $Wpt */ ?>
<script type="text/javascript">
/* <![CDATA[ */
var Wpt = Wpt || {};
<?php foreach ($Wpt as $key => $value): ?>
Wpt[<?php echo json_encode($key) ?>] = <?php echo json_encode($value) ?>;
<?php endforeach ?>
/* ]]> */
</script>