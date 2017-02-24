	/*就诊指导特效*/
	$(".m-fwtdC li").each(function(){
	$(this).hover(function(){
		$(this).find("a:first-child").stop().animate({top:"-30px"},200);
		$(this).find("a:last-child").stop().animate({top:"0px"},200);
		},function(){
		$(this).find("a:first-child").stop().animate({top:"0px"},200);
		$(this).find("a:last-child").stop().animate({top:"30px"},200);
			})
		})