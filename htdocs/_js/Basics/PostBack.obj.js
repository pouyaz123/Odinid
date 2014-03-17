//clickable cancel attr : this.ClickCanceled
//REQUIREMENTS : jquery.min.js + jquery.form.js (ajax plugin) + Assets_Prompt
/**
 * rel="AjaxPanel:#TargetID DefaultButton:#ID"
 * rel="AjaxElement:#TargetID:replace"	//:replace :insert
 * rel="AjaxExcept"
 * rel="SimpleAjaxPanel"
 * 
 * rel="__AjaxPostKW:A_SpecialKW"
 * rel="AsyncURL:http://x.x/x"
 * rel="AjaxPostParams:P1=value;P2=value"
 * rel="ImgBtnHSrc:/..."
 * 
 * example:
 * <div rel="AjaxPanel
 *			DefaultButton:#ID
 *			AsyncURL:http://x.x/x
 *			AjaxPostParams:P1=value;P2=value
 *			__AjaxPostKW:A_SpecialKW" >...</div>
 *			
 * <img rel="REFRESHONAJAX"/> refresh this img after successful ajax communication
 * AjaxLoadingCoverContainer	//this className will be added to element and panel
 * AjaxLoadingCover				//loading div className
 * By Abbas Ali Hashemian<tondarweb@gmail.com> - webdesignir.com
 **/
