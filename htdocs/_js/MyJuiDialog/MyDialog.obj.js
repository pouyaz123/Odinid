/**
 * use jq data "DialogOpts" to push new opts
 * @property {Array} Dialogs
 * @property {Object} DefOpts
 */
MyDialog = new function() {
	var RelKW = 'MyJuiDialog'
	this.Dialogs = []
	var $divs = this.Dialogs, level = 1, _this = this
	this.getCurrentDialog = function() {
		return $divs[level - 1]
	}
	function fncClick(e) {
		var $div = _this.create(), opts, eOpts = $(this).data('DialogOpts')
		$div.data({clickthis: this, clickevent: e})
		$div.dialog(_this.DefOpts)
		if (eOpts) {
			opts = $.merge([], _this.DefOpts)
			$.merge(opts, eOpts)
			$div.dialog('option', opts)
		}
		$div.dialog('open')
		return false
	}
	this.create = function(TriggerJQS) {
		if (!$divs[level])
			$divs[level] = $('<div></div>')
		if (TriggerJQS)
			$(TriggerJQS).click(fncClick)
		return $divs[level]
	}
	this.DefOpts = {autoOpen: false, modal: true, height: 'auto'
		, open: function(e, ui) {
			PostBack.TriggerClick($($divs[level].data('clickthis')), $divs[level].data('clickevent'), e.target, null, null, 'INSERT', 1)
			level++
		}
		, close: function(e) {
			$(e.target).html('')
			level--
			$divs[level].dialog('destroy')
		}
	}
	var constructed = 0
	function Construct() {
		_this.create()
		$('body').delegate('[rel*="' + RelKW + '"]', {click: fncClick})
		if (!constructed) {
			constructed = 1
		}
	}
	if (typeof PostBack !== 'undefined')
		Construct()
	else
		PBDocComplete.push(Construct)
}
/**
 * Gathers the value of the chk box lists like cat ids and sets them into the value attr of the target(Tgt)
 */
function MyDialog_SerilizeChkLstVals(jqsTgt, jqsChkBoxesContainer, OkBtnLabel, CancelBtnLabel) {
	MyDialog.getCurrentDialog().dialog('option', {buttons: [{text: OkBtnLabel ? OkBtnLabel : 'OK', click: function() {
					var arrIDs = new Array()
					$(jqsChkBoxesContainer + ' :checkbox:checked').each(function(idx, elm) {
						arrIDs.push($(elm).val())
					})
					$(jqsTgt).attr('value', arrIDs.join(','))
					$(this).dialog('close')
				}}, {text: CancelBtnLabel ? CancelBtnLabel : 'Cancel', click: function() {
					$(this).dialog('close')
				}}]})
	var arrIDs = $(jqsTgt).val().split(','), ID
	for (ID in arrIDs) {
		$(jqsChkBoxesContainer + ' :checkbox').attr('checked', null).filter('[value="' + arrIDs[ID] + '"]').attr('checked', 'checked')
	}
}
