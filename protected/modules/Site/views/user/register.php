<?php
/* @var $this \Site\controllers\UserController */
/* @var $Model \Site\models\User\Register */
/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */

//$this->breadcrumbs=array(
//	$this->module->id,
//);
?>
<div id="divRegisterForm" class="form">
	<?
	$form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'Register',
		'method' => 'POST',
		'enableClientValidation' => true,
		'enableAjaxValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
		'focus' => array($Model, 'txtEmail'),
	));
	/* @var $form CActiveForm */
	?>
	<table class="FullW">
		<tr>
			<td style="width: 350px">
				<?=
				html::FieldContainer(
						\html::activeComboBox($Model, $form, 'ddlAccountType', $Model->arrAccountTypes, array('rel' => \html::AjaxElement('#divRegisterForm')))
						, $form->labelEx($Model, 'ddlAccountType')
						, $form->error($Model, 'ddlAccountType'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtEmail')
						, $form->labelEx($Model, 'txtEmail')
						, $form->error($Model, 'txtEmail', array(), true))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtEmailRepeat')
						, $form->labelEx($Model, 'txtEmailRepeat')
						, $form->error($Model, 'txtEmailRepeat'))
				?>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtUsername')
						, $form->labelEx($Model, 'txtUsername')
						, $form->error($Model, 'txtUsername', array(), true))
				?>
				<?=
				html::FieldContainer(
						$form->passwordField($Model, 'txtPassword')
						, $form->labelEx($Model, 'txtPassword')
						, $form->error($Model, 'txtPassword'))
				?>
				<?
				switch ($Model->ddlAccountType) {
					case $Model::UserType_Company:
						echo html::FieldContainer(
								$form->textField($Model, 'txtCompanyURL')
								, $form->labelEx($Model, 'txtCompanyURL')
								, $form->error($Model, 'txtCompanyURL')) . '<div id="divDomainMsg"></div>';
						?>
						<script>
							function CompanyEmailDomainCheck() {
								var $txtEmail = $('#Register_txtEmail'), $txtURL = $('#Register_txtCompanyURL')
										, Email = $txtEmail.val(), URL = $txtURL.val(), Domain = Email ? Email.split('@')[1] : null
								if (Email && URL && Domain && (URL.indexOf('http://') > -1 || URL.indexOf('https://') > -1)) {
									$('#divDomainMsg').html(
											!URL.find2find_substr('://').find2find_substr('', '/').match(new RegExp('<?= trim(Consts\Regexp::CompanyURLDomain("' + $.ui.autocomplete.escapeRegex(Domain) + '"), '/') ?>', "i"))
											? '<?= addslashes(Lng::Site('tr_company', "The url domain doesn't not matched to your email domain")) ?>'
											: '')
								}
							}
							$('#Register_txtCompanyURL').keyup(CompanyEmailDomainCheck)
							$('#Register_txtEmail').keyup(CompanyEmailDomainCheck)
						</script>
						<?
						$wdgGeoLocation->ActiveForm = $form;
						echo $wdgGeoLocation;
						break;
					case $Model::UserType_Artist:
						echo html::FieldContainer(
								$form->textField($Model, 'txtInvitationCode', array('autocomplete' => 'off'))
								, $form->labelEx($Model, 'txtInvitationCode')
								, $form->error($Model, 'txtInvitationCode'));
						break;
				}
				?>
				<div><? $form->widget('CCaptcha') ?></div>
				<?=
				html::FieldContainer(
						$form->textField($Model, 'txtCaptcha', array('autocomplete' => 'off'))
						, $form->labelEx($Model, 'txtCaptcha')
						, $form->error($Model, 'txtCaptcha'))
				?>
				<?=
				html::ButtonContainer(
						CHtml::submitButton(\Lng::Site('tr_user', 'Register')
								, array(
							'name' => 'btnRegister',
							'rel' => \html::AjaxElement('#divRegisterForm')
								)
				))
				?>
			</td>
			<td class="BtmAlign">
				<?= $form->errorSummary($Model) ?>
			</td>
		</tr>
	</table>
	<? $this->endWidget(); ?>
</div>
