<?php
/* @var $this \Site\controllers\UserController */
/* @var $Model \Site\models\User\Register */
/* @var $wdgGeoLocation \Widgets\GeoLocationFields\GeoLocationFields */

use Site\models\User\Register as Model;

//$this->breadcrumbs=array(
//	$this->module->id,
//);
?>
<div id="divRegisterForm" class="form">
	<? $this->beginContent('Site.views.user.layout') ?>
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'Register',
		'method' => 'POST',
		'enableClientValidation' => true,
		'enableAjaxValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
		'focus' => array($Model, 'txtEmail'),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<?=
					html::FieldContainer(
							\html::activeComboBox($Model, $form, 'ddlAccountType', $Model->arrAccountTypes
									, array(
								'rel' => \html::AjaxElement('#divRegisterForm') . ' ' . html::Combobox_NoSearchRel
									)
							)
							, $form->labelEx($Model, 'ddlAccountType')
							, $form->error($Model, 'ddlAccountType'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtEmail', array('autocomplete' => 'off'))
							, $form->labelEx($Model, 'txtEmail')
							, $form->error($Model, 'txtEmail', array(), true))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtEmailRepeat', array('autocomplete' => 'off'))
							, $form->labelEx($Model, 'txtEmailRepeat')
							, $form->error($Model, 'txtEmailRepeat'))
					?>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtUsername', array('autocomplete' => 'off'))
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
						case Model::UserAccountType_Company:
							echo html::FieldContainer(
									$form->textField($Model, 'txtCompanyURL', array('autocomplete' => 'off'))
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
												? '<?= addslashes(t2::Site('tr_company', "The url's domain doesn't match to your email domain")) ?>'
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
						case Model::UserAccountType_Artist:
							echo html::FieldContainer(
									$form->textField($Model, 'txtInvitationCode', array('autocomplete' => 'off'))
									, $form->labelEx($Model, 'txtInvitationCode')
									, $form->error($Model, 'txtInvitationCode'));
							break;
					}
					?>
					<?=
					html::CaptchaFieldContainer(
							html::CaptchaImage($form)
							, $form->textField($Model, 'txtCaptcha', array('autocomplete' => 'off'))
							, $form->labelEx($Model, 'txtCaptcha')
							, $form->error($Model, 'txtCaptcha'))
					?>
					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::Site_User('Register')
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
	<? endif; ?>
	<? $this->endWidget(); ?>
	<? $this->endContent(); ?>
</div>
