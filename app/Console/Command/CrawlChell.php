<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// Inculde the phpcrawl-mainclass
include("../Vendor\PHPCrawl\libs/PHPCrawler.class.php");

// Extend the class and override the handleDocumentInfo()-method
//class MyCrawler extends PHPCrawler 
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

if (PHP_SAPI == "cli") $lb = "\n";
else $lb = "<br />";
    
echo "Summary:".$lb;
echo "Links followed: ".$report->links_followed.$lb;
echo "Documents received: ".$report->files_received.$lb;
echo "Bytes received: ".$report->bytes_received." bytes".$lb;
echo "Process runtime: ".$report->process_runtime." sec".$lb;

/*
 * 参考サイト
 * http://ichizo.biz/2014/01/10/post-19/
class CrawlShell extends AppShell {
 
    var $uses = array('Url', 'Result');
 
    public function main() {
 
        $count_prg = $this->Url->find('count', array('conditions'=>array('progress_flg'=>'1')));
        if ($count_prg == 0) {
            $this->url = $this->Url->find('first', array('conditions'=>array('progress_flg'=>'0')));
 
            if (!empty($this->url)) {
                $sql = "UPDATE crl_urls SET progress_flg = 1, started = ? WHERE id = ?";
                $this->Url->query($sql, array(date('Y-m-d H:i:s'), $this->url['Url']['id']));
 
                // Now, create a instance of your class, define the behaviour
                // of the crawler (see class-reference for more options and details)
                // and start the crawling-process.
                $crawler = new MyCrawler();
 
                $crawler->url_id = $this->url['Url']['id'];
 
                // URL to crawl (the entry-page of the mysql-documentation on php.net)
                $crawler->setURL($this->url['Url']['url']);
 
                // Only receive content of documents with content-type "text/html"
                $crawler->addReceiveContentType("#text/html#");
 
                // Ignore links to pictures, css-documents etc (prefilter)
                $crawler->addURLFilterRule("#(jpg|gif|png|pdf|jpeg|css|js|image)$# i");
 
                // Every URL within the mysql-documentation looks like 
                // "http://www.php.net/manual/en/function.mysql-affected-rows.php"
                // or "http://www.php.net/manual/en/mysql.setup.php", they all contain
                // "http://www.php.net/manual/en/" followed by  "mysql" somewhere.
                // So we add a corresponding follow-rule to the crawler.
                //$crawler->addURLFollowRule("#^http://www.php.net/manual/en/.*mysql[^a-z]# i");
 
                // That's it, start crawling using 5 processes
                $crawler->goMultiProcessed(1);
 
                // At the end, after the process is finished, we print a short
                // report (see method getReport() for more information)
//                $report = $crawler->getProcessReport();
 
                $sql = "UPDATE crl_urls SET progress_flg = 2, ended = ? WHERE id = ?";
                $this->Url->query($sql, array(date('Y-m-d H:i:s'), $this->url['Url']['id']));
            }
        }
    }
 
}
*/
?>