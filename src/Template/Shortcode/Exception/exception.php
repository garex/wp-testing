<?php $message = preg_replace('|https?://[^\s]+|', '<a href="\0">\0</a>', $message) ?>
<pre class="error-message <?php echo $class ?>"><ins><?php echo $name ?>: <?php echo $message ?></ins></pre>
