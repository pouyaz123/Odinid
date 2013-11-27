// jQuery Alert Dialogs Plugin
//
// Version 1.1 (myButtons added by abbas hashemian - alixniv@gmail.com)
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 29 December 2008
//
// Visit http://abeautifulsite.net/notebook/87 for more information
//
// Usage:
//		jAlert( message, [title, callback, myButtons] )
//		jConfirm( message, [title, callback, myButtons] )
//		jPrompt( message, [value, title, callback, myButtons] )
/**
 * myButtons=[
 * {value:'', attrs:'', callback:function(e){}, focus:true}
 * ]
 */
// 
// History:
//
//		1.00 - Released (29 December 2008)
//
// License:
// 
//		This plugin is licensed under the GNU General Public License: http://www.gnu.org/licenses/gpl.html
//
(function($) {
	
	$.alerts = {
		
		// These properties can be read/written by accessing $.alerts.propertyName from your scripts at any time
		
		verticalOffset: -75,                // vertical offset of the dialog from center screen, in pixels
		horizontalOffset: 0,                // horizontal offset of the dialog from center screen, in pixels/
		repositionOnResize: true,           // re-centers the dialog on window resize
		overlayOpacity: .25,                // transparency level of overlay
		overlayColor: '#000',               // base color of overlay
		draggable: true,                    // make the dialogs draggable (requires UI Draggables plugin)
		okButton: '&nbsp;OK&nbsp;',         // text for the OK button
		cancelButton: '&nbsp;Cancel&nbsp;', // text for the Cancel button
		dialogClass: null,                  // if specified, this class will be applied to all dialogs
		
		// Public methods
		
/**
 * myButtons=[
 * {value:'', attrs:'', callback:function(e){}, focus:true}
 * ]
 */
		alert: function(type, message, title, callback, myButtons) {
			if( title == null ) title = 'Alert';
			$.alerts._show(title, message, null, type, function(result) {
				if( callback ) callback(result);
			}, myButtons);
		},
		
/**
 * myButtons=[
 * {value:'', attrs:'', callback:function(e){}, focus:true}
 * ]
 */
		confirm: function(message, title, callback, myButtons) {
			if( title == null ) title = 'Confirm';
			$.alerts._show(title, message, null, 'confirm', function(result) {
				if( callback ) callback(result);
			}, myButtons);
		},
			
/**
 * myButtons=[
 * {value:'', attrs:'', callback:function(e){}, focus:true}
 * ]
 */
		prompt: function(message, value, title, callback, myButtons) {
			if( title == null ) title = 'Prompt';
			$.alerts._show(title, message, value, 'prompt', function(result) {
				if( callback ) callback(result);
			}, myButtons);
		},
		
		// Private methods
		
/**
 * myButtons=[
 * {value:'', attrs:'', callback:function(e){}, focus:true}
 * ]
 */
		_show: function(title, msg, value, type, callback, myButtons) {
			
			$.alerts._hide();
			$.alerts._overlay('show');
			
			var dir = $('body table:first').css('direction')
			dir=dir?dir.Lower():'ltr'
			$("BODY").append(
			  '<div id="popup_container" class='+dir+'>' +
			    '<h1 id="popup_title"></h1>' +
			    '<div id="popup_content">' +
			      '<div id="popup_message"></div>' +
				'</div>' +
			  '</div>');
			
			if( $.alerts.dialogClass ) $("#popup_container").addClass($.alerts.dialogClass);
			
			// IE6 Fix
			var pos = ($.browser.msie && parseInt($.browser.version) <= 6 ) ? 'absolute' : 'fixed'; 
			
			$("#popup_container").css({
				position: pos,
				zIndex: 99999,
				padding: 0,
				margin: 0
			});
			
			$("#popup_title").text(title);
			$("#popup_content").addClass(type);
			$("#popup_message").text(msg);
			$("#popup_message").html( $("#popup_message").text().replace(/\n/g, '<br />') );
			
			$("#popup_container").css({
				minWidth: $("#popup_container").outerWidth()
//				,
//				maxWidth: $("#popup_container").outerWidth()
			});
			
			$.alerts._reposition();
			$.alerts._maintainPosition(true);

			if(myButtons){
				var Idx, Btn, $Btn
				var $popup_panel=$('<div id="popup_panel"></div>')
				$("#popup_message").after($popup_panel)
				for (Idx in myButtons) {
					Btn=myButtons[Idx]
					if(typeof(Btn)=='string'){
						$popup_panel.append(Btn)
						continue;
					}
					$Btn=$('<input type="button" value="' + Btn.value + '" '+Btn.attrs+' /> ')
					$popup_panel.append($('<a class="Btn"><div></div></a>').append($Btn));
					
					$Btn[0].PromptCallBack=Btn.callback;
					$Btn.click( function(e) {
						$.alerts._hide();
						if(this.PromptCallBack)
							this.PromptCallBack(e)
					});
					if(Btn.focus)
						$Btn.focus()
					if(!Btn.disable_EscButton)
						$(document.body).keypress( function(e) {
							if( e.keyCode == 27 )
								$.alerts._hide();
						});
				}
			}else{
			switch (type) {
			    case 'info':
			    case 'warning':
			    case 'success':
				case 'error':
					$("#popup_message").after('<div id="popup_panel"><input type="button" value="' + $.alerts.okButton + '" id="popup_ok" /></div>');
					$("#popup_ok").click( function() {
						$.alerts._hide();
						callback(true);
					});
					$("#popup_ok").focus().keypress( function(e) {
						if( e.keyCode == 13 || e.keyCode == 27 ) $("#popup_ok").trigger('click');
					});
				break;
				case 'confirm':
					$("#popup_message").after('<div id="popup_panel"><input type="button" value="' + $.alerts.okButton + '" id="popup_ok" /> <input type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_ok").click( function() {
						$.alerts._hide();
						if( callback ) callback(true);
					});
					$("#popup_cancel").click( function() {
						$.alerts._hide();
						if( callback ) callback(false);
					});
					$("#popup_ok").focus();
					$("#popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) $("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) $("#popup_cancel").trigger('click');
					});
				break;
				case 'prompt':
					$("#popup_message").append('<br /><input type="text" size="30" id="popup_prompt" />').after('<div id="popup_panel"><input type="button" value="' + $.alerts.okButton + '" id="popup_ok" /> <input type="button" value="' + $.alerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_prompt").width( $("#popup_message").width() );
					$("#popup_ok").click( function() {
						var val = $("#popup_prompt").val();
						$.alerts._hide();
						if( callback ) callback( val );
					});
					$("#popup_cancel").click( function() {
						$.alerts._hide();
						if( callback ) callback( null );
					});
					$("#popup_prompt, #popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) $("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) $("#popup_cancel").trigger('click');
					});
					if( value ) $("#popup_prompt").val(value);
					$("#popup_prompt").focus().select();
				break;
			}
			}
			
			// Make draggable
			if( $.alerts.draggable ) {
				try {
					$("#popup_container").draggable({handle: $("#popup_title")});
					$("#popup_title").css({cursor: 'move'});
				} catch(e) { /* requires jQuery UI draggables */ }
			}
		},
		
		_hide: function() {
			$("#popup_container").remove();
			$.alerts._overlay('hide');
			$.alerts._maintainPosition(false);
		},
		
		_overlay: function(status) {
			switch( status ) {
				case 'show':
					$.alerts._overlay('hide');
					$("BODY").append('<div id="popup_overlay"></div>');
					$("#popup_overlay").css({
						position: 'fixed',	//'absolute',
						zIndex: 99998,
						top: '0px',
						left: '0px',
						width: '100%',
						height: '100%',		//$(window).height() + 'px',
						background: $.alerts.overlayColor,
						opacity: $.alerts.overlayOpacity
					});
				break;
				case 'hide':
					$("#popup_overlay").remove();
				break;
			}
		},
		
		_reposition: function() {
			var top = (($(window).height() / 2) - ($("#popup_container").outerHeight() / 2)) + $.alerts.verticalOffset;
			var left = (($(window).width() / 2) - ($("#popup_container").outerWidth() / 2)) + $.alerts.horizontalOffset;
			if( top < 0 ) top = 0;
			if( left < 0 ) left = 0;
			
			// IE6 fix
			if( $.browser.msie && parseInt($.browser.version) <= 6 ) top = top + $(window).scrollTop();
			
			$("#popup_container").css({
				top: top + 'px',
				left: left + 'px'
			});
			$("#popup_overlay").height( $(document).height() );
		},
		
		_maintainPosition: function(status) {
			if( $.alerts.repositionOnResize ) {
				switch(status) {
					case true:
						$(window).bind('resize', function() {
							$.alerts._reposition();
						});
					break;
					case false:
						$(window).unbind('resize');
					break;
				}
			}
		}
		
	}
	
	// Shortuct functions
/**
 * myButtons=[
 * {value:'', attrs:'', callback:function(e){}, focus:true}
 * ]
 */
	jAlert = function(type, message, title, callback, myButtons) {
		$.alerts.alert(type, message, title, callback, myButtons);
	}
	
/**
 * myButtons=[
 * {value:'', attrs:'', callback:function(e){}, focus:true}
 * ]
 */
	jConfirm = function(message, title, callback, myButtons) {
		$.alerts.confirm(message, title, callback, myButtons);
	};
		
/**
 * myButtons=[
 * {value:'', attrs:'', callback:function(e){}, focus:true}
 * ]
 */
	jPrompt = function(message, value, title, callback, myButtons) {
		$.alerts.prompt(message, value, title, callback, myButtons);
	};
	
})(jQuery);