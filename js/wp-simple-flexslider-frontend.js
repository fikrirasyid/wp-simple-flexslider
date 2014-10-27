jQuery(document).ready(function($){
	$('.wp-simple-flexslider').each(function(){
		$(this).flexslider({
			animation: "slide",
			selector: ".slides > .slide-item"
		});			
	});
});