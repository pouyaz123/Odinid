<?php
/* @var $this \Site\controllers\ProfileController */
/* @var $Model \Site\models\Profile\Softwares */
?>
<? $this->beginContent('Site.views.profile.editinfo.layout') ?>
<div id="divEditSoftwares">
	<?
	if ($form = $this->beginWidget('Widgets\ActiveForm', array(
		'id' => 'ProfileInfo',
		'method' => 'POST',
		'enableClientValidation' => true,
		'clientOptions' => array(
			'validateOnSubmit' => true,
		),
			))):
		/* @var $form Widgets\ActiveForm */
		?>
		<table class="FullW">
			<tr>
				<td style="width: 350px">
					<? $ValueOf_txtSoftware = ''; ?>
					<div id="divSoftwareRates" style="display: none">
						<? foreach ($Model->dtSoftwares as $idx => $dr): ?>
							<?=
							\CHtml::dropDownList("UserSoftwares[ddlRate][$idx]", $dr['SelfRate'], $Model->arrRates, array('id' => ''))
							?>
							<? $ValueOf_txtSoftware.=',' . $dr['Software'] ?>
						<? endforeach; ?>
					</div>
					<?=
					html::FieldContainer(
							$form->textField($Model, 'txtSoftwares')
							, $form->labelEx($Model, 'txtSoftwares')
							, $form->error($Model, 'txtSoftwares'))
					?>
					<div><?= t2::Site_User('TagsHelp') ?></div>
					<script>
_t.RunScriptAfterLoad('tagit/tag-it.min', function() {
	_t.RunScriptAfterLoad('balloon/jquery.balloon.min', function() {
		_t.RunScriptAfterLoad('MyJuiAutoComplete/MyAutoComplete', function() {
			var $obj = $('#UserSoftwares_txtSoftwares')
			function TagStartup() {
				var $tags = $obj.next('ul')
				$tags.find('a').attr('rel', 'AjaxExcept')
				$tags.find('li:has(span.tagit-label)').each(function(idx, elm) {
					var $slct = $('#divSoftwareRates select:eq(' + idx + ')').attr('TagLabel', $(elm).find('span.tagit-label').html())
					$slct.attr('name', $slct.attr('name').split('][')[0] + '][' + idx + ']')
					if (!$slct.attr('TagItClicked')) {
						$slct.attr('TagItClicked', 1)
						var $slctcln = $slct.clone()
						$slct.get(0).TheCloneJQ = $slctcln
						$slctcln.get(0).TheSlctJQ = $slct
						$(elm).balloon({contents: $slctcln, classname: 'Balloons'})
						$slctcln.attr({size: 4, rel: 'BalloonFormItems'}).get(0).LIElement = elm
					}
				})
			}
			$obj.tagit({
				allowSpaces: true
				, autocomplete: MyAutoComplete(
						$obj, {
							source: '<?= Tools\HTTP::URL_InsertGetParams($_SERVER['REQUEST_URI'], "__AjaxPostKW=AutoComplete_UserSoftwares_txtSoftwares") ?>'
						}, 0, 1, 1, 1)
				, afterTagAdded: function(e, ui) {
					if (!ui.duringInitialization) {
						$('#divSoftwareRates').append(
								$('<?= addslashes(str_replace("\n", '\\n', \CHtml::dropDownList('UserSoftwares[ddlRate][]', null, $Model->arrRates, array('id' => '')))) ?>')
								.attr('TagLabel', ui.tagLabel))
						TagStartup()
					}
				}
				, afterTagRemoved: function(e, ui) {
					var $slct = $('#divSoftwareRates select[TagLabel="' + ui.tagLabel + '"]')
							, TheClnJQ = $slct.get(0).TheCloneJQ
					if (TheClnJQ)
						TheClnJQ.parent().remove()
					$slct.remove()
					delete TheClnJQ, $slct
					TagStartup()
				}
			})
			$('body').delegate('[rel*=BalloonFormItems]', {
				change: function() {
					$('#divSoftwareRates [name="' + $(this).attr('name') + '"]').attr('value', $(this).attr('value'))
				}, click: function() {
					$(this.LIElement).hideBalloon()
				}
			})
			TagStartup()
		})
	})
})
					</script>

					<?=
					html::ButtonContainer(
							CHtml::submitButton(\t2::Site_User('Edit')
									, array(
								'name' => 'btnSaveEdit',
								'rel' => \html::AjaxElement('#divEditInfo') . ' ' . \html::OnceClick,
								'onclick' => "$('.Balloons').remove()"
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
</div>
<? $this->endContent(); ?>
