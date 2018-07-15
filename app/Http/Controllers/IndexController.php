<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Log;

class IndexController extends Controller
{
    public const DIR = 'record';
    public const FILE_SUFFIX_IMCOMPLETE = 'incomplete';
    public const FILE_SUFFIX_START = 'start';
    public const FILE_SUFFIX_FINISH = 'finish';
    public const LINE_SEPARATOR = "\r\n";

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show() {

        // 作業用ディレクトリの作成
        if (!file_exists(IndexController::DIR)) {
            mkdir(IndexController::DIR);
        }

        $data['file_name'] = $this->getIncompleteFile();
        return view('index', $data);
    }

    public function getInfo(Request $request)
    {
        // TODO DBから取得
        // TODO 所要時間も取得
        $response = array();
        $response["sl_cnt"] = 31;
        $response["op_cnt"] = 46;
        $response["2nd_cnt"] = 9;
        $response["3rd_cnt"] = 31;
        $response["anime_cnt"] = 10;
        $response["everyone_cnt"] = 4;
        $response["wt_cnt"] = 2;

        return Response::json($response);
    }

    public function getIncompleteFile() : ?string
    {
        $dir = self::DIR;
        $suffix = self::FILE_SUFFIX_IMCOMPLETE;
        foreach(glob("$dir/*$suffix*") as $file){
            if (is_file($file)){
                return $file;   // TODO フルパス？
            }
        }
        return null;
    }
}
