function loadCart(url) {
    var img = $('<img />');
    img.attr('id', 'cart-loading');
    img.attr('src', '/bundles/bookshopbookshop/css/images/cartLoading.gif');
    $('#cart').add($('<img />').attr({'id': 'cart-loading', 'src': '/bundles/bookshopbookshop/css/images/cartLoading.gif'}));
    img.appendTo('#cart');
    $('#cart').load(url, function(response, status, xhr) {
        $('#cart>img').hide();
    });


}
function loadMessages(url) {
    $('#messages').load(url);
}
function deleteFromCart(url, msg_path, cart_path) {
    $.ajax(url).done(function() {
        loadMessages(msg_path);
        loadCart(cart_path);
    });
}

function add(url, qty, prodId, msgs_url, cart_url) {
    $.ajax({
        type: "POST",
        url: url,
        data: {quantity: qty, productid: prodId}
    })
            .done(function() {
        loadMessages(msgs_url);
        loadCart(cart_url);
    });
}

//function search(url,keyword){
////    alert(url+'?&'+keyword);
//    $.ajax({
//        type: "GET",
//        url: url,
//        data: {q : keyword}
//    }).done(function(data, textStatus, jqXHR){
//        alert(data+" "+textStatus);
//    });
//}



