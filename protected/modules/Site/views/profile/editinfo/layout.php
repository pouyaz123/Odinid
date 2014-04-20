<?php
/* @var $this Site\controllers\ProfileController */
?>
<div id="divEditInfo" class="form">
	<div>
		<?= \CHtml::link(t2::site_site('Basic info'), \Site\Consts\Routes::User_EditInfo()) ?>
		| <?= \CHtml::link(t2::site_site('Avatar'), \Site\Consts\Routes::User_EditAvatar()) ?>
		| <?= \CHtml::link(t2::site_site('Availability'), \Site\Consts\Routes::User_EditInfo()."?mode=availability") ?>
		| <?= \CHtml::link(t2::site_site('Setting'), \Site\Consts\Routes::User_Setting()) ?>
	</div>
	<div>
		<?= \CHtml::link(t2::site_site('Emails'), \Site\Consts\Routes::User_EditEmails()) ?>
		| <?= \CHtml::link(t2::site_site('Web addresses'), \Site\Consts\Routes::User_EditWebAddresses()) ?>
		| <?= \CHtml::link(t2::site_site('Phones'), \Site\Consts\Routes::User_EditContacts()) ?>
		| <?= \CHtml::link(t2::site_site('Locations'), \Site\Consts\Routes::User_EditLocations()) ?>
		| <?= \CHtml::link(t2::site_site('Work permissions'), \Site\Consts\Routes::User_EditResidencies()) ?>
	</div>
	<div>
		<?= \CHtml::link(t2::site_site('Skills'), \Site\Consts\Routes::User_EditSkills()) ?>
		| <?= \CHtml::link(t2::site_site('Softwares'), \Site\Consts\Routes::User_EditSoftwares()) ?>
		| <?= \CHtml::link(t2::site_site('Languages'), \Site\Consts\Routes::User_EditLanguages()) ?>
		| <?= \CHtml::link(t2::site_site('Experiences'), \Site\Consts\Routes::User_EditExperiences()) ?>
		| <?= \CHtml::link(t2::site_site('Work fields'), \Site\Consts\Routes::User_EditWorkFields()) ?>
	</div>
	<?= $content; ?>
</div>