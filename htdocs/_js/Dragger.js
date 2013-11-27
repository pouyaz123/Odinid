//Dragger is not checked for jQuery 1.8 compatibility and my new knowledges
$(function(){
	var KW="Dragger:"
	, $DragEnv=$(document)
	var $Draggers=$('[rel*="'+KW+'"]')
	
	$Draggers.mousedown(function(){
		var Selector=$(this).attr('rel').toString().find2find_substr(KW, ' ')
		if(Selector)
			$DragEnv[0].DraggingElement=$(Selector)
	})
	$DragEnv.mouseup(function(){
		$DragEnv[0].DraggingElement=null
		$('body',this).css('cursor', 'default')
	})
	
	$DragEnv.mousemove(function(e){
		var $Element=this.DraggingElement
		if(!$Element || $Element.length==0)
			return
		
		$('body',this).css('cursor', 'move')
		var MaxX=$DragEnv.width()-$Element.width()
		,MaxY=$DragEnv.height()-$Element.height()
		, XPos=e.pageX
		, YPos=e.pageY
		
		if($Element.css('direction')=='rtl')
			XPos-=$Element.width()
		
		XPos=XPos<0?0:(XPos>MaxX?MaxX:XPos)
		YPos=YPos<0?0:(YPos>MaxY?MaxY:YPos)
		
		$Element.css({
			position:'fixed', 
			top:YPos+'px',
			left:XPos+'px'
		})
	})
});