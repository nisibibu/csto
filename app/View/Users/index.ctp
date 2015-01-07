<!-- app/View/User/login.ctp -->
<?php
//echo $this->Session->flash('Auth');
echo $this->Form->create('User');
echo $this->Form->input('User.username');
echo $this->Form->input('User.password');
echo $this->Form->end('Login');
?>