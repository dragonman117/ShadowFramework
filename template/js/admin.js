function get_page(url, id){
    $.get(url, function(data){
        $('#'+id).trigger('loaded', [data]);
    })
}

function save_data(me){
    var form = $(me).attr('data-form-id');
    var url = $(form).attr('data-url');
    $(form).addClass('zone-out');
    var send_data = new FormData($(form)[0]);
    send_form(url, form, send_data);
}

function send_form(url, id, data){
    $.ajax({
        url: url,
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST',
        success: function(data){
            $(id).trigger('saved', [data]);
        }
    })
}