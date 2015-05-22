<?php

/* 
 * トラッキング情報の取得
 * 
 */
use Goutte\Client;
if(env('DOCUMENT_ROOT')){
    require_once($_SERVER['DOCUMENT_ROOT']."cake/app/Vendor/goutte/goutte.phar");
}else{
    //debug(env('DOCUMENT_ROOT'));
    require_once '/var/www/cake/app/Vendor/goutte/goutte.phar';
}

class TrackingComponent extends Component{
    
}