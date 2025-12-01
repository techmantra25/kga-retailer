$(document).ready(function(){
    $(".main__nav ul li a").each(function() {
        if ($(this).next().length > 0) {
            $(this).addClass("parent");
        };
    });
    $(".main__nav ul li").unbind('mouseenter mouseleave');
    $(".main__nav ul li a.parent").unbind('click').bind('click', function(e) {
        // must be attached to anchor element to prevent bubbling
        e.preventDefault();
        if($(this).parent("li").hasClass("active")) {
            $(this).parent("li").removeClass("active");
            return;
        }
        $(".main__nav ul li").removeClass('active');
        $(this).parent("li").parent().parent().addClass("active");
        $(this).parent("li").toggleClass("active");
    });

    $('.main__nav li.active').parent().parent().addClass('active');

    $('.menu_toggle').click(function(){
        $('.side__bar').toggleClass('hide_bar');
        $('.admin').toggleClass('show_full');
    });

    // var url = window.location;
    // console.log(url);
    // $('.main__nav a[href="'+url+'"]').parent().addClass('active');
    // $('.main__nav a[href="'+url+'"]').parents('li').addClass('active');
    // $('.main__nav a').filter(function(){
    //     return this.href==url;
    // }).parent().addClass('active');
    // $('.main__nav a').filter(function(){
    //     return this.href==url;
    // }).parents('li').addClass('active');
});
$(window).on('load', function () {
    setTimeout(function(){
        $('#app-preloader').addClass('fade');
    },1000);
});
// $(window).load(function() {
//     $('.main__nav li.active').parent().parent().addClass('active');
// });

// $(document).on("load",".main__nav li.active",function() {
//     $(this).parents('li').addClass('active');
// });

// ClassicEditor
// .create( document.querySelector( '#product_des' ) )
// .catch( error => {
//     console.error( error );
// });
// ClassicEditor
// .create( document.querySelector( '#product_short_des' ) )
// .catch( error => {
//     console.error( error );
// });