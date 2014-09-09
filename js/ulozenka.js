function selPobocka(num) {

    var el = document.getElementById('selulozenka');
    var sel = el.options[el.selectedIndex].value;

    if (sel) {
        pobockaSelected = 1;
    }
    else {
        pobockaSelected = 0;
    }


    $.ajax({
        type: 'POST',
        url: baseDir + 'modules/ulozenka/ulozenka-ajax.php',
        async: true,
        cache: false,
        dataType: "json",
        data: {selpobocka: sel},
        success: function(jsonData) {
            if (jsonData['version'] == 160) {
                var prices = $('td.delivery_option_price');
                $(prices[num]).text(jsonData['cena']);
            }
            else if (jsonData['version'] == 146) { // verze 1.4.6. opc
                var td = $('td.carrier_price').get(num);
                //  $( td ).children().first().text(jsonData['cena']);
                $(td).html(jsonData['cena']);
            }
            else {
                var prices = $('.delivery_option_price');
                $(prices[num]).text(jsonData['cena']);
            }



            if (jsonData['refresh'] == 1) {
                if (jsonData['opc'] == 1 && ulozenkaActive) {
                    $("#HOOK_PAYMENT").html(jsonData['platba']);
                }

                if (jsonData['allow'] == 1)
                    $("#pobockadetail").show();
                else
                    $("#pobockadetail").hide();
            }
        },
        error: function() {
            alert('ERROR: change price');
        }
    });
}







function fbox() {
    var el = document.getElementById('selulozenka');
    var sel = el.options[el.selectedIndex].value;

    if (sel) {
        var url = baseDir + 'modules/ulozenka/pobocka.php?code=' + sel;
        $.fancybox({
            type: 'iframe',
            href: url,
            'width': 500,
            'height': 500,
        });
    }
}



