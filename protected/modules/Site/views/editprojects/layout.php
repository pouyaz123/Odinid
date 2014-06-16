<?php
/* @var $this Site\controllers\ProfileController */
use Site\Consts\Routes;
?>
<div id="divPrjEdit" class="form">
	<div>
		<?= \CHtml::link(t2::site_site('Add Project'), Routes::User_EditPrj()) ?>
		| <?= \CHtml::link(t2::site_site('Add Blog'), Routes::User_EditBlog()) ?>
		| <?= \CHtml::link(t2::site_site('Add Tutorial'), Routes::User_EditTut()) ?>
	</div>
	<?= $content; ?>
</div>