<?php

namespace App\Http\Controllers;

use App\Http\Models\Song;
use Illuminate\Http\Request;
use Input;
use Illuminate\Support\Facades\Redirect;

class LotteryController extends Controller
{

    /**
     * @var bool
     */
    private $slFlg = false;
    /**
     * @var bool
     */
    private $opFlg = false;
    /**
     * @var bool
     */
    private $secondFlg = false;
    /**
     * @var bool
     */
    private $thirdFlg = false;
    /**
     * @var bool
     */
    private $animeFlg = false;
    /**
     * @var bool
     */
    private $everyoneFlg = false;
    /**
     * @var bool
     */
    private $wtFlg = false;

    private $startTime;
    private $isMask = true;


    public function restart(Request $request)
    {
        $fileName = $request['incomplete_file_name'];
        $filePath = $this->createFileFullPath(IndexController::DIR, $fileName);
        $songs = $this->inputCsv($filePath);

        // 終了判定
        if ($songs == null || count($songs) === 0) {
            // TODO 終了処理
            // 開始時の全曲を取得する
            $startFilePath = str_replace(IndexController::FILE_SUFFIX_IMCOMPLETE, IndexController::FILE_SUFFIX_START, $filePath);
            $songs = $this->inputCsv($startFilePath);

            // 開始ファイルを結果ファイルとして保存する
            $finishFilePath = str_replace(IndexController::FILE_SUFFIX_START, IndexController::FILE_SUFFIX_FINISH, $startFilePath);
            $this->renameFile($startFilePath, $finishFilePath);

            // 途中ファイルを削除する
            $this->deleteFile($filePath);

            $dt = new \DateTime($this->startTime);
            $now = new \DateTime();
            $timeLabel =  $dt->diff($now)->format('%h時間%i分%s秒');

            $finishData = array('songs' => $songs, 'time' => $timeLabel);
            return view('finish', $finishData);
        }

        $song = array_pop($songs);
        $header = $this->startTime . ',' . ($this->isMask ? '1' : '0');
        $this->outputCsv($songs, $filePath, $header);    // 1曲引いてファイルを更新

        // 残り時間の計算
        $totalTime = Song::getTotalTime($songs);
        $label = '';
        if ($totalTime >= 60) {
            $label .= floor($totalTime/60) . '時間' . $totalTime%60 . '分';
        } else {
            $label = $totalTime . '分';
        }

        // 終了予定時間の計算
        $finishTime = date("Y-m-d H:i:s",strtotime("+$totalTime minute"));

        // 今が何曲目だったか、全曲情報を取得して算出する
        $startFilePath = str_replace(IndexController::FILE_SUFFIX_IMCOMPLETE, IndexController::FILE_SUFFIX_START, $filePath);
        $allSongs = $this->inputCsv($startFilePath);
        $allCount = count($allSongs);
        $currentCount = count($allSongs) - count($songs);


        $lotteryData = [
            'song'       => $song,
            'cnt'        => $currentCount,
            'all_cnt'    => $allCount,
            'remain_cnt' => $allCount - $currentCount,
            'total_time'   => $totalTime,
            'remain_label' => $label,
            'finish_time'  => $finishTime,
            'file_name'  => $fileName,
            'is_mask'   => $this->isMask,
//            'start_time'   => $request['start_time'],   // TODO ファイルに出力すれば要らなくなる
        ];
        return view('lottery', $lotteryData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function start(Request $request)
    {
        $selectCheckes = $request['selectcheck'] ?? [];
        foreach ($selectCheckes as $selected) {
            if ($selected == 'sl') {
                $this->slFlg = true;
            }
            if ($selected == 'op') {
                $this->opFlg = true;
            }
            if ($selected == '2nd') {
                $this->secondFlg = true;
            }
            if ($selected == '3rd') {
                $this->thirdFlg = true;
            }
            if ($selected == 'anime') {
                $this->animeFlg = true;
            }
            if ($selected == 'wt') {
                $this->wtFlg = true;
            }
            if ($selected == 'everyone') {
                $this->everyoneFlg = true;
            }
        }

        // 初回のみ
        $songs = $this->getAllSong();
        $allCount = count($songs);


        $currentCount = 1;  // TODO

        $isMask = $request['selectradio'][0] == 2;

        // popする前に出力する
        $current = date('YmdHis');
        $fileName =  $current. IndexController::FILE_SUFFIX_START;
        $filePath = $this->createFileFullPath(IndexController::DIR, $fileName);
        // 1行目のヘッダ（現在時間、曲名隠すフラグ）
        $header = $current . ',' . ($isMask ? '1' : '0');
        $this->outputCsv($songs, $filePath, $header);

        // 先頭から一曲選曲
        $song = array_pop($songs);

        // popしてから出力する
        $fileName = str_replace(IndexController::FILE_SUFFIX_START, IndexController::FILE_SUFFIX_IMCOMPLETE, $fileName);
        $filePath = str_replace(IndexController::FILE_SUFFIX_START, IndexController::FILE_SUFFIX_IMCOMPLETE, $filePath);
        $this->outputCsv($songs, $filePath, $header);

        // 残り時間の計算
        $totalTime = Song::getTotalTime($songs);
        $label = '';
        if ($totalTime >= 60) {
            $label .= floor($totalTime/60) . '時間' . $totalTime%60 . '分';
        } else {
            $label = $totalTime . '分';
        }

        // TODO ファイルの1行目とかに開始時刻を保持しておく
        $startTime = date("Y-m-d H:i:s");

        // 終了予定時間の計算
        $finishTime = date("Y-m-d H:i:s",strtotime("+$totalTime minute"));

        $lotteryData = ['song'         => $song,
                        'cnt'          => $currentCount,
                        'all_cnt'      => $allCount,
                        'remain_cnt'   => $allCount - $currentCount,
                        'total_time'   => $totalTime,
                        'remain_label' => $label,
                        'finish_time'  => $finishTime,
                        'file_name'    => $fileName,
                        'is_mask'   => $isMask,
//                        'start_time'   => $startTime,   // TODO ファイルに出力すれば要らなくなる
        ];
        return view('lottery', $lotteryData);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lottery(Request $request) {

        $currentCount = $request->cnt;
        if ($request->input('next')) {
            $currentCount ++;
        } else if ($request->input('skip')){
            // skipの場合は数値に変化なし
        }

        $fileName = $request['file_name'];
        $filePath = $this->createFileFullPath(IndexController::DIR, $fileName);
        $songs = $this->inputCsv($filePath);

        // 終了判定
        if ($songs == null || count($songs) === 0) {
            // TODO 終了処理
            // 開始時の全曲を取得する
            $startFilePath = str_replace(IndexController::FILE_SUFFIX_IMCOMPLETE, IndexController::FILE_SUFFIX_START, $filePath);
            $songs = $this->inputCsv($startFilePath);

            // 開始ファイルを結果ファイルとして保存する
            $finishFilePath = str_replace(IndexController::FILE_SUFFIX_START, IndexController::FILE_SUFFIX_FINISH, $startFilePath);
            $this->renameFile($startFilePath, $finishFilePath);

            // 途中ファイルを削除する
            $this->deleteFile($filePath);

            $dt = new \DateTime($this->startTime);
            $now = new \DateTime();
            $timeLabel =  $dt->diff($now)->format('%h時間%i分%s秒');

            $finishData = array('songs' => $songs, 'time' => $timeLabel);
            return redirect("/finish/" . str_replace(IndexController::FILE_SUFFIX_IMCOMPLETE, '', $fileName))->with('finish_data', $finishData);
        }

        $song = array_pop($songs);
        $header = $this->startTime . ',' . ($this->isMask ? '1' : '0');
        $this->outputCsv($songs, $filePath, $header);    // 1曲引いてファイルを更新

        // 残り時間の計算
        $totalTime = Song::getTotalTime($songs);
        $label = '';
        if ($totalTime >= 60) {
            $label .= floor($totalTime/60) . '時間' . $totalTime%60 . '分';
        } else {
            $label = $totalTime . '分';
        }

        // 終了予定時間の計算
        $finishTime = date("Y-m-d H:i:s",strtotime("+$totalTime minute"));

        $allCount = $request['all_count'];
        $lotteryData = [
            'song'       => $song,
            'cnt'        => $currentCount,
            'all_cnt'    => $allCount,
            'remain_cnt' => $allCount - $currentCount,
            'total_time'   => $totalTime,
            'remain_label' => $label,
            'finish_time'  => $finishTime,
            'file_name'  => $fileName,
            'is_mask'   => $this->isMask,
//            'start_time'   => $request['start_time'],   // TODO ファイルに出力すれば要らなくなる
        ];
        return view('lottery', $lotteryData);
    }

    /**
     * 現在の曲情報をファイルに出力する
     * @param $songs
     * @param $filePath
     * @param $header
     */
    public static function outputCsv($songs, $filePath, $header)
    {
        $contents = array();
        $contents[] = $header . IndexController::LINE_SEPARATOR;
        foreach ($songs as $song) {
            /** @var Song $song */
            $row = $song->convertCsv();
            if ($row != null) {
                $contents[] = $row . IndexController::LINE_SEPARATOR;
            }
        }
        file_put_contents($filePath, $contents);
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
                $this->isMask = ($header[1] === '1');
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

    /**
     * 指定したファイルをリネームする
     * @param $filePath
     */
    public static function renameFile($filePath, $newFilePath)
    {
        rename($filePath, $newFilePath);
    }

    /**
     * 指定したファイルを削除する
     * @param $filePath
     */
    public static function deleteFile($filePath)
    {
        unlink($filePath);
    }

    public static function createFileFullPath($dir, $fileName)
    {
        if (ends_with($dir, '/')) {
            if (starts_with($fileName, '/')) {
                return $dir . substr($fileName, 1);
            } else {
                return $dir . $fileName;
            }
        } else {
            if (starts_with($fileName, '/')) {
                return $dir . $fileName;
            } else {
                return $dir . '/' . $fileName;
            }
        }
    }

    /**
     * @return array
     */
    public function getAllSong() {
        // TODO
//        $response = array();
//        $response["sl_cnt"] = 31;
//        $response["op_cnt"] = 46;
//        $response["2nd_cnt"] = 9;

        $allSongs = array();
        $res[] = new Song('BRAND NEW FIELD', '3730-64', '673880', '6:01');
        $res[] = new Song('Planet scape', '3732-62', '673787', '5:15');
        $res[] = new Song('GLORIA MOMENT', '3769-19', '690556', '4:35');
        $res[] = new Song('Over AGAIN', '4400-68', '425657', '4:14');
        $res[] = new Song('STARLIGHT CELEBRATE!', '3730-68', '673789', '4:51');
        $res[] = new Song('DRAMATIC NONFICTION', '3732-61', '673790', '3:50');
        $res[] = new Song('MOON NIGHTのせいにして', '3765-24', '686951', '5:03');
        $res[] = new Song('ARRIVE TO STAR', '4400-75', '425825', '4:59');
        $res[] = new Song('スマイル・エンゲージ', '3738-44', '676587', '5:30');
        $res[] = new Song('想いはETERNITY', '3738-45', '676586', '5:00');
        $res[] = new Song('Fun! Fun! Festa!', '3768-31', '688836', '4:52');
        $res[] = new Song('TOMORROW DIAMOND', '4060-83', '424869', '4:47');
        $res[] = new Song('HIGH JUMP NO LIMIT', '3738-46', '676571', '4:40');
        $res[] = new Song('JOKER/オールマイティ', '3738-47', '676570', '5:25');
        $res[] = new Song('OUR SONG-それは世界でひとつだけ-', '3765-25', '686950', '4:41');
        $res[] = new Song('Sunset★Colors', '4400-70', '425826', '4:05');
        $res[] = new Song('VICTORY BELIEVER', '3747-36', '679628', '4:30');
        $res[] = new Song('Pleasure Forever...', '3747-35', '687405', '4:31');
        $res[] = new Song('LEADING YOUR DREAM', '3769-27', '690189', '4:21');
        $res[] = new Song('AFTER THE RAIN', '4060-81', '424871', '4:48');
        $res[] = new Song('∞ Possibilities', '3747-31', '681297', '4:50');
        $res[] = new Song('Study Equal Magic!', '3747-32', '687404', '4:17');
        $res[] = new Song('サ・ヨ・ナ・ラ Summer Holiday', '3768-32', '688815', '4:29');
        $res[] = new Song('From Teacher To Future!', '4060-80', '424870', '4:34');
        $res[] = new Song('勇敢なるキミヘ', '3756-27', '682272', '4:35');
        $res[] = new Song('MISSION is ピースフル!', '3756-26', '685184', '4:32');
        $res[] = new Song('Swing Your Leaves', '4401-82', '426685', '3:57');
        $res[] = new Song('喝彩!～花鳥風月～', '3756-41', '682271', '4:50');
        $res[] = new Song('和風堂々!～WAnderful NIPPON!～', '3756-42', '685183', '4:33');
        $res[] = new Song('桜彩', '4405-83', '427882', '4:29');
        $res[] = new Song('Cafe Parade!', '3762-51', '686102', '4:46');
        $res[] = new Song('A La Carte FREEDOM♪', '3762-50', '686101', '4:19');
        $res[] = new Song('Reversed Masquerade', '4400-81', '426330', '5:20');
        $res[] = new Song('バーニン・クールで輝いて', '3762-69', '685486', '4:26');
        $res[] = new Song('オレたちの最強伝説～一世一代、破羅駄威棲!～', '3762-68', '685487', '3:14');
        $res[] = new Song('RIGHT WAY,SOUL MATE', '4405-86', '427881', '4:36');
        $res[] = new Song('The 1st Movement～未来のための二重奏～', '3766-43', '687759', '4:57');
        $res[] = new Song('Never end 「Opus」', '3766-42', '687760', '5:06');
        $res[] = new Song('Tone\'s Destiny', '4400-79', '426415', '4:39');
        $res[] = new Song('強く尊き獣たち', '3766-49', '687762', '4:06');
        $res[] = new Song('情熱...FIGHTER', '3766-50', '687763', '4:25');
        $res[] = new Song('RAY OF LIGHT', '4405-68', '427880', '4:50');
        $res[] = new Song('うぇるかむ・はぴきらパーク!', '3769-32', '691074', '4:10');
        $res[] = new Song('もっふ・いんざぼっくす♪', '3769-33', '691075', '4:04');
        $res[] = new Song('伝えたいのはこんなきもち', '4401-96', '426686', '4:21');
        $res[] = new Song('夢色VOYAGER', '3769-17', '691093', '3:57');
        $res[] = new Song('With...STORY', '3769-16', '691092', '4:58');
        $res[] = new Song('Cupids!', '4401-81', '426687', '4:32');
        $res[] = new Song('Legacy of Spirit', '3769-22', '691091', '4:35');
        $res[] = new Song('String of Fate', '3769-23', '691090', '4:44');
        $res[] = new Song('Symphonic Brave', '4400-86', '427514', '4:46');
        $res[] = new Song('夜空を煌めく星のように', '3765-23', '686923', '4:58');
        $res[] = new Song('エウレカダイアリー', '3768-30', '688814', '4:48');
        $res[] = new Song('カレイド TOURHYTHM', '3769-18', '690188', '4:44');
        $res[] = new Song('Eternal Fantasia', '4400-82', '425973', '4:36');
        $res[] = new Song('Compass Gripper!!!', '4401-83', '426688', '3:57');
        $res[] = new Song('笑顔の祭りにゃ、福来る', '4405-84', '427879', '4:56');
        $res[] = new Song('冬の日のエトランゼ', '4402-46', 'なし', '4:06');
        $res[] = new Song('いつかこの瞬間に名前をつけるなら', '914355', 'なし', '4:14');
        $res[] = new Song('Secret!Playful!Drive!', '4403-13', 'なし', '4:39');
        $res[] = new Song('流星PARADE', '4060-82', '424858', '4:31');
        $res[] = new Song('夏時間グラフィティ', '4400-80', '426012', '4:05');
        $res[] = new Song('HAPPY×HAPPYミーティング', '3791-27', '423992', '4:43');
        $res[] = new Song('BACK FLIP☆EMOTION', '3792-30', '423677', '3:45');
        $res[] = new Song('ROMANTIC SHAKER', '3780-58', '695513', '3:38');
        $res[] = new Song('THE FIRST STAR', '3771-78', '691133', '4:12');
        $res[] = new Song('Because', '3780-61', '695514', '4:55');
        $res[] = new Song('約束はドリーミングフライト', '3778-49', '692785', '4:16');
        $res[] = new Song('Flying Hawk', '3772-61', '692296', '4:27');
        $res[] = new Song('魔法のステアー', '4062-18', '425363', '4:08');
        $res[] = new Song('Cherish BOUQUET', '788620', '425166', '3:58');
        $res[] = new Song('サイコーCOUNT UP!', '3778-39', '692784', '4:32');
        $res[] = new Song('Genuine Feelings', '788651', '423885', '4:41');
        $res[] = new Song('青春!サティスファクション', '3791-24', '423991', '4:28');
        $res[] = new Song('ナツゾラRecords', '3792-31', '423676', '4:11');
        $res[] = new Song('PRECIOUS TONE', '4062-32', '425359', '4:16');
        $res[] = new Song('Worldwide Ambitions!', '4062-34', '425358', '4:32');
        $res[] = new Song('Wonderful Tactics!', '3789-73', '698074', '4:35');
        $res[] = new Song('Learning Message', '788712', '423884', '5:03');
        $res[] = new Song('THIS IS IT!', '4062-37', '425357', '4:39');
        $res[] = new Song('GOLD ～No.79～', '3771-67', '691758', '4:22');
        $res[] = new Song('GO AHEAD SMILE!', '3791-25', '423990', '4:35');
        $res[] = new Song('Happy-Go-Unlucky!', '3772-62', '692309', '4:07');
        $res[] = new Song('ハートフル・パトローラー', '4058-86', '423883', '4:10');
        $res[] = new Song('ぴんとこな～蝶よ華よ～', '3772-53', '692308', '4:45');
        $res[] = new Song('だいぶ・いんとぅ・にゅ～・わあるど!', '3771-81', '691759', '4:08');
        $res[] = new Song('流るゝ風の如く～和敬清寂～', '3780-68', '695515', '4:16');
        $res[] = new Song('A CUP OF HAPPINESS', '3792-36', '423675', '4:38');
        $res[] = new Song('我が混沌のサバト・マリアージュ', '3771-53', '691760', '4:45');
        $res[] = new Song('フェイバリットに踊らせて', '3780-67', '695516', '4:06');
        $res[] = new Song('Piece Montee', '3778-48', '692783', '4:36');
        $res[] = new Song('ワンホール=ワンダーランド!', '4062-25', '425356', '5:03');
        $res[] = new Song('威風堂々と', '3789-74', '697571', '4:27');
        $res[] = new Song('漢一貫ロックン・ロール', '3791-18', '423989', '3:45');
        $res[] = new Song('その場所へ行くために-KEEP ON FIGHTING-', '3792-38', '423674', '4:34');
        $res[] = new Song('RULE～牙ヲ穿テヨ～', '3789-75', '697572', '3:43');
        $res[] = new Song('ROAD TO THE FUTURE', '691010', '423988', '5:02');
        $res[] = new Song('Sanctuary World', '3780-70', '695517', '4:49');
        $res[] = new Song('Echoes My Note', '3772-59', '692311', '4:53');
        $res[] = new Song('ふわもこシフォンなゆめのなか♪', '3778-50', '692782', '3:49');
        $res[] = new Song('ほっぷ・すてっぷ・ハイ、しーぷ!', '3789-76', '697573', '4:12');
        $res[] = new Song('新世代ワールド・ビッグスター!', '3771-62', '691761', '3:39');
        $res[] = new Song('羽ばたきのMy Soul', '3772-56', '692312', '4:46');
        $res[] = new Song('…掲げよう、偽りなき自分を。', '3792-28', '423673', '4:14');
        $res[] = new Song('HANAMARU LIFE', '4058-93', '423882', '4:20');
        $res[] = new Song('Flowing Freedom', '3791-31', '423987', '4:14');
        $res[] = new Song('Sweep Your Gloom', '3789-77', '698073', '3:41');
        $res[] = new Song('Undiscovered WORLD', '3778-41', '692781', '4:45');
        $res[] = new Song('DRIVE A LIVE(Jupiter ver.)', '3730-93', '673788', '4:42');
        $res[] = new Song('Beyond The Dream', '3777-87', '692693', '4:32');
        $res[] = new Song('Reason!!', '4059-67', '424125', '5:05');
        $res[] = new Song('MEET THE WORLD!', '4424-66', '429193', '4:53');
        $res[] = new Song('Genesis Contact', '3791-73', 'なし', '4:43');
        $res[] = new Song('GLORIOUS RO@D', 'なし', '426742', '4:58');
        $res[] = new Song('永遠なる四銃士', '4424-18', '429194', '4:14');
        $res[] = new Song('トレジャー・パーティー!', '4427-49', '429942', '3:44');
        $res[] = new Song('Alice or Guilty(M@STER VERSION)', '3141-29', '148364', '');
        $res[] = new Song('恋をはじめよう(M@STER VERSION)', '3141-30', '148365', '');
        $res[] = new Song('BANG×BANG', '3205-27', '315525', '');
        $res[] = new Song('On Sunday', '3205-14', '315526', '');
        $res[] = new Song('結晶～Crystal Dust～', '476825', '669447', '');
        $res[] = new Song('Dazzling World(M@STER VERSION)', '3344-49', '145585', '4:29');
        $res[] = new Song('ヒミツの珊瑚礁', '3344-50', '145586', '');
        $res[] = new Song('嗚呼、情熱に星は輝く～315プロダクション社歌～', '4424-52', 'なし', '');

        $allSongs = $res;
        if ($this->slFlg) {
            array_merge($allSongs, $this->getSlSongs());
        }
        if ($this->opFlg) {
            array_merge($allSongs, $this->getOpSongs());
        }
        if ($this->secondFlg) {
            array_merge($allSongs, $this->get2ndSongs());
        }
        if ($this->thirdFlg) {
            array_merge($allSongs, $this->get3rdSongs());
        }
        if ($this->animeFlg) {
            array_merge($allSongs, $this->getAnimeSongs());
        }
        if ($this->everyoneFlg) {
            array_merge($allSongs, $this->getEveryoneSongs());
        }
        if ($this->wtFlg) {
            array_merge($allSongs, $this->getWtSongs());
        }
        shuffle($allSongs);

        return $allSongs;

    }

    // TODO mock

    /**
     * @return array
     */
    private function getSlSongs()
    {
        $res = array();
        return $res;
    }

    // TODO mock

    /**
     * @return array
     */
    private function getOpSongs()
    {
        $res = array();
        return $res;
    }

    // TODO mock

    /**
     * @return array
     */
    private function get2ndSongs()
    {
        $res = array();
        return $res;
    }

    // TODO mock

    /**
     * @return array
     */
    private function get3rdSongs()
    {
        $res = array();
        return $res;
    }

    // TODO mock

    /**
     * @return array
     */
    private function getAnimeSongs()
    {
        $res = array();
        return $res;
    }

    // TODO mock

    /**
     * @return array
     */
    private function getEveryoneSongs()
    {
        $res = array();
        return $res;
    }

    // TODO mock

    /**
     * @return array
     */
    private function getWtSongs()
    {
        $res = array();
        return $res;
    }
}
