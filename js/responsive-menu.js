jQuery(function( $ ){

	$("header .genesis-nav-menu, .nav-primary .genesis-nav-menu").addClass("responsive-menu").before('<div class="responsive-menu-icon"></div>');

	$(".responsive-menu-icon").click(function(){
		$(this).next(".genesis-nav-menu, .nav-primary ").slideToggle();
            });

	$(window).resize(function(){
		if(window.innerWidth > 800) {
			$("header .genesis-nav-menu, .nav-primary .genesis-nav-menu, nav .sub-menu").removeAttr("style");
			$(".responsive-menu > .menu-item").removeClass("menu-open");
		}
	});

	$(".responsive-menu > .menu-item").click(function(event){
            if (event.target !== this)
            return;
                $(this).find(".sub-menu:first").slideToggle(function() {
                $(this).parent().toggleClass("menu-open");                
            });
	return false;});

});