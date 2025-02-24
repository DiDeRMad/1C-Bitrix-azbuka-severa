$(document).ready(function() {

    $('.js-collapse-block__header').on('click', function(e) {
        e.preventDefault();
        if ($(this).hasClass('collapse-block__header_active')) {
            $(this).removeClass('collapse-block__header_active');
            $(this).next('.collapse-block__body').slideUp(300);
        } else {
            $(this).addClass('collapse-block__header_active');
            $(this).next('.collapse-block__body').slideDown(300);
            if ($(this).hasClass('js-with-slick')) {
                $('.reviews-slider').slick('setPosition');
            }
        }
    });

    $('.show_all_review').on('click', function () {
        const textOpen = 'Читать далее';
        const textClose = 'Скрыть';

        var state = $(this).data('state');
        var textBlock = $(this).parent().find('.reviews-slider__description');

        if (state === 'close') {
            $(this).html(textClose);
            $(this).data('state', 'open')
            textBlock.css('height', '100%');
        } else {
            $(this).html(textOpen);
            $(this).data('state', 'close')
            textBlock.css('height', '94px');
        }
    })

    $('.js-modal-address-edit').on('click', function() {
        $('#modal-address-name').removeClass('hidden');
        $('.js-modal-address-edit').addClass('hidden');
    });

    $('.js-modal-address-confirm').on('click', function() {
        $('#modal-address-name').addClass('hidden')
        $('.js-modal-address-edit').removeClass('hidden');
    });

    $('.datepicker').datepicker({
        orientation: 'bottom',
        format: "dd.mm.yyyy",
        weekStart: 1
    });

    var edit = false;

    $(document).on('click', '.add-reciept', function(e) {
        e.preventDefault();
        let recId = $(this).attr('data-reciept');
        $.ajax({
            url: '/local/ajax/addrecieptfav.php',
            method: 'post',
            dataType: 'html',
            data: {
                'recId': recId
            },
            success: function(data){
                if (data == '"add"') {
                } else if (data == '"delete"') {
                    $('.add-reciept[data-reciept="' + recId + '"]').parent().css('display', 'none');
                }
            }
        });
    })

    $(document).on('click', '.js-address-del', function() {
        $(this).parent().parent().parent().remove();
    })

    $('#address-form').on('submit', function(e) {
        e.preventDefault();
        let addressName = $('input[name="address_name"]').val();
        if (addressName == 'Адрес') {
            addressName += ' 2';
        }
        let town = $('select[name="town"]').val();
        let street = $('input[name="street"]').val();
        let home = $('input[name="home"]').val();
        let entrance = $('input[name="entrance"]').val();
        let floor = $('input[name="floor"]').val();
        let kv = $('input[name="kv"]').val();
        let fulLAddress = '';

        if (town.match('/Россия/')) {
            fulLAddress = 'г. ' + town + ', ул. ' + street + ', дом ' + home + ', подъезд ' + entrance + ', этаж ' + floor + ', квартира ' + kv;
        } else {
            fulLAddress = street + ', дом ' + home + ', подъезд ' + entrance + ', этаж ' + floor + ', квартира ' + kv;
        }
        if (edit) {
            $('a[data-key="' + edit + '"]').parent().parent().find('input[name="UF_UF_ADDRESSES_NAMES[]"]').val(addressName);
            $('a[data-key="' + edit + '"]').parent().parent().find('input[name="UF_ADDRESSES[]"]').val(fulLAddress);
        } else {
            var htmlAdd = '<div class="col-lg-6">\n' +
                '                    <div class="form-group">\n' +
                '                        <div class="form-group__tools">\n' +
                '                            <label class="form-label">' + addressName + '</label>\n' +
                '                            <a class="form-group__edit icon-link icon-link_edit" href="#" data-modal="true" data-modal-id="#modal-address-add"></a>\n' +
                '                            <a class="form-group__del icon-link icon-link_close js-address-del" href="#"></a>\n' +
                '                        </div>\n' +
                '                                                <input type="hidden" name="UF_UF_ADDRESSES_NAMES[]" value="' + addressName + '" />\n' +
                '                        <input type="text" class="input form-control" name="UF_ADDRESSES[]" value="' + fulLAddress + '" readonly>\n' +
                '                    </div>\n' +
                '                </div>';
            $('.js-addresses-list').append(htmlAdd);
        }
        edit = false;
        $('#modal-address-add').modal('hide');
        $('.blocker.current').css('display', 'none');
        $('body').css('overflow', 'auto');
    })

    $(document).on('click','.js-address-edit', function() {
        edit = $(this).attr('data-key');
        let addrName = $(this).parent().parent().find('input[name="UF_UF_ADDRESSES_NAMES[]"]').val();
        let strAddr = $(this).parent().parent().find('input[name="UF_ADDRESSES[]"]').val();
        $('#address_name').val(addrName);
        let arAddr = strAddr.split(',');
        let val = '';
        arAddr.forEach(function(el) {
            let check = el.split(' ');
            let pref = check[0];
            if (pref == '') {
                pref = check[1];
            }
            switch(pref) {
                case 'г.':
                    delete(check[0]);
                    val = check.join(' ');
                    $('select[name="town"] option[value="' + val + '"]').attr('selected', 'selected');
                    $('select[name="town"]').change();
                    break;
                case 'ул.':
                    delete(check[0]);
                    delete(check[1]);
                    val = check.join(' ');
                    $('input[name="street"]').val(val);
                    break;
                case 'дом':
                    delete(check[0]);
                    delete(check[1]);
                    val = check.join('');
                    $('input[name="home"]').val(val);
                    break;
                case 'подъезд':
                    delete(check[0]);
                    delete(check[1]);
                    val = check.join('');
                    $('input[name="entrance"]').val(val);
                    break;
                case 'этаж':
                    delete(check[0]);
                    delete(check[1]);
                    val = check.join('');
                    $('input[name="floor"]').val(val);
                    break;
                case 'квартира':
                    delete(check[0]);
                    delete(check[1]);
                    val = check.join('');
                    $('input[name="kv"]').val(val);
                    break;
            }
        })
    })

    $('.js-order-repeat').on('click', function(e) {
        e.preventDefault();
        let id = $(this).parent().attr('data-id');
        //console.log(id);
        $.ajax({
            url: '/local/ajax/repeatorder.php',
            type: 'POST',
            data: {
                id: id
            },
            success: function(res) {
                alert('Заказ отправлен!');
                location.reload();
            }
        })
    })

    var token = "0cf6341c9082bb03bf06a5510feb0668453f0ee9";//"4b4e4c6012265a4397eeefc265a420d57252f57b";
    $('#address_main').on('keydown', function (){
        $('#address_selector_select').html('');
        var query = $(this).val();
        var url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address";

        var options = {
            method: "POST",
            mode: "cors",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": "Token " + token
            },
            body: JSON.stringify({query: query, locations:[{country:"*"}]})
        };

        fetch(url, options)
            .then(response => response.text())
            .then(function(result){
                var suggestions = JSON.parse(result)['suggestions'];
                $('#address_selector_select').append('<option class="address_selector_element" value="">Не выбрано</option>');
                suggestions.forEach(function(arElement) {
                    if (arElement['data']['geo_lat']) {
                        var elem = '<option class="address_selector_element" value="' + arElement['value'] + '" data-coordinates="' + arElement['data']['geo_lat'] + ',' + arElement['data']['geo_lon'] + '">' + arElement['value'] + '</option>';
                    } else {
                        var elem = '<option class="address_selector_element" value="' + arElement['value'] + '" data-coordinates="">' + arElement['value'] + '</option>';
                    }
                    $('#address_selector_select').append(elem);
                });
                if (suggestions.length > 0) {
                    $('#address_selector').css('display', 'block');
                    $('#address_selector').attr('data-full', '1');
                    $('#address_selector').attr('data-open', '1');
                } else {
                    $('#address_selector').css('display', 'none');
                    $('#address_selector').attr('data-full', '0');
                    $('#address_selector').attr('data-open', '0');
                }

                $('.address_selector_element').on('click', function(){
                    $('#address_selector').css('display', 'none');
                    $('#address_selector').attr('data-open', '0');
                    var coords = $(this).attr('data-coordinates');
                    $('input[name="coordinates"]').val(coords);
                })
            })
            .catch(error => console.log("error =>" + error));

        if (query == '') {
            $('#address_selector').css('display', 'none');
            $('#address_selector').attr('data-open', '0');
        }
    });

    $('#address_selector_select').on('change', function(){
        $('#address_main').val($(this).val());
        $('#save_profile').removeAttr('disabled').css('background-color', '#1b389e');
    });

    $(document).on('keydown','input[name="street"]', function (){
        let cityname = $('input[name="town"]').val();
        $('input[name="street"]').suggestions({
            token: token,
            type: "ADDRESS",
            constraints: {
                locations: {region: cityname},
            },
            restrict_value: true,
        });
    })
});

function removeAllFromWish()
{
    $.ajax({
        url: '/local/ajax/clearwishlist.php',
        method: 'post',
        dataType: 'html',
        data: {},
        success: function(data){
            let html = ' <div class="form-group">\n' +
                '        <div class="form-text form-text_lg">В списке пока нет ни одного избранного товара</div>\n' +
                '    </div>\n' +
                '    <a href="/catalog/" class="btn btn--blue">Перейти в каталог</a>';
            $('#lk-wishlist').html(html);
        }
    });
}