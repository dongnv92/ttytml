(function($){
    $(document).ready(function(){
        $('.feature-img img').on('click',function(){
            $('#indexdm_img').addClass('active');
            var img_src = $(this).parent().attr('data-src');
            $('.img-demo').css('display','block');
            $('.img-demo').css('background-image','url('+img_src+')');
        })
    });
})(jQuery);