<?php
/* @var $this Site\controllers\ProfileController */
use Site\Consts\Routes;
?>
<div id="divEditInfo" class="form">
	<div>
		<?= \CHtml::link(t2::site_site('Basic info'), Routes::User_EditInfo()) ?>
		| <?= \CHtml::link(t2::site_site('Avatar'), Routes::User_EditAvatar()) ?>
		| <?= \CHtml::link(t2::site_site('Availability'), Routes::User_EditInfo() . "?mode=availability") ?>
		| <?= \CHtml::link(t2::site_site('Setting'), Routes::User_Setting()) ?>
	</div>
	<div>
		<?= \CHtml::link(t2::site_site('Emails'), Routes::User_EditEmails()) ?>
		| <?= \CHtml::link(t2::site_site('Web addresses'), Routes::User_EditWebAddresses()) ?>
		| <?= \CHtml::link(t2::site_site('Phones'), Routes::User_EditContacts()) ?>
		| <?= \CHtml::link(t2::site_site('Locations'), Routes::User_EditLocations()) ?>
		| <?= \CHtml::link(t2::site_site('Work permissions'), Routes::User_EditResidencies()) ?>
	</div>
	<div>
		<?= \CHtml::link(t2::site_site('Skills'), Routes::User_EditSkills()) ?>
		| <?= \CHtml::link(t2::site_site('Tools'), Routes::User_EditTools()) ?>
		| <?= \CHtml::link(t2::site_site('Languages'), Routes::User_EditLanguages()) ?>
		| <?= \CHtml::link(t2::site_site('Work fields'), Routes::User_EditWorkFields()) ?>
	</div>
	<div>
		<?= \CHtml::link(t2::site_site('Educations'), Routes::User_EditEducations()) ?>
		| <?= \CHtml::link(t2::site_site('Certificates'), Routes::User_EditCertificates()) ?>
		| <?= \CHtml::link(t2::site_site('Awards'), Routes::User_EditAwards()) ?>
		| <?= \CHtml::link(t2::site_site('Experiences'), Routes::User_EditExperiences()) ?>
		| <?= \CHtml::link(t2::site_site('Additionals'), Routes::User_EditAdditionals()) ?>
	</div>
	<div>
		<?= \CHtml::link(t2::site_site('Project Categories'), Routes::User_EditPrjCat()) ?>
		| <?= \CHtml::link(t2::site_site('Blog Categories'), Routes::User_EditBlogCat()) ?>
		| <?= \CHtml::link(t2::site_site('Tutorial Categories'), Routes::User_EditTutCat()) ?>
	</div>
	<?= $content; ?>
</div>