require('./bootstrap');

$(function () {
    if (typeof builderPage != 'undefined') {
        let part = $('.part-dropdown');
        let total = 0;
        part.select2({
            width: '100%',
            placeholder: 'Select one',
            allowClear: true,
            theme: 'bootstrap4',
            ajax: {
                url: '/api/parts',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        type: $(this).attr('data-part-type'),
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.data,
                        pagination: {
                            more: (params.page * 10) < data.message
                        }
                    };
                },
                cache: true,
            }
        });
        part.change(function (e) {
            recalculate();
        });

        $('#ram-counter').change(function (e) {
            recalculate();
        });

        function recalculate() {
            // Reset the total counter
            total = 0;

            // Parse dropdowns
            $('.part-dropdown').each(function () {
                let data = $(this).select2('data');
                let type = $(this).attr('data-part-type');
                let price = 0;
                if (typeof data[0].price == 'undefined') {
                    price = $(this).children('option:selected').attr('data-price');
                    if (typeof price == 'undefined') {
                        price = 0;
                    }
                } else {
                    price = data[0].price;
                }
                if (data[0].id != '') {
                    if (type === 'RAM') {
                        total += parseFloat(price) * parseInt($('#ram-counter').val());
                    } else {
                        total += parseFloat(price);
                    }
                }
            });
            total = $.number(total, 2);
            $('.build-total').text(total);
        }
    }

    $('form#build-form').submit(function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Please wait...',
            text: "We're saving your build.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            },
            onOpen: () => {
                $.ajax({
                    'url': $(this).attr('action'),
                    'method': 'POST',
                    'data': $(this).serialize()
                }).done(function (data) {
                    Swal.close();
                    Swal.fire({
                        title: 'Yay!',
                        text: 'Your build has been saved.',
                        icon: 'success',
                        onClose: () => {
                            window.location.href = '/' + data.data.hash;
                        }
                    });
                }).fail(function (e) {
                    Swal.close();
                    Swal.fire({
                        title: 'Uh-oh.',
                        text: e.responseJSON.message,
                        icon: 'error'
                    });
                });
            }
        });
    });
    $('.show-login-modal').click(function (e) {
        e.preventDefault();
        $('#modalLogin').modal();
    });
    $('#modalLogin').on('shown.bs.modal', function () {
        $('#username').focus();
    });
    $('#login-form').submit(function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Please wait...',
            text: "We're logging you in.",
            allowOutsideClick: false,
            allowEscapeKey: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            },
            onOpen: () => {
                $.ajax({
                    'url': $(this).attr('action'),
                    'method': 'POST',
                    'data': $(this).serialize()
                }).done(function (data) {
                    Swal.close();
                    location.reload();
                }).fail(function (e) {
                    Swal.close();
                    Swal.fire({
                        title: 'Uh-oh.',
                        text: e.responseJSON.message,
                        icon: 'error'
                    });
                });
            }
        });
    });
    $('.delete-build').click(function (e) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to restore this build.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.value) {
                $('#delete-form').submit();
            }
        })
    });
});