//---------------
//				var AjaxComboURL=select.data('AjaxComboURL'),
//						AjaxComboCache=select.data('AjaxComboCache')
//				AjaxComboURL ?
//							function( request, response ) {
//								var term = request.term;
//								if ( term in cache ) {
//									response( cache[ term ] );
//									return;
//								}
//
//								lastXhr = $.getJSON( AjaxComboURL, request, function( data, status, xhr ) {
//									cache[ term ] = data;
//									if ( xhr === lastXhr ) {
//										response( data );
//									}
//								});
//							} :
//
