<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Projects\Projects */
/* @var $TargetJQS string */

use Site\models\Projects\Projects;
use Site\Consts\Routes;
use Tools\HTTP;
use Tools\Settings;
?>
<div id="pop_divPrjCats">
	<?= CHtml::checkBoxList('pop_chkPrjCat', '', $Model->arrCategories) ?>	
</div>
<script>
	MyDialog_SerilizeChkLstVals('#<?= $TargetJQS ?>', '#pop_divPrjCats', '<?=t2::site_site('Ok')?>', '<?=t2::site_site('Cancel')?>')
</script>
