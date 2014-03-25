<?php
/* @var $this Site\controllers\ProfileController */
?>
<div id="divEditInfo" class="form">
	<div>
		<?= \CHtml::link(t2::Site_User('Basic info'), \Site\Consts\Routes::UserEditInfo()) ?>
		| <?= \CHtml::link(t2::Site_User('Emails'), \Site\Consts\Routes::UserEditEmails()) ?>
		| <?= \CHtml::link(t2::Site_User('Phones'), \Site\Consts\Routes::UserEditContacts()) ?>
	</div>
	<?= $content; ?>
</div>