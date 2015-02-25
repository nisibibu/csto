<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'C:\xampp\htdocs\cake\app\Vendor/autoload.php';


App::uses('Component', 'Controller');

/*サッカーダイジェストウェブ用コンポ―ネント*/
class ScDigestComponent extends NewsCrawComponent{

    public $uses = array('POST','Live','Totovote','Teamtrendgoal');    //使用するモデルを宣言
    public $components = array('News');
    
}