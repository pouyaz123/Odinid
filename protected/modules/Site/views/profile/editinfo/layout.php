<?php
/* @var $this Site\controllers\ProfileController */
?>
<div id="divEditInfo" class="form">
	<div>
		<?= \CHtml::link(t2::Site_User('Edit basic info'), \Site\Consts\Routes::UserEditInfo()) ?>
		| <?= \CHtml::link(t2::Site_User('Edit contact info'), \Site\Consts\Routes::UserEditContacts()) ?>
	</div>
	<?= $content; ?>
</div>