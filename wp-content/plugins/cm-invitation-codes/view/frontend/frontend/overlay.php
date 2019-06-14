<?php

use com\cminds\registration\model\Labels;

?>
<div class="cmreg-overlay">
	<div class="cmreg-overlay-inner">
		<span class="cmreg-overlay-close" title="<?php echo esc_attr(Labels::getLocalized('close')); ?>">&times;</span>
		<?php echo $content; ?>
	</div>
</div>