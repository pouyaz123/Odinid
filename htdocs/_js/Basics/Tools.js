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

function htmlTagDecode(val){if(!val)return val;return val.replace(/&gt;/gm, '>').replace(/&lt;/gm, '<').replace(/\\\//gm, '/')}


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
	
	DocumentTitle=document.title
	t.DocTitle=function(Title){
		DocumentTitle=document.title=Title
	}

	var LoadCount = 0
	var LoadingCoverIsOn = false
	var arrURLs = new Array()
	t.RunScriptAfterLoad = function(jssrc, fnc) {//TODO2: complete ajax script loading system (one of other required one)
		jssrc = GetSrcURL(jssrc)
		if (!arrURLs[jssrc])
			fnc()
		else
			arrURLs[jssrc].push(fnc)
	}
	function GetSrcURL(src) {
		if (src.indexOf(AbsURL_Sign) !== 0)
			src = JSRootURL + '/' + src + JSExt
		else
			src = src.substr(1)
		return src
	}
	t.LoadJS = function(src, dontUnique) {
		src = GetSrcURL(src)
		if (arrLoadedJS[src] && !dontUnique)
			return false;
		arrLoadedJS[src] = 1;
		if (!document.body || !document.body.Completed)
			t.Wrt('<script type="text/javascript" language="javascript" src="' + src + '"></script>')
		else if ($) {
			var PBExists = typeof(PostBack) != 'undefined'
			LoadCount++
			arrURLs[src] = new Array()
			if (PBExists && !LoadingCoverIsOn) {
				LoadingCoverIsOn = 1
				PostBack.LoadingCover(document.body, 1)
			}
			$.ajax({url: src, dataType: 'script', type: 'get', cache: true
						, success: function() {
					LoadCount--
					var fnc
					for (fnc in arrURLs[src]) {
						arrURLs[src][fnc]()
					}
					arrURLs[src] = null
					delete arrURLs[src]
					if (!LoadCount && PBExists && LoadingCoverIsOn) {
						LoadingCoverIsOn = 0
						PostBack.LoadingCover(document.body, 0)
					}
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
			DefaultsAreChecked = (typeof(PostBack) != 'undefined')
		}
		if (href.indexOf(AbsURL_Sign) !== 0)
			href = CSSRootURL + '/' + href + CSSExt
		else
			href = href.substr(1)
		if (arrLoadedCSS[href] && !dontUnique)
			return false;
		arrLoadedCSS[href] = 1;
		if (typeof($) == 'undefined')
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

if (typeof($) == 'undefined') {
	if (typeof(window.top.$) != 'undefined')
		var $ = window.top.$
	else
		_t.LoadJS('Basics/jquery.min')
	PBDocComplete.push(function() {
		document.body.Completed = 1
		SyncTDDivHeight()
		PostBack.AddInAjaxComplete('SyncedH', SyncTDDivHeight)
	})
}

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
