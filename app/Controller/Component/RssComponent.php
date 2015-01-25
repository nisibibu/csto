<?php

/* RSSフィードの読み込み.コンポーネント
 */
App::uses('Xml','Utility');

class RssComponent extends Component{
    
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