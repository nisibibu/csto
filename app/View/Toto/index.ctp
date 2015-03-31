<?php
echo "チーム選択"."</br>";
    
    
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
    
    echo "第".$recent_toto_info[0]['Totovote']['held_time']."回 totoの組み合わせ情報"."</br>";
    foreach($recent_toto_info as $toto_info){
        echo  $toto_info['Totovote']['no']."   " .$toto_info['Totovote']['home_team'] ." VS ". $toto_info['Totovote']['away_team']."<br />";
    }
    
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
    <h1>Hello, world!</h1>
    <?php echo $this->BootstrapForm->input('name'); ?>
    
    
    
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
    
    
    <!-- Form 部分 -->
    <form class="form-inline" style="margin-bottom: 15px;">
    	<div class="form-group">
    	<label class="sr-only control-label" for="email">Email</label>
    	<input type="text" id="email" class="form-control" placeholder="email">
    	<!-- <span class="help-block">Error</span> -->
    	</div>
    	<div class="form-group">
    		<input type="submit" value="submit" class="btn btn-primary">
    	</div>
    </form>
    
    <form class="form-horizontal" style="margin-bottom: 15px;">
    	<div class="form-group">
    	<label class="control-label col-sm-2" for="email">Email</label>
    		<div class="col-sm-4">
    			<input type="text" id="email" class="form-control" placeholder="email">
    	<!-- <span class="help-block">Error</span> -->
    		</div>
    	</div>
    	<div class="form-group">
    	<label class="control-label col-sm-2" for="email">Email</label>
    		<div class="col-sm-4">
    			<input type="text" id="email" class="form-control" placeholder="email">
    	<!-- <span class="help-block">Error</span> -->
    		</div>
    	</div>
    	<div class="form-group">
    		<div class="col-sm-offset-2 col-sm-4">
    			<input type="submit" value="submit" class="btn btn-primary">
    		</div>
    	</div>
    </form>
    
    <!-- Glyphicons -->
    <p><i class="glyphicon glyphicon-book"></i>BOOKS</p>
    
    <!-- ボタン  -->
    <button class="btn btn-primary"><i class="glyphicon glyphicon-book"></i>push</button>
    <!-- ボタンのグループ化 -->
    <div class="btn-group">
    <button class="btn btn-primary">push</button>
    <button class="btn btn-sucess">push</button>
    <button class="btn btn-info">push</button>
    </div>
    
    <!-- ドロップダウンメニュー -->
    <div class="btn-group">
    	<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">メニュー
    	<span class="caret"></span>
    	</button>
    	<ul class="dropdown-menu">
    		<li><a href="#">メニュー１</a></li>
    		<li><a href="#">メニュー２</a></li>
    		<li class="divider"></li>
    		<li><a href="#">メニュー３</a></li>
    	</ul>
    </div>
    
    <!-- ドロップダウンメニュー（キャロットをボタンに） -->
    <div class="btn-group">
    	<button class="btn btn-primary">メニュー</button>
    	<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
    	<span class="caret"></span>
    	</button>
    	<ul class="dropdown-menu">
    		<li><a href="#">メニュー１</a></li>
    		<li><a href="#">メニュー２</a></li>
    		<li class="divider"></li>
    		<li><a href="#">メニュー３</a></li>
    	</ul>
    </div>
    
    <!-- ナビゲーション -->
    <!-- breadcrmb（パンくずリスト） -->
    <div class="container" style="padding: 20px 0">
    	<ul = class="breadcrumb">
    		<li><a href="">Home</a></li>
    		<li><a href="">page1</a></li>
    		<li class="active">page2</li>
    	</ul>
    	
    	
    	<!-- ページング -->
    	<ul class="pagination">
    		<li class="disabled"><a href="">&laquo;</a></li>	<!--クリックできないようにする-->
    		<li class="active"><a href="">1</a></li>			<!--現在のページ-->
    		<li><a href="">2</a></li>
    		<li><a href="">3</a></li>
    		<li><a href="">&raquo;</a></li>
    	</ul>
    	
    	<!-- ページング（前ページ、次ページ遷移） -->
    	<ul class="pager">
    		<li class="previous"><a href="">前へ</a></li>	<!--クリックできないようにする-->
    		<li class="next"><a href="">次へ</a></li>			<!--現在のページ-->
    	</ul>
    	
    	
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
    	<p>Product<span class="label label-primary">NEW!</span></p>
    	<!-- badge -->
    	<p>Inbox<span class="badge">5</span></p><!--中身がない場合バッジ表示されない -->
    	<!-- alert -->
    	<div class="alert alert-info">
    		<!-- アラートを消すボタン(×)  -->
    		<button class="close" data-dismiss="alert">&times;</button>
    		おしらせ
    	</div>
    	<!-- panel -->
    	<div class="panel panel-primary">
    		<div class="panel-heading">
    			お知らせ
    		</div>
    		<div class="panel-body">
    			本文
    		</div>
    		
    	<!-- プログレスバー  -->
    	<div class="progress">
    		<div class="progress-bar progress-bar-primary" style="width:60%"></div>
    	</div>
    	
    	<div class="progress progress-striped active">
    		<div class="progress-bar progress-bar-info" style="width:40%"></div>
    		<div class="progress-bar progress-bar-primary" style="width:30%"></div>
    		<div class="progress-bar progress-bar-warning" style="width:30%"></div>
    	</div>
    	
    	<!-- Modalウィンドウ-->
    	<a data-toggle="modal" href="#myModal" class="btn btn-primary">Show me!</a>
    	
    	<div class="modal fade" id="myModal">
    		<div class="modal-dialog">
    			<div class="modal-content">
	    			<div class="modal-header">
	    				<button class="close" data-dismiss="modal">&times;</button>
	    				<h4>My Modal</h4>
	    			</div>
	    			<div class="modal-body">
	    				テスト
	    			</div>
	    			<div class="modal-footer">
	    				<button class="btn btn-primary">OK!</button> <!-- -->
	    			</div>
	    		</div>
    		</div>
    	</div>
    	
    	<!-- タブメニューを作る -->
    	<ul class="nav nav-tabs">
    		<li class="active"><a href="#home" data-toggle="tab">Top</a></li>
    		<li><a href="#about" data-toggle="tab">About</a></li>
    	</ul>
    	<!-- タブの中身 -->
    	<div class="tab-content">
    		<div class="tab-pane" id="home">トップ</div>
    		<div class="tab-pane" id="about">アバウト</div>
    	</div>
    	
    	<!-- tooltip(カーソルを合わせると説明表示）  -->
    		<p><a href="#" data-toggle="tooltip" title="説明">this</a> and that
    	<!-- popover(クリックで説明表示)  -->
			and <a href="#" data-toggle="popover" title="説明" data-content="さらに説明">that</a>
    	
    	
    	<!-- カルーセル（アクセシブル的にはあまり好ましくない？） -->
		  <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
		  <!-- Indicators -->
		  <ol class="carousel-indicators">
		    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
		    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
		    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
		  </ol>

		  <!-- Wrapper for slides -->
		  <div class="carousel-inner" role="listbox">
		    <div class="item active">
		      <!--<img src="A005.jpg" alt="..."> -->
		      <div class="carousel-caption">
		        ベジータ
		      </div>
		    </div>
		    <div class="item">
		      <!--<img src="A001.jpg" alt="...">-->
		      <div class="carousel-caption">
		        フリーザ
		      </div>
		    </div>
		    ...
		  </div>

		  <!-- Controls -->
		  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
		    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		    <span class="sr-only">Previous</span>
		  </a>
		  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
		    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		    <span class="sr-only">Next</span>
		  </a>
		</div>
    	
    	<!--  カルーセルのサンプルここまで  -->
    	
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