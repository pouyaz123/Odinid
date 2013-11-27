function SuperBox_Reconstruct() {
	$.superbox();
	$('[rel*="superbox"]').each(function(idx, obj) {
		var rel = $(obj).attr('rel').replace('superbox', '')
		$(obj).attr('rel', rel)
	})
}
PBDocComplete.push(function() {
	SuperBox_Reconstruct()
	PostBack.AddInHTMLAjaxComplete('SuperBox', function() {
		SuperBox_Reconstruct()
	})
});
