<?php

/* 
 * 
 * -ニュースの処理に関するコンポーネント
 */

require_once 'C:\xampp\htdocs\cake\app\Vendor/autoload.php';
App::uses('Component', 'Controller');

class NewsComponent extends Component{

    public $uses = array('POST','Live','Totovote','Teamtrendgoal');    //使用するモデルを宣言
    
    
}
?>