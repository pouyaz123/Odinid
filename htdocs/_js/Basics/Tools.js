/** By Abbas Ali Hashemian<tondarweb@gmail.com> - webdesignir.com*/
//+synced height
PBDocComplete = new Array()
MyAutoCompleteFNCs = new Array()
function GetObjLen(obj) {
	var len = 0, i
	for (i in obj) {
		len++
	}
	return len
}
String.prototype.Fill = function(character, num) {
	var str = this.toString()
	for (var i = 0; i < num; i++) {
		str += character
	}
	return str
}
String.prototype.___trim = function(chr, LOR) {
	with (this) {
		var result = (!LOR || LOR == 'R') && substr(length - 1) == chr ? substr(0, length - 1) : this.toString()
		result = (!LOR || LOR == 'L') && substr(0, 1) == chr ? substr(1) : result
	}
	return result
}
String.prototype.trim = function(chr) {
	return this.___trim(chr)
}
String.prototype.ltrim = function(chr) {
	return this.___trim(chr, 'L')
}
String.prototype.rtrim = function(chr) {
	return this.___trim(chr, 'R')
}
String.prototype.Left = function(n) {
	return this.substring(0, n)
}
String.prototype.Right = function(n) {
	with (this) {
		return substring(length - n)
	}
}
String.prototype.Lower = function() {
	return this.toLowerCase()
}
String.prototype.Upper = function() {
	return this.toUpperCase()
}
String.prototype.myIndexOf = function(find, ofEnd) {
	return this[ofEnd ? 'lastIndexOf' : 'indexOf'](find)
}
//find , after this , 1/0(search of end?)
String.prototype.findAfter = function(find, after, ofEnd) {
	with (this) {
		var fromWhr = myIndexOf(after, ofEnd)
		return indexOf(find, fromWhr >= 0 ? fromWhr : length)
	}
}
//vice versa
String.prototype.findBefore = function(find, before, ofEnd) {
	with (this) {
		var fromWhr = myIndexOf(before, ofEnd)
		return lastIndexOf(find, fromWhr >= 0 ? fromWhr : 0)
	}
}
String.prototype.Contains = function(str) {
	return this.indexOf(str) > -1
}
//get substring from a substr to another substr
String.prototype.find2find_substr = function(findStart, findEnd, ofEnd) {
	with (this) {
		var fromWhr = myIndexOf(findStart, ofEnd), toWhr = findAfter(findEnd, findStart, ofEnd);
		if (fromWhr < 0)
			return ''
		if (toWhr < 0)
			toWhr = length
		return substring(fromWhr + findStart.length, toWhr)
	}
}
String.prototype.replaceAll = function(oldS, newS) { //some backups
	var str = new String(this), oldInd = str.indexOf(oldS)
	while (oldInd > -1) {
		str = str.replace(oldS, newS)
		oldInd = str.indexOf(oldS)
	}
	return str
}

