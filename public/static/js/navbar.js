$('#newsCatLink').on('click', function() {
    $('#newsCatLink').addClass('active');
    $('#newsLinkWrap').addClass('active');
    $('.menu-btn').on('click', function(){
        const menuItemPos  = $('#newsCatLink').position().top - 104;
        $('#newsLinkWrap').css('margin-top', menuItemPos);
    })
    $('.menu_inner').css('background-image', 'url("/static/images/events-bg.png")');
    
    $('#aboutCatLink').removeClass('active');
    $('#aboutLinkWrap').removeClass('active');
    $('#academyCatLink').removeClass('active');
    $('#academyLinkWrap').removeClass('active');
    $('#advisoryCatLink').removeClass('active');
    $('#advisoryLinkWrap').removeClass('active');
    $('#resourcesCatLink').removeClass('active');
    $('#resourcesLinkWrap').removeClass('active');
})