function loadCart(url){
    var img = $('<img />');
    img.attr('id', 'cart-loading');
    img.attr('src', '/bundles/bookshopbookshop/css/images/cartLoading.gif');
    $('#cart').add($('<img />').attr({'id': 'cart-loading', 'src': '/bundles/bookshopbookshop/css/images/cartLoading.gif'}));
    img.appendTo('#cart');
    $('#cart').load(url,function( response, status, xhr ) {
        $('#cart>img').hide();
    });
    
    
}


