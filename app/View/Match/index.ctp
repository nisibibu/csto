<?php
    echo "試合結果表示画面"."</br>";
    
    
    /*** Formの作成 **
     * find('list')のデータを渡すことで
     * リスト生成可能（書きかえる）
     *      */
    $team = array(
        "FC東京"=> "FC東京",
        "浦和"=>"浦和",
        '鹿島'=>"鹿島",
        "神戸" => "神戸",
        "G大阪" => "G大阪",
        "C大阪" => "C大阪",
        );
    
    $count = array(
        "1" => 1,
        "3" => 3, 
        "5" => 5,
        "10" => 10,
        "20" => 20,
    );
    
    
    
    echo $this->Form->create('match',array(
        'type' => 'POST',
        'url' => array(
            'controller' => 'Match','action' => 'index'))); //指定アクションに送信
    echo $this->Form->label('team','チーム');
    echo $this->Form->select('team', $team);
    echo $this->Form->label("count","取得件数");
    echo $this->Form->input('count',array(     
          "type" => "select", 
          'options'=> $count,
          //'selected' => $count["5"], //画面側で初期値の設定
        ));
    echo $this->Form->submit("検索");
    echo $this->Form->end();
    
    //echo $match['team'] の直近  回のデータ表示"."</br>";
    var_dump($match);
?>