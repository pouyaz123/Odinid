<?php
/* @var $this Widgets\GeoLocationFields\GeoLocationFields */
/* @var $Model Base\FormModel */

use Widgets\TagIt\TagIt;
?>
<?=
($this->txtAddress2Attr ? \html::FieldContainer(\html::activeTextField($Model, $this->ActiveForm, $this->txtAddress2Attr)
				, \html::activeLabelEx($Model, $this->ActiveForm, $this->txtAddress2Attr)
				, \html::error($Model, $this->ActiveForm, $this->txtAddress2Attr)) : '')
?>
<?= $form->hiddenField($Model, 'hdnCompanyID') ?>
<?=
html::FieldContainer(
		$form->textField($Model, 'txtCompanyTitle')
		, $form->labelEx($Model, 'txtCompanyTitle')
		, $form->error($Model, 'txtCompanyTitle'))
?>
<?=
html::FieldContainer(
		$form->textField($Model, 'txtCompanyURL')
		, $form->labelEx($Model, 'txtCompanyURL')
		, $form->error($Model, 'txtCompanyURL'))
?>
<script>
	_t.RunScriptAfterLoad('tagit/tag-it.min', function() {
		_t.RunScriptAfterLoad('MyJuiAutoComplete/MyAutoComplete', function() {
			function TagStartup($obj) {
				$($obj).next('ul').find('a').attr('rel', 'AjaxExcept')
			}
			var $obj = $('#UserExperiences_txtCompanyTitle')
					, ACOpts = MyAutoComplete(
							$obj, {
								source: '<?= Tools\HTTP::URL_InsertAjaxKW("AutoComplete_UserExperiences_txtCompanyTitle") ?>'
								, select: function(e, ui) {
									if (ui.item.label) {
										var dr = $.parseJSON($(ui.item.label).attr('rel'))
										if (dr['ID'])
											$('#UserExperiences_hdnCompanyID').attr('value', dr['ID'])
										if (dr['URL'])
											$('#UserExperiences_txtCompanyURL').tagit('createTag', dr['URL'])
									}
								}
							}, 0, 1, 1, 1)

			$obj.tagit({
				allowSpaces: true
				, autocomplete: ACOpts
				, tagLimit: 1
				, afterTagAdded: function(e, ui) {
					TagStartup($(this))
				}
				, afterTagRemoved: function() {
					$('#UserExperiences_txtCompanyURL').tagit('removeAll')
					$('#UserExperiences_hdnCompanyID').attr('value', '')
				}
			})
			TagStartup($obj)
			TagStartup($('#UserExperiences_txtCompanyURL').tagit({
				allowSpaces: true
				, tagLimit: 1
				, afterTagAdded: function(e, ui) {
					TagStartup($(this))
				}
				, afterTagRemoved: function() {
					$('#UserExperiences_hdnCompanyID').attr('value', '')
				}
			}))
		})
	})
</script>

