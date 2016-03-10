(function($){

    $(document).ready(function(){

        // bgcolor line
        $('.list_over').hover(function(){
            $(this).addClass("list_over_bg");
        },function(){
            $(this).removeClass("list_over_bg");  
        });

	});

})(jQuery);