function htmlTagDecode(val) {
	if (!val)
		return val;
	return val.replace(/&gt;/gm, '>').replace(/&lt;/gm, '<').replace(/\\\//gm, '/')
}


/**
 *<ul>
 * <li>_t.loadJS = _t.LoadJS //camel mode naming</li>
 * <li>_t.LoadJS = function(src, dontUnique)</li>
 * <li>
 *	_t.RunScriptAfterLoad = function(jssrc, fnc)
 *	_t.RunScriptAfterLoad("myCode", function(){do this when it is loaded})
 * </li>
 * <li>
 *	_t.AddToDependencies = function(src, arrDs)
 *	_t.AddToDependencies('MyJuiAutoComplete/MyComboBox', ['jqUI/jquery.ui.core.min', ...])
 * </li>
 * <li>_t.PushState(url)</li>
 * <li>_t.SetCookie = function(Name, Value)</li>
 * <li>_t.GetCookie = function(Name)</li>
 * <li>_t.loadCSS = _t.LoadCSS //camel mode naming</li>
 * <li>_t.LoadCSS = function(href, dontUnique)</li>
 * <li>DocumentTitle = documen_t.title//global</li>
 * <li>_t.DocTitle = function(Title)</li>
 * <li>_t.PushState = function(URL)</li>
 * <li>_t.Wrt = function(str)</li>
 * </ul>
 * @author "Abbas Ali Hashemian"<tondarweb@gmail.com> webdesignir.com
 * @type @new;_L118
 */
_t = new function() {
	var JSRootURL = '/_js',
			JSExt = '.js',
			CSSRootURL = '/_css',
			CSSExt = '.css',
			AbsURL_Sign = '*',
			t = this

	var arrLoadedJS = {}, arrLoadedCSS = {}

	t.Wrt = function(str) {
		document.open()
		document.write(str)
		document.close()
	}

	t.PushState = function(URL) {
//		history.replaceState
//		history.pushState
		PageURL = URL
		PageURLState = {URL: PageURL}
		if (history.replaceState)
			history.replaceState(PageURLState, '', URL);
	}

	DocumentTitle = document.title//global
	t.DocTitle = function(Title) {
		DocumentTitle = document.title = Title
	}

	var LoadCount = 0
	var LoadingCoverIsOn = false
	var arrOnLoadFncs = new Array()
	function CreateOnLoadFnc(src) {
		if (!arrOnLoadFncs[src]) {
			arrOnLoadFncs[src] = new Array()
			arrOnLoadFncs[src].dependencies = new Array()
		}
	}
	function GetSrcURL(src) {
		if (src.indexOf(AbsURL_Sign) !== 0)
			src = JSRootURL + '/' + src + JSExt
		else
			src = src.substr(1)
		return src
	}
	t.RunScriptAfterLoad = function(jssrc, fnc) {
		if (typeof jssrc != 'string' && typeof jssrc == 'object') {
			t.RunScriptAfterLoad(jssrc[0], jssrc.length > 1 ?
					function() {
						t.RunScriptAfterLoad(jssrc.slice(1), fnc)
					} : fnc)
			return;
		}
		jssrc = GetSrcURL(jssrc)
		if (!arrOnLoadFncs[jssrc] && arrLoadedJS[jssrc])
			fnc()
		else {
			CreateOnLoadFnc(jssrc)
			arrOnLoadFncs[jssrc].push(fnc)
		}
	}
	/**
	 * call all of your loads before this. the LoadJS here will not work in non-ajax mode
	 * @param {String} src the source code requires this dependencies
	 * @param {Array} arrDs dependencies
	 */
	t.AddToDependencies = function(src, arrDs) {
		t.LoadJS(src)
		src = GetSrcURL(src)
		var eachD, eachDurl
		for (eachD in arrDs) {
			eachDurl = GetSrcURL(arrDs[eachD])
			if (!arrLoadedJS[eachDurl] && arrOnLoadFncs[src])
				arrOnLoadFncs[src].dependencies[eachDurl] = 1
			t.LoadJS(arrDs[eachD])
		}
	}
	t.LoadJS = function(src, dontUnique) {
		src = GetSrcURL(src)
		if (arrLoadedJS[src] && !dontUnique)
			return false;
		arrLoadedJS[src] = 1;
		if (!document.body || !document.body.Completed)
			t.Wrt('<script type="text/javascript" language="javascript" src="' + src + '"></script>')
		else if ($) {
			var PBExists = typeof (PostBack) != 'undefined'
			LoadCount++
			CreateOnLoadFnc(src)
			if (PBExists && !LoadingCoverIsOn) {
				LoadingCoverIsOn = 1
				PostBack.LoadingCover(document.body, 1)
			}
			function HandleOnloadFncs(src) {
				if(!arrOnLoadFncs[src])
					return
				var i
				for (i = 0; i < arrOnLoadFncs[src].length; i++)
					arrOnLoadFncs[src][i]()
				arrOnLoadFncs[src] = null
				delete arrOnLoadFncs[src]
				LoadCount--
			}
			function SuccessHandler() {
				var eachD
				if (arrOnLoadFncs[src])
					for (eachD in arrOnLoadFncs[src].dependencies)
						return//so there is atleast a D
				var eachSrc, arrEachSrc, anyD = false
				for (eachSrc in arrOnLoadFncs) {
					arrEachSrc = arrOnLoadFncs[eachSrc]
					if (arrEachSrc.dependencies[src]) {
						arrEachSrc.dependencies[src] = null
						delete arrEachSrc.dependencies[src]
						for (eachD in arrEachSrc.dependencies)
							anyD = true
						if (!anyD) {
							delete arrEachSrc.dependencies
							HandleOnloadFncs(eachSrc)
						}
					}
				}
				HandleOnloadFncs(src)
				if (!LoadCount && PBExists && LoadingCoverIsOn) {
					LoadingCoverIsOn = 0
					PostBack.LoadingCover(document.body, 0)
				}
			}
			$.ajax({url: src, dataType: 'script', type: 'get', cache: true
				, success: function() {
					setTimeout(SuccessHandler, 100)
				}
			})
		}
		return true;
	}
	t.loadJS = t.LoadJS //camel mode naming

	var DefaultsAreChecked = false
	t.LoadCSS = function(href, dontUnique) {
		if (!DefaultsAreChecked) {
			$('head link[rel="stylesheet"][type="text/css"]').each(function(idx, elm) {
				arrLoadedCSS[$(elm).attr('href')] = 1
			})
			DefaultsAreChecked = (typeof (PostBack) != 'undefined')
		}
		if (href.indexOf(AbsURL_Sign) !== 0)
			href = CSSRootURL + '/' + href + CSSExt
		else
			href = href.substr(1)
		if (arrLoadedCSS[href] && !dontUnique)
			return false;
		arrLoadedCSS[href] = 1;
		if (typeof ($) == 'undefined')
			t.Wrt('<link rel="stylesheet" type="text/css" href="' + href + '" />')
		else {
			var $Link = $('<link />')
			$('head').append($Link)
			$Link.attr({'rel': 'stylesheet', 'type': 'text/css', 'href': href})
		}
		return true;
	}
	t.loadCSS = t.LoadCSS //camel mode naming

	var JSCookieName = 'JS'
	t.SetCookie = function(Name, Value) {
		var d = new Date();
		d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
		document.cookie = JSCookieName + '[' + Name + ']=' + Value + '; expires=' + d.toGMTString() + '; path=/';
	}
	t.GetCookie = function(Name) {
		return document.cookie.toString().find2find_substr(JSCookieName + '[' + Name + ']=', ';');
	}
}

function SyncTDDivHeight() {
	var MinH = 0
	$('.SyncTDDivHeight tr').each(function(idx, elm) {
		$('>td>div', this).each(function(idx, elm) {
			var H = $(elm).height()
			MinH = H > MinH ? H : MinH
		}).height(MinH)
		MinH = 0
	})
}

if (typeof ($) == 'undefined') {
	if (typeof (window.top.$) != 'undefined')
		var $ = window.top.$
	else
		_t.LoadJS('Basics/jquery-1.8.0.min')
}
PBDocComplete.push(function() {
	document.body.Completed = 1
	SyncTDDivHeight()
	PostBack.AddInAjaxComplete('SyncedH', SyncTDDivHeight)
})

function TinyMCE_destroyer($Target) {
	if (window.tinyMCE) {
		if (!$Target)
			$Target = $('body')
		$Target.find('.mceBox_Simple, .mceBox_Full').each(function(idx, obj) {
			var id = $(obj).attr('id'), editor = tinyMCE.editors[id]
			if (editor) {
				editor.remove()
				tinymce.execCommand('mceRemoveControl', true, id);
				editor.destroy()
			}
		})
	}
}
