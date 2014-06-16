<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Projects\Projects */

use Site\models\Projects\Projects;
use Site\Consts\Routes;
use Tools\HTTP;
use Tools\Settings;
?>
<? $this->beginContent('Site.views.editprojects.layout') ?>
<?
if ($form = $this->beginWidget('Widgets\ActiveForm', array(
	'id' => 'formPrj',
	'method' => 'POST',
	'enableClientValidation' => true,
	'clientOptions' => array(
		'validateOnSubmit' => true,
	),
		))):
	/* @var $form Widgets\ActiveForm */
	?>
	<?=
	$Model->scenario == 'Edit' ? \CHtml::link(t2::site_site('Add new')
					, $Model->Type == Projects::Type_Blog ?
							Routes::User_EditBlog() :
							($Model->Type == Projects::Type_Project ?
									Routes::User_EditPrj() :
									Routes::User_EditTut()
							)
			) : ''
	?>
	<table class="FullW">
		<tr>
			<td style="width: 350px">
				<?= $form->hiddenField($Model, 'hdnID') ?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtTitle')
						, $form->labelEx($Model, 'txtTitle')
						, $form->error($Model, 'txtTitle'))
				?>
				<?=
				html::FieldContainer(
						$form->textArea($Model, 'txtSmallDesc')
						, $form->labelEx($Model, 'txtSmallDesc')
						, $form->error($Model, 'txtSmallDesc'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtWorkFields')
						, $form->labelEx($Model, 'txtWorkFields')
						, $form->error($Model, 'txtWorkFields'))
				?>
				<div><?= t2::site_site('TagsHelp') ?></div>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtTools')
						, $form->labelEx($Model, 'txtTools')
						, $form->error($Model, 'txtTools'))
				?>
				<div><?= t2::site_site('TagsHelp') ?></div>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtTags')
						, $form->labelEx($Model, 'txtTags')
						, $form->error($Model, 'txtTags'))
				?>
				<div><?= t2::site_site('TagsHelp') ?></div>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtSkills')
						, $form->labelEx($Model, 'txtSkills')
						, $form->error($Model, 'txtSkills'))
				?>
				<div><?= t2::site_site('TagsHelp') ?></div>
				<script>
					_t.RunScriptAfterLoad(['tagit/tag-it.min', 'MyJuiAutoComplete/MyAutoComplete', 'tagit/ac_lib'], function() {
						tagit_ac(
								'#<?= $Model->PostName ?>_txtWorkFields', <?= Settings::GetInstance()->MaxProjectWorkfields ?>
						, '<?= HTTP::URL_InsertAjaxKW("AutoComplete_Prj_txtWorkFields") ?>')
						tagit_ac(
								'#<?= $Model->PostName ?>_txtTools', <?= Settings::GetInstance()->MaxProjectTools ?>
						, '<?= HTTP::URL_InsertAjaxKW("AutoComplete_Prj_txtTools") ?>')
						tagit_ac(
								'#<?= $Model->PostName ?>_txtTags', <?= Settings::GetInstance()->MaxProjectTags ?>
						, '<?= HTTP::URL_InsertAjaxKW("AutoComplete_Prj_txtTags") ?>')
						tagit_ac(
								'#<?= $Model->PostName ?>_txtSkills', <?= Settings::GetInstance()->MaxProjectSkills ?>
						, '<?= HTTP::URL_InsertAjaxKW("AutoComplete_Prj_txtSkills") ?>')
					})
				</script>
				<?= $form->hiddenField($Model, 'hdnSchoolIDs') ?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtSchools')
						, $form->labelEx($Model, 'txtSchools')
						, $form->error($Model, 'txtSchools'))
				?>
				<div><?= t2::site_site('TagsHelp') ?></div>
				<?= $form->hiddenField($Model, 'hdnCompanyIDs') ?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtCompanies')
						, $form->labelEx($Model, 'txtCompanies')
						, $form->error($Model, 'txtCompanies'))
				?>
				<div><?= t2::site_site('TagsHelp') ?></div>
				<script>
					_t.RunScriptAfterLoad(['tagit/tag-it.min', 'MyJuiAutoComplete/MyAutoComplete', 'tagit/ac_companies'], function() {
						tagit_ac_companies(
								'#<?= $Model->PostName ?>_txtSchools', '#<?= $Model->PostName ?>_hdnSchoolIDs', '',
								'<?= Tools\HTTP::URL_InsertAjaxKW("AutoComplete_Prj_txtSchools") ?>', <?= Settings::GetInstance()->MaxProjectSchools ?>)
						tagit_ac_companies(
								'#<?= $Model->PostName ?>_txtCompanies', '#<?= $Model->PostName ?>_hdnCompanyIDs', '',
								'<?= Tools\HTTP::URL_InsertAjaxKW("AutoComplete_Prj_txtCompanies") ?>', <?= Settings::GetInstance()->MaxProjectCompanies ?>)
					})
				</script>
				<?=
				$Model->Type == Projects::Type_Project ?
						html::FieldContainer(
								$form->checkBox($Model, 'chkIsReel')
								, $form->labelEx($Model, 'chkIsReel')
								, $form->error($Model, 'chkIsReel')) : ""
				?>
				<?=
				$Model->Type == Projects::Type_Tutorial ?
						html::FieldContainer(
								$form->checkBox($Model, 'chkPaidTutorial')
								, $form->labelEx($Model, 'chkPaidTutorial')
								, $form->error($Model, 'chkPaidTutorial')) : ""
				?>
				<?=
				html::FieldContainer(
						\html::activeComboBox($Model, $form, 'ddlStatus', $Model->arrStatuses
								, array('rel' => \html::Combobox_NoSearchRel))
						, $form->labelEx($Model, 'ddlStatus')
						, $form->error($Model, 'ddlStatus'))
				?>
				<? require_once 'prj_form_thumb.php'; ?>
				<?=
				html::FieldContainer(
						$form->checkBox($Model, 'chkVisibility')
						, $form->labelEx($Model, 'chkVisibility')
						, $form->error($Model, 'chkVisibility'))
				?>
				<?= $form->hiddenField($Model, 'hdnCatIDs') ?>
				<?=
				html::FieldContainer(
						$form->checkBox($Model, 'chkShowInHome')
						, $form->labelEx($Model, 'chkShowInHome')
						, $form->error($Model, 'chkShowInHome'))
				?>
				<?=
				html::FieldContainer(
						$form->checkBox($Model, 'chkAdult')
						, $form->labelEx($Model, 'chkAdult')
						, $form->error($Model, 'chkAdult'))
				?>
				<?=
				html::FieldContainer(
						$form->passwordField($Model, 'txtPassword')
						, $form->labelEx($Model, 'txtPassword')
						, $form->error($Model, 'txtPassword'))
				?>
				<?=
				html::FieldContainer(
						\html::activeComboBox($Model, $form, 'ddlDividerLineType', $Model->arrDividerLineTypes
								, array('rel' => \html::Combobox_NoSearchRel))
						, $form->labelEx($Model, 'ddlDividerLineType')
						, $form->error($Model, 'ddlDividerLineType'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtContentSpacing')
						, $form->labelEx($Model, 'txtContentSpacing')
						, $form->error($Model, 'txtContentSpacing'))
				?>
				<? /* 	hdnID;
				  #
				  txtTitle = 'Untitled project';
				  txtSmallDesc;
				  chkIsReel = 0;
				  chkPaidTutorial = 0;
				  ddlStatus;
				  fileThumb;
				  hdnThumbCrop;
				  chkVisibility = 1;
				  hdnCatIDs;
				  chkShowInHome = 1;
				  chkAdult = 0;
				  txtPassword;
				  ddlDividerLineType;
				  txtContentSpacing;
				  #
				  txtWorkFields;
				  txtTools;
				  txtTags;
				  txtSkills;
				  #
				  hdnSchoolIDs;
				  txtSchools;
				  #
				  hdnCompanyIDs;
				  txtCompanies; */ ?>

				<?=
				html::ButtonContainer(
						CHtml::submitButton(\t2::site_site($Model->scenario == 'Edit' ? 'Edit' : 'Add')
								, array(
							'name' => $Model->scenario == 'Edit' ? 'btnSaveEdit' : 'btnAdd',
							'rel' => \html::AjaxElement('#divPrjEdit') . ' ' . \html::OnceClick
								)
				))
				?>
				<?=
				$Model->scenario == 'Edit' ?
						html::ButtonContainer(
								CHtml::button(\t2::site_site('Delete')
										, array(
									'name' => 'btnDelete',
									'rel' => \html::AjaxElement('#divPrjEdit') . ' ' . html::OnceClick,
									'onclick' => \html::PostbackConfirm_OnClick('Are you sure?'),
										)
						)) : ''
				?>
			</td>
			<td class="BtmAlign">
				<?= $form->errorSummary($Model) ?>
			</td>
		</tr>
	</table>
<? endif; ?>
<? $this->endWidget(); ?>
</div>
<? $this->endContent(); ?>
