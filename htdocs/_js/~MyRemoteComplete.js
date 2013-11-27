//function MyRemoteComplete() {
//	function Construct() {
//		var KW = '[rel*="RemoteAutoComplete"]'
//		$(KW).not('[RACAdded]').attr('RACAdded', 1).each(function(idx, obj) {
//			var rel=$(obj).attr('rel'), URL
//			if(rel)
//				URL = rel.find2find_substr('AsyncURL:', ' ')
//			if(!URL)
//				URL=window.location.href
//			$(obj).autocomplete({
//				source: function(request, response) {
//					$.ajax({
//						url: URL,
//						dataType: "jsonp",
//						data: {
//							featureClass: "P",
//							style: "full",
//							maxRows: 12,
//							name_startsWith: request.term
//						},
//						success: function(data) {
//							response($.map(data.geonames, function(item) {
//								return {
//									label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName,
//									value: item.name
//								}
//							}));
//						}
//					});
//				},
//				minLength: 1,
////				select: function(event, ui) {
////				},
//				open: function() {
//					$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
//				},
//				close: function() {
//					$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
//				}
//			});
//		})
//	}
//	Construct()
//	if (typeof(PostBack) != 'undefined')
//		PostBack.AddInHTMLAjaxComplete('AutoComplete', Construct)
//	PBDocComplete.push(function() {
//		PostBack.AddInHTMLAjaxComplete('AutoComplete', Construct)
//		Construct()
//	})
//}
//jqUI_RemoteComplete()