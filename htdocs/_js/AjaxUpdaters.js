function UpdtLst(slctCoreDiv, slctScrl, msg){var $d=$(slctCoreDiv);$d.append(msg);setTimeout(function(){var os=$d.find(slctScrl).offset();$d=$d.parent();os=(os?os.top:0)+$d.scrollTop();$d[0].scrollTop=os}, 50)}

urlMsgUpdt=new Object()
UpdateRecursives=new Object()
function UpdateRecursive(slctTgt, url, delay, timeout){
	if(UpdateRecursives[slctTgt])return
	else UpdateRecursives[slctTgt]={fncSleep:null}
	if(!timeout)timeout=30
	if(!delay)delay=4
	var tout, kw='UpdateRecursiveScript', fncSleep
	function cleart(){if(tout){clearTimeout(tout);tout=null}}
	function recursive(){
		var $Tgt=$(slctTgt)
		if(!$Tgt.length){cleart();UpdateRecursives[slctTgt]=null;return}
		var handler=function(result){
			cleart()
			if(!$Tgt.find('.'+kw).length)$Tgt.append('<div class="'+kw+'" style="display:none"></div>')
			if(result)$Tgt.find('.'+kw).append(result)
			setTimeout(function(){recursive()},delay*1000)
		}
		fncSleep=UpdateRecursives[slctTgt].fncSleep
		if(fncSleep && fncSleep())
			handler()
		else{
			var CurrentReqURL=url.url
			$.ajax({url:url.url,type:'get',success:handler,error:handler})
			tout=setTimeout(function(){PostBack.AbortAjax(CurrentReqURL)}, timeout*1000)
		}
	};setTimeout(function(){recursive()},delay*1000)
}

function updtCartableBar(nb_kmg, nm_kmg, nc_kmg, nb, nm, nc) {
$('#hdrNewBidCount').html(nb ? nb_kmg : '').attr('title', nb).css('display',nb?'inline-block':'none')
$('#hdrNewMsgCount').html(nm ? nm_kmg : '').attr('title', nm).css('display',nm?'inline-block':'none')
$('#hdrNewCnnCount').html(nc ? nc_kmg : '').attr('title', nc).css('display',nc?'inline-block':'none')
setTimeout(function(){document.title=(nb?'(B:'+nb_kmg+') ':'')+(nm?'(M:'+nm_kmg+') ':'')+(nc?'(C:'+nc_kmg+') ':'')+DocumentTitle}, 100)}
function sleepCartableBar(fnc) {UpdateRecursives['#hdrNewMsgCount'].fncSleep=fnc}
function updtobjCbarURL(url){if(typeof(objCbarURL)!=='undefined')objCbarURL.url=url}
function updtCopyright(str){$('#tdCopyright').html(str)}
function updtLngLinks(arrURLs){var code; for (code in arrURLs)$('#lnkLng'+code).attr('href', arrURLs[code])}
function updtOnlineStatus(UID, Online, Title){/*T/User*/$('.divOnlineStatus'+UID).replaceWith("<div class='divOnlineStatus" + UID + (Online ? " OnlineStatus" : " OfflineStatus") + "'>"+ Title+ "</div>")}