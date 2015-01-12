<?php

// Inculde the phpcrawl-mainclass
include("../Vendor\PHPCrawl\libs/PHPCrawler.class.php");

// Extend the class and override the handleDocumentInfo()-method
class MyCrawler extends PHPCrawler 
{
  function handleDocumentInfo($DocInfo) 
  {
    // Just detect linebreak for output ("\n" in CLI-mode, otherwise "<br>").
    /* 改行についての処理部分 */
    if (PHP_SAPI == "cli") $lb = "\n";
    else $lb = "<br />";

    // Print the URL and the HTTP-status-Code
    // HTTPステータスコードを表示する
    echo "Page requested: ".$DocInfo->url." (".$DocInfo->http_status_code.")".$lb;
    
    // Print the refering URL
    // 
    echo "Referer-page: ".$DocInfo->referer_url.$lb;
    
    // Print if the content of the document was be recieved or not
    // 
    if ($DocInfo->received == true)
      echo "Content received: ".$DocInfo->bytes_received." bytes".$lb;
    else
      echo "Content not received".$lb; 
    
    // Now you should do something with the content of the actual
    // received page or file ($DocInfo->source), we skip it in this example 
    
    echo $lb;
    
    //CGI Webサーバーなどのバックエンドのシステム書き込みをフラッシュする
    //それまでのすべての出力をユーザーのブラウザに対して出力しようとする
    flush();
  }
}

// Now, create a instance of your class, define the behaviour
// of the crawler (see class-reference for more options and details)
// and start the crawling-process.
// ここではクローラーのクラスのインスタンス生成、ビヘイビアの定義
//（その他のオプションや詳細はクラスのリファレンスを参照）
// インスタンス生成後、クローニング開始

class CrawlComponent extends Component{
    
    //クロールの結果を返す
    public function getCrawlResult(){
    //クローラークラス(MyCrawler)インスタンスの生成
    $crawler = new MyCrawler();

    // URL to crawl (the entry-page of the mysql-documentation on php.net)
    // クロールするURLの設定
    $crawler->setURL("http://www.php.net/manual/en/book.mysql.php");

    // Only receive content of documents with content-type "text/html"
    // コンテンツタイプがtext/htmlのもののみ受信する
    // 受診するコンテンツタイプを指定する
    $crawler->addReceiveContentType("#text/html#");

    // Ignore links to pictures, css-documents etc (prefilter)
    // pictureやCSSドキュメントなどを除外する
    // 除外する拡張子を指定する
    $crawler->addURLFilterRule("#\.(jpg|gif|png|pdf|jpeg|css|js)$# i");

    // Every URL within the mysql-documentation looks like 
    // "http://www.php.net/manual/en/function.mysql-affected-rows.php"
    // or "http://www.php.net/manual/en/mysql.setup.php", they all contain
    // "http://www.php.net/manual/en/" followed by  "mysql" somewhere.
    // So we add a corresponding follow-rule to the crawler.
    // The MySQLのドキュメント内のすべてのURLは次のようになる
    // "http://www.php.net/manual/en/function.mysql-affected-rows.php" か
    //　http://www.php.net/manual/en/ に含まれるURL
    //  なので、クローラーに対応する follow-rule を追加する

    $crawler->addURLFollowRule("#^http://www.php.net/manual/en/.*mysql[^a-z]# i");

    // That's it, start crawling using 5 processes
    // クロール開始（5プロセス）
    $crawler->goMultiProcessed(5);

    // At the end, after the process is finished, we print a short
    // report (see method getReport() for more information)
    $report = $crawler->getProcessReport();
    
    return $report;
    }
}
?>