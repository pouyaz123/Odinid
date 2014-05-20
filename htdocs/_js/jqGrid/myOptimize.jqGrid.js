function link_dtpicker(el) {
	setTimeout(function() {
		$(el).css({width: 'auto'}).attr({size: '10'})
		if ($.ui && $.datepicker) {
			$(el)
					.after('<button>Calendar</button>')
					.next()
					.button({icons: {primary: 'ui-icon-calendar'}, text: false})
					.css({'font-size': '69%'})
					.click(function(e) {
						$(el).datepicker('show')
						return false;
					});
			$(el).datepicker({'disabled': false, 'dateFormat': 'yy-mm-dd'});
			$('.ui-datepicker').css({'font-size': '69%'});
		}
	}, 100);
}
function DGEDITROW(DGID, RIdx, _this) {
	$('#' + DGID).editRow(RIdx, true);
	$(_this).parent().hide();
	$(_this).parent().next().show();
}
function DGSAVEROW(DGID, RIdx, _this) {
	$('#' + DGID).saveRow(RIdx);
	$(_this).parent().hide();
	$(_this).parent().prev().show();
}
function DGCANCELEDIT(DGID, RIdx, _this) {
	$('#' + DGID).restoreRow(RIdx);
	$(_this).parent().hide();
	$(_this).parent().prev().show();
}
function DGDELETEROW(DGID, RIdx) {
	$('#' + DGID).delGridRow(RIdx);
}
function DGRowCheckAll(ThisObj) {
	$(ThisObj).parents("tr:first").find(":checkbox:not(.cbox)").attr("checked", "checked");
}
function DGRowUncheckAll(ThisObj) {
	$(ThisObj).parents("tr:first").find(":checkbox:not(.cbox)").attr("checked", null);
}

function DGConstruct(locale) {
	_t.AddToDependencies('jqGrid/jquery.jqGrid.min', [
		'jqUI/jquery.ui.core.min',
		'jqUI/jquery.ui.widget.min',
		'jqUI/jquery.ui.mouse.min',
		'jqUI/jquery.ui.position.min',
		'jqUI/jquery.ui.resizable.min',
		'jqUI/jquery.ui.button.min',
		'jqUI/jquery.ui.dialog.min',
		'jqUI/jquery.ui.datepicker.min',
		'jqGrid/jQueryUI/i18n/grid.locale-' + locale
	])
}

PBDocComplete.push(function() {
	$(document).delegate('body', {keypress: function(e) {//goto:SetActionColumn of jqGrid
			if (e.keyCode == 27 || e.keyCode == 13)
				$('[rel="GridInlistCancelBtn"]:visible').click()
		}})
});
