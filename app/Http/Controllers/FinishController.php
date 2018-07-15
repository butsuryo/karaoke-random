<?php

namespace App\Http\Controllers;

use App\Http\Models\Song;
use Illuminate\Http\Request;
use Response;
use Log;

class FinishController extends Controller
{

    private $startTime;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request) {

        $fileTimestamp = $request->fileTimestamp;
        $finishData = \Session::get('finish_data');

        // ダイレクトアクセスの場合
        if ($finishData == null) {
            $filePath = LotteryController::createFileFullPath(IndexController::DIR, $fileTimestamp . IndexController::FILE_SUFFIX_FINISH);
            $songs = $this->inputCsv($filePath);
            $finishData['songs'] = $songs;

            $dt = new \DateTime($this->startTime);    // 開始時間
            $date = new \DateTime();
            $date->setTimestamp(filemtime($filePath));    // ファイルが最終更新された時間＝終了時間
            $timeLabel =  $dt->diff($date)->format('%h時間%i分%s秒');
            $finishData['time'] = $timeLabel;
        }
        return view('finish', $finishData);
    }

    /**
     * ファイルから曲情報を取得する
     * @param $filePath
     * @return array|null
     */
    public function inputCsv($filePath) : ?array
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $file = file($filePath);
        $songs = array();
        foreach ($file as $index => $row) {
            if ($index === 0) {
                $header = explode(',', $row);
                $this->startTime = $header[0];
                continue;
            }
            if ($row == null || trim($row) == '' || strpos($row, ',') === false) {
                continue;
            }
            $row = str_replace(IndexController::LINE_SEPARATOR, '', $row);
            $song = Song::newInstanceFromCsv($row);
            if ($song != null) {
                $songs[] = $song;
            }
        }
        return $songs;
    }

}
