<?php
App::uses('Component', 'Controller');

class TotovotesController extends AppController{
    public $uses = array('POST','Live','Totovote');    //使用するモデルを宣言
    
    public function index(){
        $data = array(
            'Totovote' =>array(
                'id' => 2,
                'heldtime' => '0'
            )
        
        );
        $this->Totovote->save($data);
    }
}

?>