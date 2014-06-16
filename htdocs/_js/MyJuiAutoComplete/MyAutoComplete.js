/** By Abbas Ali Hashemian<tondarweb@gmail.com> - webdesignir.com*/
/**
 * @param options is same as the autocomplete
 */
function MyAutoComplete($obj, options, isMulti, isAjax, minLen, returnACOptsOnly) {
	function split(val) {
		return val.split(/,\s*/);
	}
	function extractLast(term) {
		return split(term).pop();
	}
	var opts = {
		open: function(event, ui) {
			if (options.open)
				options.open(event, ui)
			var $this = $(this), $uac = $('.ui-autocomplete')
			if ($this.hasClass('ltr'))
				$uac.addClass('ltr')
			if ($this.hasClass('rtl'))
				$uac.addClass('rtl')
		},
		close: function(event, ui) {
			if (options.close)
				options.close(event, ui)
			$('.ui-autocomplete').removeClass('ltr rtl')
		},
		minLength: minLen && minLen.toString().length ? minLen : (options.minLength ? options.minLength : 1),
		delay: options.delay ? options.delay : 750
	}
	var opt
	for (opt in options) {
		if (opt !== 'open' && opt !== 'close' && opt !== 'minLength')
			opts[opt] = options[opt]
	}
	if (isMulti) {
		$obj.keydown(function(event) {
			if (event.keyCode === $.ui.keyCode.TAB && $(this).data("autocomplete").menu.active)
				event.preventDefault();
		})
		opts.focus = function(event, ui) {
			if (options.focus)
				options.focus(event, ui)
			return false;
		}
		opts.select = function(event, ui) {
			if (options.select && options.select(event, ui) === false)
				return
			var terms = split(this.value);
			terms.pop();
			terms.push(ui.item.value);
			terms.push("");
			this.value = terms.join(", ");
			return false;
		}
		opts.source = function(request, response) {
			response($.ui.autocomplete.filter(options.source, extractLast(request.term)));
		}
	}
	if (isAjax) {
		opts.source = function(request, response) {
			function myResp(r) {
				response(r)
				var $uiac = $('.ui-autocomplete')
				setTimeout(function() {
					$uiac.find('li a').each(function() {
						var $a = $(this)
						$a.html(htmlTagDecode($a.html()))
						var $href = $a.find('[rel*="href:"]')
						if ($href.length) {
							$a.attr({rel: 'superbox[ajax][' + $href.attr('rel').find2find_substr('href:') + '][autoxauto]'})
						}
						var $separators = $a.find('.__uiausep')
						if ($separators.length)
							$a.replaceWith($($a.html()))
						var $BlankTargets = $a.find('[rel*=__btarget]')
						if ($BlankTargets.length)
							$a.attr({'target': '_blank', 'href': $BlankTargets.attr('rel').find2find_substr('__btarget:', ' ')}).unbind('click')
					})
					$uiac.css('width', 'auto')
//					SuperBox_Reconstruct()
				}, 100)
			}
			$.getJSON(options.source, {
				term: extractLast(request.term)
			}, myResp);
		}
		opts.search = function(event, ui) {
			if (options.search)
				options.search(event, ui)
			if (extractLast(this.value).length < minLen)
				return false;
		}
	}
	if (returnACOptsOnly)
		return opts;
	else
		$obj.autocomplete(opts)
}
function MyAutoComplete_Construct() {
	function Construct() {
		var fnc //MyAutoCompleteFNCs is created in Tools
		for (fnc in MyAutoCompleteFNCs) {
			if (MyAutoCompleteFNCs[fnc])
				MyAutoCompleteFNCs[fnc]()
			MyAutoCompleteFNCs[fnc] = null
			delete MyAutoCompleteFNCs[fnc]
		}
		return true
	}
	function Run() {
		PostBack.AddInHTMLAjaxComplete('MyAutoComplete', Construct)
		Construct()
	}
	if (typeof (PostBack) !== 'undefined')
		Run()
	else
		PBDocComplete.push(Run)
}
MyAutoComplete_Construct()
