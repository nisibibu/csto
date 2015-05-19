<?php

use Goutte\Client;
//require_once 'C:\xampp\htdocs\cake\app\Vendor/goutte/goutte.phar';
require_once($_SERVER['DOCUMENT_ROOT']."cake/app/Vendor/autoload.php");
/*定数*/
//TOTOの投票率の取得用URL
define('TOTO_RESULT', 'http://www.totoone.jp/kekka/');
//TOTOの投票率の過去取得用URL
define('TOTO_RESULT_BACKNUMBER','http://www.totoone.jp/kekka/archives.php');

/*totoの投票率を返す*/
class TotoResultComponent extends Component{
    
    public $uses = array('POST','Live','Totovote');    //使用するモデルを宣言
    
        public function getTotoResult($url = TOTO_RESULT){
                  //Goutteオブジェクト生成
                  $client_result = new Client();
                  $result_all = array();              //Totoの結果を全て保持
                  $toto_result = array();           //totoの結果を格納
                  $miniA_result = array();         //miniA の結果を格納
                  $miniB_result = array();         //miniB の結果を格納
                  $goal3_result = array();         //goal3の結果を格納
                  $goal2_result = array();         //goal2の結果を格納
                  $big_result = array();            //TotoBigの結果を格納
                  $big1000_result = array();    //Toto Big 1000 の結果を格納
                  $mini_big_result =array();    //miniBig の結果を格納

                  //totoマッチング、結果のHTMLを取得
                 $crawler_result = $client_result->request('GET', $url);

                  //結果の取得
                  $crawler_result->filter('.kekka-hyou table tr td')->each(function( $node )use(&$result_all){
                    //var_dump($node->text());
                           $result_all[] = $node->text();
                  });
                  
                  
                  function even($var){
                      return ($var <> "");
                  }
                  //取得したデータから空の部分を詰めて配列添え直し
                  $result_all =  array_filter($result_all, "even");
                  $result_all = array_values($result_all);
                  //debug($result_all);
        
                  /*totoの結果を取得*/  
                  $pattern = "/.+totoくじ結果$/";
                  $toto_result = $this->getIndividual($pattern, $result_all);
                  debug($toto_result);
                  
                  /*取得したデータの加工
                   *  未実装　後で修正を行う 
                   * 
                   *                    */
                  if(!is_null($toto_result)){
                      $result = array();
                      $match_begin_no = 16;
                      $match_end_no = 29;
                      $held_time = preg_match('/[0-9]+/', $toto_result[0]); //開催回の取得
                      $result[] = $held_time;
                      /*第1試合～第13試合までの結果取得*/
                      for($i = $match_begin_no; $i < $match_end_no; $i++){
                          $match_result[] = $toto_result[$i];
                          $result[] = $match_result;
                      }
                      //当選についての情報の取得
                      $rank = array();
                      for($i = $match_end_no; $i < count($toto_result); $i++ ){
                          if(preg_match('/1等/', $toto_result[$i])){
                              $temp['money'] = $toto_result[$i + 1];
                              $temp['count'] = $toto_result[$i + 2];
                              $temp['carry_over'] = $toto_result[$i + 3];
                              $rank['rank1'] = $temp;
                              $result[] = $rank;
                          }
                      }
                  }
                  debug($result);

                   /*miniAの結果を取得*/
                  $pattern_a = "/.+Ａ組くじ結果$/";
                  //$miniA_result = $this->getIndividual($pattern_a, $result_all);
                  //debug($miniA_result);
                  
                  /*miniBの結果を取得*/
                  $pattern_b = "/.+Ｂ組くじ結果$/";
                  //$miniB_result = $this->getIndividual($pattern_b, $result_all);
                  //debug($miniB_result);
                  
                   /*goal3の結果を取得*/
                  $pattern_g3 = "/.+ＧＯＡＬ３くじ結果$/";
                  //$goal3_result = $this->getIndividual($pattern_g3, $result_all);
                  //debug($goal3_result);
                  
                  /*goal2の結果を取得*/
                  $pattern_g2 = "/.+ＧＯＡＬ２くじ結果$/";
                  //ここにGOAL2の場合の処理を追加
                  
                  /*BIGの結果を取得*/
                  $pattern_big = "/.+ＢＩＧくじ結果$/";
                  //$big_result = $this->getIndividual($pattern_big, $result_all);
                  //debug($big_result);
                  
                  /*BIG1000の結果を取得*/
                  $pattern_1000 = "/.+ＢＩＧ１０００くじ結果$/";
                  //$big1000_result = $this->getIndividual($pattern_1000, $result_all);
                  //debug($big1000_result);
                  
                  /*BIG mini の結果を取得*/
                  $pattern_mini = "/.+ｍｉｎｉＢＩＧくじ結果$/";
                  //$mini_big_result = $this->getIndividual($pattern_mini, $result_all);
                  //debug($mini_big_result);
        }
        
        
        /*  function getIndividual()
         *   $pattern 正規表現のパターン 
         *   $result    全体のデータ(Totoの結果の全データ）
         */
         public function getIndividual($pattern,$result){
                $flag = TRUE;         //whileループを抜ける判定に使用
                $data  = array();    //結果を格納
                   /*totoの結果を個別に取得*/
                   for($i  = 0 ; $i < count($result); $i++){
                         //$pattern = '/.+totoくじ結果$/';
                        if(!$flag){
                            //var_dump("Falseになったので抜ける");
                            break;
                        }
                        //くじ結果の初めのデータかチェック         
                        if(preg_match($pattern, $result[$i])){
                            $j = $i;    //くじ結果の配列添え字番号を記録
                            //次のくじ結果の行の手前までデータを取得する
                            while(preg_match('/.+くじ結果$/', $result[$j+1] ) == 0) {
                                    $data[] = $result[$j];
                                    $j++;
                                    //最終行なら抜ける
                                    if(!isset($result[$j +1])){
                                        //debug('最終行なので抜けます');
                                        break;
                                    }
                           }
                           $data[]  = $result[$j];
                           $flag = FALSE;
                 }
             }
             return $data;
         }
 
}
?>

