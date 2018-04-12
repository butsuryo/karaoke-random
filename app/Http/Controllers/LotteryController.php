<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;

class LotteryController extends Controller
{
    public function start(Request $request) {

        // TODO DBからすべての曲データ取得
        $data = $this->getAllSong();

        // TODO 抽選

        //$request = Input::get();
        $selectCheckes = $request['selectcheck'] ?? [];
        $allCount = 0;
        foreach ($selectCheckes as $selected) {
            if ($selected == 'sl') {
                $allCount += $data['sl_cnt'];   // TODO DBでとってきた後カテゴリーごとにサマリーが必要？モデル作成の必要が出てきそう
            }
            if ($selected == 'op') {
                $allCount += $data['op_cnt'];   // TODO DBでとってきた後カテゴリーごとにサマリーが必要？モデル作成の必要が出てきそう
            }
            if ($selected == '2nd') {
                $allCount += $data['2nd_cnt'];   // TODO DBでとってきた後カテゴリーごとにサマリーが必要？モデル作成の必要が出てきそう
            }
        }

        $currentCount = 1;  // TODO

        $is_mask = $request['selectradio'] == 2;

        $lotteryData = ['song'       => date('Ymd_H:i:s') . '_song',
                        'cnt'        => $currentCount,
                        'all_cnt'    => $allCount,
                        'remain_cnt' => $allCount - $currentCount];
        return view('lottery', $lotteryData);
    }

    public function lottery(Request $request) {

        $currentCount = $request->cnt;
        if ($request->input('next')) {
            $currentCount ++;
        } else if ($request->input('skip')){
            // skipの場合は数値に変化なし
        }

        $lotteryData = ['song'       => date('Ymd_H:i:s') . '_song',
            'cnt'        => $currentCount,
            'all_cnt'    => 100,
            'remain_cnt' => 100 - $currentCount];
        return view('lottery', $lotteryData);
    }

    private function getAllSong() {
        // TODO
        $response = array();
        $response["sl_cnt"] = 31;
        $response["op_cnt"] = 46;
        $response["2nd_cnt"] = 9;
        return $response;

    }
}
