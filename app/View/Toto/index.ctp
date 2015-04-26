<?php
    
    
    /*** Formの作成 **
     * find('list')のデータを渡すことで
     * リスト生成可能（書きかえる）
     *      
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
            'controller' => 'Toto','action' => 'index'))); //指定アクションに送信
    echo $this->Form->label('team','チーム');
    //echo $this->Form->select('team', $team_list);
     echo $this->Form->input('team',array(     
          "type" => "select", 
          'options'=> $team_list,
          //'selected' => $count["5"], //画面側で初期値の設定
    ));
    echo $this->Form->label("count","取得件数");
    echo $this->Form->input('count',array(     
          "type" => "select", 
          'options'=> $count,
          //'selected' => $count["5"], //画面側で初期値の設定
        ));
    echo $this->Form->submit("検索");
    echo $this->Form->end();
    
    //var_dump($team);
    */
    /*
    echo "第".$recent_toto_info[0]['Totovote']['held_time']."回 totoの組み合わせ情報"."<br />";
    foreach($recent_toto_info as $toto_info){
        echo  $toto_info['Totovote']['no']."   " .$toto_info['Totovote']['home_team'] .
                    " VS ". $toto_info['Totovote']['away_team']."  ".$toto_info['Totovote']['stadium']."<br />";
    }
    */
    $held_time = $recent_toto_info['toto']['held_time'];
    unset($recent_toto_info['toto']['held_time']);
    /*
    $table_header = $this->Html->tableHeaders(
            array("開催日","開始時刻","No","ホーム","アウェイ","会場"));
    $table_cells = $this->Html->tableCells(
            //array("1","4/5","15:00","ベガルタ仙台","清水エスパルス","ユアスタ"));
            $recent_toto_info);
    
    echo $this->Html->div('panel panel-default',
            $this->Html->tag("table",$table_cells,$table_header)
    );
    */
    
    echo "第".$held_time."回toto"."</br>";
    
    /*分けて記述*/
    echo $this->Html->div('panel panel-default');
    echo $this->Html->tag('table');
    echo $this->Html->tableHeaders(array("開催日","開始時刻","No","ホーム","アウェイ","会場","詳細"));
    foreach($recent_toto_info['toto'] as $toto_info){
        $temp = $this->Form->create('card',array(
        'type' => 'POST',
        'url' => array(
            'controller' => 'Toto','action' => 'index'))); //指定アクションに送信
        //debug($temp);
        $temp_2 = $this->Form->hidden('home_team',
                array('value' => $toto_info['home_team']));
        //debug($temp_4);
        $temp_3 = $this->Form->hidden('away_team',
                array('value' => $toto_info['away_team']));
        //debug($temp_5);
        $temp_4 = $this->Form->submit("詳細");
        //debug($temp_2);
        $options = array(
        'label' => '詳細',
        'div' => array(
            'class' => 'glass-pill',
            )
        );
        $temp_5 = $this->Form->end();
        //debug($temp_3);
        //$toto_info[] = $detail;
        $temp_str = $temp. $temp_2. $temp_3. $temp_4. $temp_5;
        $toto_info[] = $temp_str;
        echo $this->Html->tableCells($toto_info);
         
    }
    echo $this->Html->tag('/table');
    echo $this->Html->tag('/div');
    
    /*
    if(count($match) === 1){
        debug($match);
    }
    */
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link href="C:\xampp\htdocs\cake\app\webroot\css/bootstrap.min.css" rel="stylesheet">

	<!--古いバージョン対応 -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <?php //echo $this->BootstrapForm->input('name'); 
        if($home_team_info){
             echo 'ホーム：'. $home_team_info. "<br />". 'アフェイ：'. $away_team_info.'<br />';
        }
               
    ?>
    
    
    
    <!-- ヘッダー -->
    <div id="header" class="container" style="background:red;">header</div>
    
    <!-- ラベル部分 -->
    <div class="container">
    <div class="row">
    	<div class="col-sm-3 hidden-xs" style="background:pink;">Side1</div>
    	<div class="col-sm-6 col-xs-6" style="background:green;">Main</div>
    	<div class="col-sm-3 col-xs-6" style="background:orange;">Side2</div>
    </div>
    </div>
    
    <!-- テーブル  -->
    <div class="container" style="padding:20px 0">
    <table class="table table-striped table-bordered table-hover">
    	<thead>
    		<tr><th>ID</th><th>Score</th></tr>
    	</thead>
    	<tbody>
    		<tr><td>@a</td><td>100</td></tr>
    		<tr class="warning"><td>@b</td><td>80</td></tr>
    		<tr><td>@c</td><td>90</td></tr>
    	</tbody>
    </table>
    </div>
    
    <?php echo $this->BootstrapForm->input('prefecture', array(
    'label' => 'チームリスト',
    'multiple' => 'checkbox',
    'options' => $team_list,
    'li' => array('style' => 'width:6em;float:left;padding-top:2px;'),
)); ?>
    
    	
    	
    	<!-- ナビゲーションバー  -->
    	<nav class="navbar navbar-default navbar-fixed-top"> <!-- fixed-top で上固定 -->
    		<div class="navbar-header">	
    		<!-- ナビゲーションバーのスマホ対応 -->
    			<button class="navbar-toggle" data-toggle="collapse" data-target=".target">
    				<span class="icon-bar"></span>
    				<span class="icon-bar"></span>
    				<span class="icon-bar"></span>
    			</button>
    			<a class="navbar-brand" href="">Toto試合情報</a>
    		</div>
    		
    		<div class="collapse navbar-collapse target">
	    		<ul class="nav navbar-nav">
	    			<li class="active"><a href="">Link1</a></li>
	    			<li><a href="">Link2</a></li>
	    		</ul>
	    		<ul class="nav navbar-nav navbar-right">
	    			<li><a href="">Link3</a></li>
	    			<li><a href="">Link4</a></li>
	    		</ul>
	    	</div>
    	</nav>
    	
    	<!-- ラベル -->
    	<!--<p>Product<span class="label label-primary">NEW!</span></p>-->
    	<!-- badge -->
    	<!--<p>Inbox<span class="badge">5</span></p><!--中身がない場合バッジ表示されない -->

    	<!-- panel -->
    	<div class="panel panel-primary">
    		<div class="panel-heading">
    			お知らせ
    		</div>
    		<div class="panel-body">
    			本文
    		</div>
        </div>
    
    
    
    <!-- フッター -->
    <div id="footer" class="container" style="background:blue;">footer</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    <!-- JQuery -->
    <script>
    	$(function(){
    		$("[data-toggle=tooltip]").tooltip({
 				placement: 'bottom'	//テキストの下に表示
 			});
    		$("[data-toggle=popover]").popover()
    	});
    </script>
    
  </body>
</html>