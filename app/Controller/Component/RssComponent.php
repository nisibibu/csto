<?php

/* RSSフィードの読み込み.コンポーネント
 */
App::uses('Xml','Utility');

class RssComponent extends Component{
    
     /* ファイルから情報を取得
     *  デフォルトではフィードリスト取得
     */
    public function getFromtoFileData($path ='/Text/jfeed_list.txt'){
       $handle = fopen(APP.$path,'r');
       $data_temp = array();
       $data = array();
       
       $count = count(file(APP.$path));
       //debug($count);
       
       for($i = 0; $i < $count; $i++){
           $data_temp[] = fgets($handle);
       }
       fclose($handle); //ファイルを閉じる
       
       //ファイルから取得した情報を連想配列で取得
       for($i = 0; $i < count($data_temp); $i++){
           $tmp = split(',', $data_temp[$i]);
           //debug($tmp);
           //「キー」→「値」の配列にする
           $data[$tmp[0]] = $tmp[1];
            //$data[$temp[0]] = array_merge($data[$temp[0]]);    //添え字を詰める
           
       }
       return $data;
        
    }


    /*フィート取得
     * $feed feedURL
     * $item 取得する個数
     *      */
    public function read($feed, $items = 5){
        try{
            //RSSフィードをリード
            //debug($feed);
            $xmlObject = Xml::build($feed);
        } catch (Exception $ex) {
            throw new InternalErrorException();
        }
        
        $output = array();
        for($i = 0; $i < $items; $i++):
            if(is_object($xmlObject->channel->item->$i)){
                $output[] = $xmlObject->channel->item->$i;
            }    
        endfor;

        return $output;
    }
    
    /*RSSフィード取得URLを取得する*/
    public function get_feed_urls(){
        
    }
    
}