<?php
/* @var $this Site\controllers\ProfileController */
?>
<div id="divEditInfo" class="form">
	<div>
		<?= \CHtml::link(t2::Site_User('Basic info'), \Site\Consts\Routes::User_EditInfo()) ?>
		| <?= \CHtml::link(t2::Site_User('Emails'), \Site\Consts\Routes::User_EditEmails()) ?>
		| <?= \CHtml::link(t2::Site_User('Phones'), \Site\Consts\Routes::User_EditContacts()) ?>
		| <?= \CHtml::link(t2::Site_User('Locations'), \Site\Consts\Routes::User_EditLocations()) ?>
		| <?= \CHtml::link(t2::Site_User('Work permissions'), \Site\Consts\Routes::User_EditResidencies()) ?>
		| <?= \CHtml::link(t2::Site_User('Web addresses'), \Site\Consts\Routes::User_EditWebAddresses()) ?>
		| <?= \CHtml::link(t2::Site_User('Skills'), \Site\Consts\Routes::User_EditSkills()) ?>
		| <?= \CHtml::link(t2::Site_User('Languages'), \Site\Consts\Routes::User_EditLanguages()) ?>
		| <?= \CHtml::link(t2::Site_User('Experiences'), \Site\Consts\Routes::User_EditExperiences()) ?>
		| <?= \CHtml::link(t2::Site_User('Setting'), \Site\Consts\Routes::User_Setting()) ?>
	</div>
	<?= $content; ?>
</div>