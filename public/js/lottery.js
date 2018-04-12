$info = [];

$(function() {


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

