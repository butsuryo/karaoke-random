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
        $keys = ['sl', 'op', '2nd', '3rd', 'anime', 'everyone'];

        $.each($keys, function(i, $key) {
            if ($selectedVal == $key) {
                $count += $info[$key + '_cnt'];
                console.log($key + $count);
            }
        });
    });

    $minutes = 90;
    $currentTime = new Date();
    $endtime = formatDate(new Date($currentTime.setMinutes($currentTime.getMinutes() + $minutes)), 'hh:mm');
    $message = '単純再生時間は' + $minutes + '分です。(合計' + $count + '曲)'
        + "<br>" + '今から開始すると、終了は' + $endtime + 'ごろになります。';
    $('#message').html($message);
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


/**
 * TODO 共通化
 * https://qiita.com/osakanafish/items/c64fe8a34e7221e811d0
 * 日付をフォーマットする
 * @param  {Date}   date     日付
 * @param  {String} [format] フォーマット
 * @return {String}          フォーマット済み日付
 */
function formatDate(date, format) {
    if (!format) format = 'YYYY-MM-DD hh:mm:ss.SSS';
    format = format.replace(/YYYY/g, date.getFullYear());
    format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));
    format = format.replace(/DD/g, ('0' + date.getDate()).slice(-2));
    format = format.replace(/hh/g, ('0' + date.getHours()).slice(-2));
    format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2));
    format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2));
    if (format.match(/S/g)) {
        var milliSeconds = ('00' + date.getMilliseconds()).slice(-3);
        var length = format.match(/S/g).length;
        for (var i = 0; i < length; i++) format = format.replace(/S/, milliSeconds.substring(i, i + 1));
    }
    return format;
};

