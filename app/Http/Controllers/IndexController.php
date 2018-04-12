<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Log;

class IndexController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show() {
        return view('index');
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

        return Response::json($response);
    }
}
