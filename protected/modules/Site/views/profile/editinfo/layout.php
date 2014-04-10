<?php
/* @var $this Site\controllers\ProfileController */
?>
<div id="divEditInfo" class="form">
	<div>
		<?= \CHtml::link(t2::Site_User('Basic info'), \Site\Consts\Routes::User_EditInfo()) ?>
		| <?= \CHtml::link(t2::Site_User('Emails'), \Site\Consts\Routes::User_EditEmails()) ?>
		| <?= \CHtml::link(t2::Site_User('Phones'), \Site\Consts\Routes::User_EditContacts()) ?>
		| <?= \CHtml::link(t2::Site_User('Locations'), \Site\Consts\Routes::User_EditLocations()) ?>
		| <?= \CHtml::link(t2::Site_User('Residencies'), \Site\Consts\Routes::User_EditResidencies()) ?>
	</div>
	<?= $content; ?>
</div>