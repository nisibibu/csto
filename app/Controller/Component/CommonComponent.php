<?php


/* 共通処理用のコンポーネント
 * 
 *  
 *
 */
App::uses('Component', 'Controller');
class CommonComponent extends Component{
   
    /*日にちから月の何週目かを取得して返す
     *
     * @param  stirng  $date 
     * @return int  $count_week
     *      */
    public function getWeek($date){
        $now = strtotime($date);
        $saturday = 6;
        $week_day = 7;
        //debug(date('w',$now));
        $w = intval(date('w',$now));
        //debug($w);
        $d = intval(date('d',$now));
        //debug($d);
        if ($w!=$saturday) {
        $w = ($saturday - $w) + $d;
        } else { // 土曜日の場合を修正
        $w = $d;
        }
        $count_week = ceil($w/$week_day);
        return $count_week;
    }
    
    /* チーム名を正式名称に変換する
     * 川崎Ｆ→川崎フロンターレ
     * @param string $name
     * 
     * @return string $official_name
     */
    /*チーム名の変換
     * 仙台　→　ベガルタ仙台
     * 
     * 全ての登録時に必ず正式名称に変換して登録する
     * 
     * 
     *      */
    public function formatTeamName($name){
        if($name === '仙台'){
            $name = 'ベガルタ仙台';
        }
        else if($name === '甲府'){
            $name = 'ヴァンフォーレ甲府';
        }
	else if($name === '名古屋'){
            $name = '名古屋グランパス';
        }
	else if($name === 'C大阪'){
            $name = 'セレッソ大阪';
        }
	else if($name === '鳥栖'){
            $name = 'サガン鳥栖';
        }
	else if($name === '柏'){
            $name = '柏レイソル';
        }
	else if($name === 'G大阪'){
            $name = 'ガンバ大阪';
        }
	else if($name  === '横浜'){
            $name = '横浜 Ｆ・マリノス';
        }
	else if($name === '川崎F'){
            $name = '川崎フロンターレ';
        }
	else if($name === 'FC東京'){
            $name = 'ＦＣ東京';
        }
	else if($name === '鹿島'){
            $name = '鹿島アントラーズ';
        }
	else if($name === '新潟'){
            $name = 'アルビレックス新潟';
        }
	else if($name === '千葉'){
            $name = 'ジェフユナイテッド市原・千葉';
        }
	else if($name === '大分'){
            $name = '大分トリニータ';
        }
        else if($name === '熊本'){
            $name = 'ロアッソ熊本';
        }
	else if($name === '広島'){
            $name = 'サンフレッチェ広島';
        }
	else if($name === '神戸'){
            $name = 'ヴィッセル神戸';
        }
	else if($name === '徳島'){
            $name = '徳島ヴォルティス';
        }
	else if($name === '浦和'){
            $name = '浦和レッズ';
        }
	else if($name === '大宮'){
            $name = '大宮アルディージャ';
        }
	else if($name === '清水'){
            $name = '清水エスパルス';
        }
	else if($name === '栃木'){
            $name = '栃木ＳＣ';
        }
	else if($name === '東京V'){
            $name = '東京ヴェルディ1969';
        }
	else if($name === '岐阜'){
            $name = 'ＦＣ岐阜';
        }
	else if($name === '磐田'){
            $name = 'ジュビロ磐田';
        }
	else if($name === '水戸'){
            $name = '水戸ホーリーホック';
        }
	else if($name === '横浜FC'){
            $name = '横浜ＦＣ';
        }
	else if($name === '岡山'){
            $name = 'ファジアーノ岡山';
        }
	else if($name === '北九州'){
            $name = 'キラヴァンツ北九州';
        }
	else if($name === '湘南'){
            $name = '湘南ベルマーレ';
        }
	else if($name === '長崎'){
            $name = 'Ｖ・ファーレン長崎';
        }
	else if($name === '福岡'){
            $name = 'アビスパ福岡';
        }
	else if($name === '愛媛'){
            $name = '愛媛ＦＣ';
        }
	else if($name === '讃岐'){
            $name = 'カマタマーレ讃岐';
        }
	else if($name === '群馬'){
            $name = 'ザスパクサツ群馬';
        }
	else if($name === '札幌'){
            $name = 'コンサドーレ札幌';
        }
	else if($name === '山形'){
            $name = 'モンテディオ山形';
        }
	else if($name === '松本'){
            $name = '松本山雅ＦＣ';
        }
	else if($name === '富山'){
            $name = 'カターレ富山';
        }
	else if($name === '京都'){
            $name = '京都サンガF.C.';
        }
	else if($name === '長野'){
            $name = 'ＡＣ長野パルセイロ';
        }
	else if($name === '金沢'){
            $name = 'ツエーゲン金沢';
        }
        $official_name = $name;
        
        return $official_name;
    }
}