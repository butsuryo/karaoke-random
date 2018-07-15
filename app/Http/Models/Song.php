<?php
/**
 * Created by PhpStorm.
 * User: naguy
 * Date: 2018/07/10
 * Time: 21:44
 */

namespace App\Http\Models;


class Song
{

    /**
     * 歌手 TODO
     * @var
     */
    private $songer;

    /** 曲名
     * @var
     */
    private $title;

    /**
     * 曲の長さ（秒数）
     * @var float|int
     */
    private $sec;

    /**
     * DAM選曲番号
     * @var
     */
    private $damNumber;

    /**
     * JOY SOUND選曲番号
     * @var
     */
    private $joysoundNumber;


    /**
     * Song constructor.
     * @param $title
     * @param $damNumber
     * @param $joysoundNumber
     * @param $sec
     */
    public function __construct($title, $damNumber, $joysoundNumber, $sec)
    {
        $this->title = $title;
        $this->damNumber = $damNumber;
        $this->joysoundNumber = $joysoundNumber;

        if ($sec == null) {
            // TODO 設定がない場合はとりあえず平均的な4:00を設定
            $sec = '4:00';
        }
        if (strpos($sec,':') !== false) {
            $time = explode(':', $sec);
            $sec = ($time[0] * 60) + $time[1];
        }

        $this->sec = $sec;
    }

    /**
     * @return mixed
     */
    public function getDamNumber()
    {
        return $this->damNumber;
    }

    /**
     * @return mixed
     */
    public function getJoysoundNumber()
    {
        return $this->joysoundNumber;
    }

    /**
     * @return mixed
     */
    public function getSec()
    {
        return $this->sec;
    }

    /**
     * @return mixed
     */
    public function getSonger()
    {
        return $this->songer;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * オブジェクトの情報をCSVの一行に変換する
     * @return string
     */
    public function convertCsv()
    {
        return implode(',', array($this->getTitle(), $this->getDamNumber(), $this->getJoysoundNumber(), $this->getSec()));

    }

    /**
     * CSVの一行からオブジェクトを生成する
     * @param $row
     * @return Song|null
     */
    public static function newInstanceFromCsv($row)
    {
        if (strpos($row, ',') !== false) {
            $items = explode(',', $row);
            if (count($items) >= 4) {
                return new Song($items[0], $items[1], $items[2], $items[3]);
            }
        }
        return null;
    }

    /**
     * 引数に与えられた曲情報の合計タイム（秒）を返す
     * @param $songs
     * @return sec
     */
    public static function getTotalTime($songs)
    {
        $totalSec = 0;
        foreach ($songs as $song) {
            $totalSec += $song->getSec();
        }
        $totalTime = round($totalSec/60);
        return $totalTime;
    }
}