<!-- app/View/Tasks/edit.ctp -->
<?php echo $this->form->create('Task', array('type' => 'post')); ?>
<!-- まとめて表示を行う -->
<?php
echo $this->Form->input('Task.name', array('label' => 'タイトル'));
echo $this->Form->input('Task.body', array('label' => '詳細'));
echo $this->Form->end('保存');
?>'
