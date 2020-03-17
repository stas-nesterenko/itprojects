$(document).on('submit', "form", function (e) {
    e.preventDefault();

    var _this = $(this);

    $.ajax({
        url: $(this).attr('action'),
        method: $(this).attr('method'),
        data: $(this).attr('method') === 'get' ? $(this).serializeArray() : new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: $(this).attr('method') === 'get',
        dataType: 'json',
        success: function (response) {
            $(_this).find('.form-group .invalid-feedback').text('');
            $(_this).find('input').removeClass('is-invalid');

            if (response.field_error) {
                for (e in response.field_error) {
                    $(_this).find('input[name^="' + e + '"]').addClass('is-invalid');
                    $(_this).find('input[name^="' + e + '"]').siblings('.form-group .invalid-feedback').html(response.field_error[e]);
                }
            }
            if (response.location){
                window.location.href = response.location;
                return false;
            }
        }
    });
});
