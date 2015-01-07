<!-- app/View/Tasks/create.ctp -->
<form action="<?php 
		echo $this->Html->url('/Tasks/create');
?>" method="POST">
<?php echo $this->form->error('Task.name'); ?>
<?php echo $this->form->error('Task.body'); ?>
タスク名<input type="text" name="name" size="40">
詳細<br />
<textarea name="body" cols="40" rows="8"></textarea>
<input type="submit" value="タスクを生成">
</form>