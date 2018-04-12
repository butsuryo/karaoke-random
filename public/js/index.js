$info = [];

$(function() {

    var deferred = getInfo();
    deferred.done(function(){
        changeText();
    });

    // 「全てにチェック」
    $('input[name="all"]').change(function() {
        if ($(this).prop('checked')) {
            $('input[name="selectcheck[]"]').prop('checked', true);
        } else {
            $('input[name="selectcheck[]"]').prop('checked', false);
        }
        changeText();
    });

    // それぞれのチェック
    $('input[name="selectcheck[]"]').change(function(){
        $isAllSelected = ($('[name="selectcheck[]"]:not(:checked)').length == 0);
        $('input[name="all"]').prop('checked', $isAllSelected);
        changeText();
    });
});


function changeText() {

    $count = 0;
    $time = 0;

    $('[name="selectcheck[]"]:checked').each(function(i, elem) {
        $selectedVal = $(elem).val();
        if ($selectedVal == 'sl') {
            $count += $info['sl_cnt'];
        }
        if ($selectedVal == 'op') {
            $count += $info['op_cnt'];
        }
        if ($selectedVal == '2nd') {
            $count += $info['2nd_cnt'];
        }
    });

    $message = 'チェックされているカウントの合計は' + $count + 'です。';
    $('#message').text($message);
}

function getInfo() {

    var deferred = new $.Deferred();

    $.ajax({
        type: 'get',
        datatype: 'json',
        url: 'api/info'
    })
    .done(function(data){ //ajaxの通信に成功した場合
        $info = data;
    })
    .fail(function(data){ //ajaxの通信に失敗した場合
        alert("ajax error!");
    }).always(function(){
        //ajax処理を終了したことをDeferredオブジェクトに通知
        deferred.resolve();
    });

    return deferred;
}

