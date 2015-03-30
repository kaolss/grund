/* 
/*
* Author: Karin H Olsson
* Author url: http://www.kobotolo.se
*/

jQuery(function($) {
	var $container = $('.masonry-page .content .masonry-content'); 
	$container.imagesLoaded( function(){
		$container.masonry({
                    columnWidth: '.grid-sizer',
                    gutter: '.gutter-width',
                    itemSelector: '.entry',
                    isAnimated: true			
			
		});
        });
        
        
        
    $("button").click(function(){
        $(".archivetitle").html(this.name);
        $(".entry").hide();
        
        $("."+this.id).show();
        $container.masonry();
    });
    
    $("input").click(function(){
        if (this.id ==='entry') {
            if (this.checked) {
                $("."+this.id).show();
                 $(':checkbox').each(function() {
                this.checked = false;                        
            });
                this.checked = true;                        
            
            }
        }
        else {     
            
            $(".entry").hide();
            $(':checkbox').each(function() {
                if (this.id ==='entry')               
                    this.checked = false;                        
                if (this.checked){
                    console.log("show ."+this.id);        
                    $("."+this.id).show();
                }
            
            });
  
            
        }
        $container.masonry();
        });
});