//Any thing about post back is here
var PostBack = new function()
{
	var ClickDelay = 50

	//------ KEYWORDS
	var AjaxElmPrefix = 'AjaxElement'
			, AjaxElmJQSlct = '[rel*="' + AjaxElmPrefix + '"]'
			, AjaxPnlPrefix = 'AjaxPanel'
			, AjaxPnlJQSlct = '[rel*="' + AjaxPnlPrefix + '"]'
			, DefaultButtonPrefix = 'DefaultButton'
			, DefaultButtonJQSlct = '[rel*="' + DefaultButtonPrefix + '"]'
			, SaveHotKeyPanelJQSlct = DefaultButtonJQSlct + '[rel*="SaveHotKey"]'
			, jqAjaxNotExempt = ':not([rel*="AjaxExcept"], [class*="AjaxExcept"], [rel*="AjaxExempt"], [class*="AjaxExempt"], [rel*="[ajax]"], [target])'
			, AsyncURLPrefix = 'AsyncURL'
			, AjaxPostParamsPrefix = 'AjaxPostParams'
			, AjaxKeyword_PostParamName = '__AjaxPostKW';
	//	,AsyncMouseoverJQSlct='[rel*="'+AsyncMouseoverPrefix+'"]'

	//------ CONFIG
	if (!Resources)
		Resources = {}
	if (!Resources.PostBack_AJAX_Err)
		Resources.PostBack_AJAX_Err = 'An error occurred during AJAX communication!'
	if (!Resources.PostBack_AJAX_ErrRetry)
		Resources.PostBack_AJAX_ErrRetry = 'An error occurred during AJAX communication! Would you like to retry?'

	//LOADING
	var AjaxLoadingCover_AnimationSpeed = 0
			, AjaxLoadingCover_Opacity = 0.5
			, AjaxLoadingCover_ClassName = 'AjaxLoadingCover'
			, AjaxLoadingCover_ContainerClassName = 'AjaxLoadingCoverContainer'
			, Ajax_ProgressBar = '<div class="AjaxProgressBar"></div>'
			, Ajax_ProgressRod = '<div class="AjaxProgressRod"></div>'
			, Ajax_ProgressPercent = '<div class="AjaxProgressPercent"></div>'

	//for validation public access
	this.DefaultButtonPrefix = DefaultButtonPrefix
	var _this = this;

	//success handler
	this.arrAjaxSuccess = []
	this.AddInAjaxSuccess = function(Idx, Fun) {
		if (Idx === null)
			Idx = this.arrAjaxSuccess.length
		this.arrAjaxSuccess[Idx] = Fun
	}
	function AJAX_Success(result) {
		setTimeout(function() {
			var idx
			for (idx in _this.arrAjaxSuccess) {
				if (_this.arrAjaxSuccess[idx](result) === false) {
					_this.arrAjaxSuccess[idx] = null
					delete _this.arrAjaxSuccess[idx]
				}
			}
		}, 100);
	}
	//error handler
	this.arrAjaxErr = []
	this.arrAjaxErr_temp = []
	this.AddInAjaxErr = function(Idx, Fun) {
		if (Idx === null)
			Idx = this.arrAjaxErr.length
		this.arrAjaxErr[Idx] = Fun
	}
	this.AddInAjaxErr_temp = function(Idx, Fun) {
		if (Idx === null)
			Idx = this.arrAjaxErr_temp.length
		this.arrAjaxErr_temp[Idx] = Fun
		return Idx
	}
	var OfflineErrorCounts = 0
	function AJAX_Err(XHR) {
		var idx
		for (idx in _this.arrAjaxErr) {
			_this.arrAjaxErr[idx](XHR)
		}
		for (idx in _this.arrAjaxErr_temp) {
			_this.arrAjaxErr_temp[idx](XHR)
			_this.arrAjaxErr_temp[idx] = null;
			delete(_this.arrAjaxErr_temp[idx]);
		}
		if (!XHR.AlixErrHandled && !XHR.status)
			OfflineErrorCounts++
		if (XHR.status == 200) {
			OfflineErrorCounts = 0
			setTimeout(function() {
				LoadingCover($('body'), 0)
			}, 1000);
		}
		if (!XHR.AlixErrHandled && ((XHR.status && XHR.status !== 200) || (!XHR.status && OfflineErrorCounts >= 4))) {
			OfflineErrorCounts = 0
			jAlert((XHR.status === 403 ? 'warning' : 'error'),
					XHR.status + '\n' + Resources.PostBack_AJAX_Err + '\n' +
					'\nServer Response:\n' + XHR.responseText
					, XHR.status == 403 ? Resources.Warning : 'AJAX Communication Error!')
		}
	}
	this.AJAX_Err = AJAX_Err;
	this.arrAjaxComplete = []
	this.AddInAjaxComplete = function(Idx, Fnc) {
		if (Idx === null)
			Idx = this.arrAjaxComplete.length
		this.arrAjaxComplete[Idx] = Fnc
	}
	this.arrHTMLAjaxComplete = []
	this.AddInHTMLAjaxComplete = function(Idx, Fnc) {
		if (Idx === null)
			Idx = this.arrHTMLAjaxComplete.length
		this.arrHTMLAjaxComplete[Idx] = Fnc
	}
	$.ajaxSetup({
		type: 'POST',
		//		data : {}, 
		beforeSend: function(XHR) {
		},
		complete: function(XHR) {
			if (XHR.status == 200 || XHR.status == 301 || XHR.status == 302 || XHR.status == 304) {
				var ax = 'a8d2fdabaa1dd803bad2914a9484c389'
				OfflineErrorCounts = 0
				var RHeaders = XHR.getAllResponseHeaders()
				var idx
				for (idx in _this.arrAjaxComplete) {
					if (_this.arrAjaxComplete[idx](XHR) === false) {
						_this.arrAjaxComplete[idx] = null
						delete _this.arrAjaxComplete[idx]
					}
				}
				if (RHeaders.Contains('text/html') || RHeaders.Contains('text/xml') || RHeaders.Contains('text/json'))
					setTimeout(function() {
						for (idx in _this.arrHTMLAjaxComplete) {
							if (_this.arrHTMLAjaxComplete[idx](XHR) === false && ax === 'a8d2fdabaa1dd803bad2914a9484c389') {
								_this.arrHTMLAjaxComplete[idx] = null
								delete _this.arrHTMLAjaxComplete[idx]
							}
						}
					}, 100);
			} else {//err
				if (!XHR.status)
					XHR.responseText = "You're NOT ONLINE probably"
				AJAX_Err(XHR)
			}
		},
		success: function(data, s, XHR) {
			OfflineErrorCounts = 0
		}, //jQuery ajax success doesn't work properly so it will be triggered by complete
		error: AJAX_Err,
		cache: true
	})
	var currentRequests = {}
	function AbortAjax(URL) {
		var LoopURL, idx
		for (LoopURL in currentRequests) {
			if (LoopURL == URL) {
				for (idx in currentRequests[URL]) {
					currentRequests[URL][idx].abort()
				}
				currentRequests[URL] = null
				delete currentRequests[URL]
			}
		}
	}
	this.AbortAjax = AbortAjax
	$.ajaxPrefilter(function(opt, def, $xhr) {
		if (opt.abortOnRetry)
			AbortAjax(opt.url)
		if (!currentRequests[ opt.url ])
			currentRequests[ opt.url ] = []
		currentRequests[ opt.url ].push($xhr);
	})

	//Construct
	function Construct() {
		$('body').delegate('[rel*="OnceClick"]', {
			click: function() {
				var _this = this
				$(this).attr('disabled', 'disabled').addClass('disabled')
				setTimeout(function() {
					if (_this.ClickCanceled)
						$(_this).attr('disabled', null).removeClass('disabled')
				}, ClickDelay)
			}
		}).delegate('[rel*="ImgBtnHSrc"]', {
			mouseover: function() {
				var $this = $(this)
				if (!this.nhsrc) {
					this.nhsrc = $this.attr('src')
					this.hsrc = $this.attr('rel').toString().find2find_substr('ImgBtnHSrc:', ' ')
				}
				$(this).attr('src', this.hsrc)
			},
			mouseout: function() {
				$(this).attr('src', this.nhsrc)
			}
		}
		)
		DefBtnConstruct()
		AsyncConstruct()
	}
	function DefBtnConstruct() {
		function IsDefBtnHotKeys(e) {
			return (e.which == 13 || e.keyCode == 13 || e.which == 10 || e.keyCode == 10) && e.ctrlKey;
		}
		function DefBtnClick($Panel) {
			if ($Panel && $Panel.length) {
				var DefBtnSelector = $Panel.attr('rel').find2find_substr(DefaultButtonPrefix + ':', ' ')
				if (DefBtnSelector)
					$Panel.find(DefBtnSelector + ':not(:disabled):first').click()
				return false
			}
		}
		$('body').delegate(SaveHotKeyPanelJQSlct, {
			mouseover: function() {
				$(this).attr('FocusedPostbackPanel', 1)
			}, mouseout: function() {
				$(this).attr('FocusedPostbackPanel', 0)
			}
		})
		$('body').delegate(DefaultButtonJQSlct, {
			keypress: function(e) {
				if (!$(e.target).is('.ui-search-toolbar input') &&
						(((e.which == 13 || e.keyCode == 13) && e.target.tagName.toString().Lower() == 'input') ||
								(e.target.tagName.toString().Lower() == 'textarea' && IsDefBtnHotKeys(e))))
					return DefBtnClick($(this))
			}
		})
		$(document.body).keypress(function(e) {
			if (IsDefBtnHotKeys(e))
				return DefBtnClick($(SaveHotKeyPanelJQSlct + '[FocusedPostbackPanel=1]'))
		})
	}
	this.arrOnHashChange = Array();
	var StoredHash = ''
	function AsyncConstruct() {
		$('body').delegate(
				//panel
				AjaxPnlJQSlct + ' a:not([href*="javascript:"], ' + AjaxElmJQSlct + ')' + jqAjaxNotExempt +
				', ' + AjaxPnlJQSlct + ' :button:enabled:not(' + AjaxElmJQSlct + ')' + jqAjaxNotExempt +
				', ' + AjaxPnlJQSlct + ' :submit:enabled:not(' + AjaxElmJQSlct + ')' + jqAjaxNotExempt +
				', ' + AjaxPnlJQSlct + ' :image:enabled:not(' + AjaxElmJQSlct + ')' + jqAjaxNotExempt +
				//independent
				', ' + AjaxElmJQSlct + ':not(:text, :password, select, textarea, :disabled)' + jqAjaxNotExempt +
				', [class*="' + AjaxElmPrefix + '"]',
				{click: function(e) {
						var $this = $(this)
						if ($this.is('select') || ($this.is('a') && !$this.attr('href') && $this.parent().is('.ui-menu-item')))
							return true
						var href = $this.attr('href')
						if (href) {
							href = href.split('?', 2)[0]
							if (/^.*\.\w{1,4}$/.test(href) && href.Right(3).Lower() != 'php')
								return true;
						}
						if ($this.is('[class*="' + AjaxElmPrefix + '"]')) {
							var cls = $this.attr('class'), target = cls.find2find_substr(AjaxElmPrefix + ':', ' ')
							$this.attr({
								'class': cls.replace(AjaxElmPrefix + (target ? ':' + target : '')),
								'rel': AjaxElmPrefix + (target ? ':' + target : '')
							})
						}
						return TriggerClick($this, e);
					}
				}
		)

		$('body').delegate(
				':text' + AjaxElmJQSlct + jqAjaxNotExempt
				+ ', :password' + AjaxElmJQSlct + jqAjaxNotExempt
				+ ', select' + AjaxElmJQSlct + jqAjaxNotExempt
				+ ', textarea' + AjaxElmJQSlct + jqAjaxNotExempt
				, {
			change: function(e) {
				var $this = $(this)
						, URL = GetAsyncURL($this)
						, Params = {}
				Params[$this.attr('name')] = $this.val()
				Post($this, null, URL, Params, 'REPLACE')
			}
		}
		)
	}

	function GetAsyncURL($this, $Panel) {
		var URL = ''
				, ThisRel = $this.attr('rel')
				, PanelRel = $Panel ? $Panel.attr('rel') : '';
		if (ThisRel)
			URL = ThisRel.find2find_substr(AsyncURLPrefix + ':', ' ')
		if (!URL && $this.is('a') && $this[0].href.indexOf('javascript:') !== 0)
			URL = $this[0].href
		if (!URL && $Panel) {
			if (PanelRel)
				URL = PanelRel.find2find_substr(AsyncURLPrefix + ':', ' ')
			if (!URL && $Panel.is('form'))
				URL = $Panel.attr('actoin')
		}
		//commented because of pushstate in AddEditCat
//		if(!URL)
//			URL=$this.parents('form:first').attr('action')
		if (!URL)
			URL = PageURL
		return URL
	}
	function LoadingCover($Target, boolOnOff, CallBack, CoverSpecialCSS, URL) {
		if ($Target)
			$Target = $($Target)
		else
			$Target = $('body')

		if ($Target.is('input, select, textarea'))
			$Target = $Target.parent()

		if (!CallBack)
			CallBack = function() {
			}
		if (boolOnOff) {
			$Target.addClass(AjaxLoadingCover_ContainerClassName)
			var $LoadingCover = $('<div class="' + AjaxLoadingCover_ClassName + '"></div>'),
					$LoadingRing = $('<div class="AjaxLoadingRing"></div>')
			if ($Target.is('body'))
				$LoadingCover.css({position: 'fixed'})
			var $CloseBtn = $('<a href="javascript:void(0);" class="LodingCoverCloseButton">X</a>').click(function() {
				LoadingCover($Target, 0, 0, 0, URL)
			})
			if (CoverSpecialCSS)
				$LoadingCover.css(CoverSpecialCSS)

			$Target.append($LoadingCover
					.append($LoadingRing.css({
				opacity: AjaxLoadingCover_Opacity
			}))
					.append($CloseBtn)
					)

			$LoadingCover.fadeTo(AjaxLoadingCover_AnimationSpeed, 1, function() {
				CallBack($LoadingCover)
			})
		}
		else {
			$Target.find('div.' + AjaxLoadingCover_ClassName).fadeOut(AjaxLoadingCover_AnimationSpeed, function() {
				$(this).remove()
				CallBack(null)
			})
			if (URL)
				AbortAjax(URL)
			//commented for PIE problem with IE when a relative obj is changing to static
			//$Target.removeClass(AjaxLoadingCover_ContainerClassName)
		}
	}
	_this.LoadingCover = LoadingCover
	function Post($this, $Panel, URL, arrNewParams, InsertMode, PanelAsSimpleTarget) {
		var ThisRel = $this ? $this.attr('rel') : ''
				, PanelRel = $Panel ? $Panel.attr('rel') : ''
				, TargetSelector
				, AjaxKW
				, RelPostParams;
		if (ThisRel) {
			TargetSelector = ThisRel.find2find_substr(AjaxElmPrefix + ':', ' ')
			AjaxKW = ThisRel.find2find_substr(AjaxKeyword_PostParamName + ':', ' ')
			RelPostParams = ThisRel.find2find_substr(AjaxPostParamsPrefix + ':', ' ')
		}
		PanelAsSimpleTarget = PanelAsSimpleTarget || PanelRel.indexOf('SimpleAjaxPanel')>-1
		if (PanelRel && !PanelAsSimpleTarget) {
			if (!TargetSelector)
				TargetSelector = PanelRel.find2find_substr(AjaxPnlPrefix + ':', ' ')
			if (!AjaxKW)
				AjaxKW = PanelRel.find2find_substr(AjaxKeyword_PostParamName + ':', ' ')
			if (!RelPostParams)
				RelPostParams = PanelRel.find2find_substr(AjaxPostParamsPrefix + ':', ' ')
		}
		if (AjaxKW)
			arrNewParams[AjaxKeyword_PostParamName] = AjaxKW
		if (RelPostParams) {
			RelPostParams = RelPostParams.split(';')
			var Idx, Param
			for (Idx in RelPostParams) {
				Param = RelPostParams[Idx]
				if (Param.Left(1) == '*') {
					var $Obj = $(Param.substr(1));
					Param = [$Obj.attr('name'), $Obj.attr('value')]
				} else
					Param = Param.split('=')
				arrNewParams[Param[0]] = Param[1]
			}
		}

		//POSTING
		InsertMode = InsertMode ? InsertMode.Upper() : (TargetSelector ? 'INSERT' : 'REPLACE')
		if (TargetSelector) {
			if (TargetSelector.indexOf(':replace') > -1) {
				InsertMode = 'REPLACE'
				TargetSelector = TargetSelector.replace(':replace', '')
			}
			if (TargetSelector.indexOf(':insert') > -1) {
				InsertMode = 'INSERT'
				TargetSelector = TargetSelector.replace(':insert', '')
			}
		}
		var $Target = TargetSelector ? $(TargetSelector) : $Panel
		$Target = ($Target && $Target.length > 0) ? $Target : null
		if (window.tinyMCE)
			tinyMCE.triggerSave()
		var AnyFile = $this.is('input:file:enabled[value]')
		if (AnyFile)
			AnyFile = ($this.attr('value') != '')

		//target posting
		if ($Target) {
			TinyMCE_destroyer($Target)
			if (!PanelAsSimpleTarget && !$this.is('a')) {
				if (!AnyFile && !$this.is('select')) {
					$Target.find('input:file:enabled[value]').each(function(idx, obj) {
						if (obj.value != '')
							AnyFile = true
					})
				}
				if (!AnyFile) {
					var PanelParams = $Target.find('input, select, textarea').filter(':not(:disabled)').serializeArray()
							, PPIdx
							, PPItem;
					for (PPIdx in PanelParams) {
						PPItem = PanelParams[PPIdx]
						arrNewParams[PPItem.name] = PPItem.value
					}
				}
			}
		}

		var ErrHandlerIdx = _this.AddInAjaxErr_temp(null, function(XHR) {
			if (!XHR.AlixErrHandled)
				XHR.AlixErrHandled = true;
			LoadingCover($Target, 0, function() {
				var Msg = '\nServer Response:\n' + XHR.responseText
				if (XHR.status == 403)
					jAlert('warning', Resources.PostBack_AJAX_Err + '\n' + Msg, 'Forbidden')
				else
					jConfirm(Resources.PostBack_AJAX_ErrRetry + '\n' + Msg, 'AJAX Communication Error!', function(r) {
						if (r)
							Post($this, $Panel, URL, arrNewParams, InsertMode);
					})
			}, 0, URL)
		})
		function OnSuccessHandler(result) {
			AJAX_Success(result)
			_this.arrAjaxErr_temp[ErrHandlerIdx] = null
			delete _this.arrAjaxErr_temp[ErrHandlerIdx];
			if ($Target) {
				var $imgs, $Container
				if (InsertMode === 'INSERT') {
					$Container = $Target.html(result)
				}
				else if (InsertMode === 'REPLACE') {
					$Container = $Target.parent()
					$Target.replaceWith(result)//so it's an ajax panel and should be replaced
				}
				var idx
				for (idx in _this.arrHTMLAjaxComplete) {
					_this.arrHTMLAjaxComplete[idx]({responseText: result})
				}
				$imgs = $Container.find('img[rel*="REFRESHONAJAX"]')
				$imgs.each(function(idx, obj) {
					LoadingCover($(obj).parent(), 1, 0, 0, URL)
					var $iframe = $('<iframe src=' + obj.src + ' style="display:none"></iframe>')
					$iframe.load(function() {
						setTimeout(function() {
							obj.src = obj.src
							LoadingCover($(obj).parent(), 0, 0, 0, URL)
							$iframe.remove()
						}, 500)
					})
					$(document.body).append($iframe)
				})
			} else
				LoadingCover(null, 0, 0, 0, URL)
		}
		if ($this.is('a') && !GetObjLen(arrNewParams)) {
			LoadingCover($Target, 1, function() {
				$.get(URL, OnSuccessHandler)
			}, 0, URL)
		} else if (AnyFile) {
			LoadingCover($Target, 1, function($LoadingCover) {
				var $Ajax_ProgressBar = $(Ajax_ProgressBar)
						, $Ajax_ProgressRod = $(Ajax_ProgressRod)
						, $Ajax_ProgressPercent = $(Ajax_ProgressPercent)
				$Ajax_ProgressBar.append($Ajax_ProgressRod).append($Ajax_ProgressPercent)
				arrNewParams['X_REQUESTED_WITH'] = 'XMLHttpRequest'

				$('body form:first').ajaxSubmit({
					url: URL,
					data: arrNewParams,
					success: function(result) {
						if (result && result.Contains('<!--##--ERROR--##-->'))
							AJAX_Err({
								responseText: result.replaceAll('<!--##--ERROR--##-->', '')
							})
						else
							OnSuccessHandler(result)
					},
					error: AJAX_Err,
					beforeSend: function() {
						$Ajax_ProgressRod.width('0%')
						$Ajax_ProgressPercent.html('0%')
					},
					uploadProgress: function(event, position, total, Percent) {
						var ProgressPercent = Percent + '%';
						if ($LoadingCover.has('div.AjaxProgressBar_Container').length == 0)
							$LoadingCover.append('<div class="AjaxProgressBar_Container"><table><tr><td></td></tr></table></div>').find('td').append($Ajax_ProgressBar)
						$Ajax_ProgressRod.width(ProgressPercent)
						$Ajax_ProgressPercent.html(ProgressPercent);
					},
					complete: function(xhr) {
						$Ajax_ProgressRod.width('100%').delay(250).remove()
						$Ajax_ProgressPercent.html('100%').delay(250).remove()
					}
				})
			}, 0, URL)
		} else {
			LoadingCover($Target, 1, function() {
				$.post(URL, arrNewParams, OnSuccessHandler)
			}, 0, URL)
		}
		return false;
	}
	this.Post = Post
	function TriggerClick($this, e) {
		setTimeout(function() {
			if (e.which > 1 || e.button > 1)
				return true
			if ($this[0].ClickCanceled)
				return false;
			var $Panel = $this.parents(AjaxPnlJQSlct + ':first')
					, URL
					, Params = {}
			//Hyperlinks
			if ($this.is('input')) {
				var name = $this.attr('name')
						, value = $this.val()
				if (name && value)
					Params[name] = value
			}
			URL = GetAsyncURL($this, $Panel)
			return Post($this, $Panel, URL, Params)
		}, ClickDelay)
		return false
	}
	this.TriggerClick = TriggerClick
	this.jConfirm = function(_this, message, title, myButtons) {
		if (_this.jConfirmed === true) {
			_this.jConfirmed = null
			_this.ClickCanceled = 0;
			return true
		}
		if (_this.jConfirmed !== false) {
			jConfirm(message, title, function(r) {
				_this.jConfirmed = r
				$(_this).click()
			}, myButtons)
		} else {
			_this.jConfirmed = null
		}
		_this.ClickCanceled = 1;
		return false
	}
	PBDocComplete.push(Construct)
}
function PBDocCompleteTriggerer() {
	var ehIdx
	for (ehIdx in PBDocComplete) {
		if (!PBDocComplete[ehIdx]) {
			delete PBDocComplete[ehIdx]
			continue
		}
		PBDocComplete[ehIdx]()
		PBDocComplete[ehIdx] = null
		delete PBDocComplete[ehIdx]
	}
}
PBDocCompleteTriggerer()