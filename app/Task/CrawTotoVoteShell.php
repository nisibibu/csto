<?php

//Totoコンポーネントの読み込み
require_once 'C:\xampp\htdocs\cake\app\Controller\Component\TotoComponent.php';

$toto_component = new TotoComponent;
//投票率の取得
$toto_vote = $toto_component->getTotoVote();
//投票率の格納
$toto_component->setTotoVotesTable($toto_vote);