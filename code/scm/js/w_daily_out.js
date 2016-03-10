(function($){

    $(document).ready(function(){

        // bgcolor line
        $('.list_txt').hover(function(){
            $(this).addClass("list_txt_bg");
        },function(){
            $(this).removeClass("list_txt_bg");  
        });

	});

})(jQuery);