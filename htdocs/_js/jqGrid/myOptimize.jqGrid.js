function link_dtpicker(el){
	setTimeout(function(){
		$(el).css({width:'auto'}).attr({size:'10'})
		if($.ui){
			if($.datepicker){
				$(el)
					.after('<button>Calendar</button>')
					.next()
					.button({icons:{primary: 'ui-icon-calendar'}, text:false})
					.css({'font-size':'69%'})
					.click(function(e){
						$(el).datepicker('show')
						return false;
					});
				$(el).datepicker({'disabled':false, 'dateFormat':'yy-mm-dd'});
				$('.ui-datepicker').css({'font-size':'69%'});
			}
		}
	},100);
}
function DGEDITROW(DGID, RIdx, _this){
	$('#'+DGID).editRow(RIdx,true); $(_this).parent().hide(); $(_this).parent().next().show();}
function DGSAVEROW(DGID, RIdx, _this){
	$('#'+DGID).saveRow(RIdx); $(_this).parent().hide(); $(_this).parent().prev().show();}
function DGCANCELEDIT(DGID, RIdx, _this){
	$('#'+DGID).restoreRow(RIdx); $(_this).parent().hide(); $(_this).parent().prev().show();}
function DGDELETEROW(DGID, RIdx){
	$('#'+DGID).delGridRow(RIdx);}
function DGRowCheckAll(ThisObj){
	$(ThisObj).parents("tr:first").find(":checkbox:not(.cbox)").attr("checked", "checked");
}
function DGRowUncheckAll(ThisObj){
	$(ThisObj).parents("tr:first").find(":checkbox:not(.cbox)").attr("checked", null);
}

PBDocComplete.push(function(){
	$(document).delegate('body', {keypress:function(e){//goto:SetActionColumn
		if(e.keyCode==27 || e.keyCode==13)
			$('[rel="GridInlistCancelBtn"]:visible').click()
	}})
});
