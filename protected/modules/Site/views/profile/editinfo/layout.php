<?php
/* @var $this Site\controllers\ProfileController */
?>
<div id="divEditInfo" class="form">
	<div>
		<?= \CHtml::link(t2::site_site('Basic info'), \Site\Consts\Routes::User_EditInfo()) ?>
		| <?= \CHtml::link(t2::site_site('Avatar'), \Site\Consts\Routes::User_EditAvatar()) ?>
		| <?= \CHtml::link(t2::site_site('Availability'), \Site\Consts\Routes::User_EditInfo() . "?mode=availability") ?>
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
		| <?= \CHtml::link(t2::site_site('Softwares'), \Site\Consts\Routes::User_EditTools()) ?>
		| <?= \CHtml::link(t2::site_site('Languages'), \Site\Consts\Routes::User_EditLanguages()) ?>
		| <?= \CHtml::link(t2::site_site('Work fields'), \Site\Consts\Routes::User_EditWorkFields()) ?>
	</div>
	<div>
		<?= \CHtml::link(t2::site_site('Educations'), \Site\Consts\Routes::User_EditEducations()) ?>
		| <?= \CHtml::link(t2::site_site('Certificates'), \Site\Consts\Routes::User_EditCertificates()) ?>
		| <?= \CHtml::link(t2::site_site('Awards'), \Site\Consts\Routes::User_EditAwards()) ?>
		| <?= \CHtml::link(t2::site_site('Experiences'), \Site\Consts\Routes::User_EditExperiences()) ?>
		| <?= \CHtml::link(t2::site_site('Additionals'), \Site\Consts\Routes::User_EditAdditionals()) ?>
	</div>
		<?= \CHtml::link(t2::site_site('Project Categories'), \Site\Consts\Routes::User_EditPrjCat()) ?>
		| <?= \CHtml::link(t2::site_site('Blog Categories'), \Site\Consts\Routes::User_EditBlogCat()) ?>
		| <?= \CHtml::link(t2::site_site('Tutorial Categories'), \Site\Consts\Routes::User_EditTutCat()) ?>
	<div>
	</div>
	<?= $content; ?>
</div>