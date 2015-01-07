<?php if(empty($user)): /*未ログインの場合はFormヘルパーを使って認証ボタンを表示*/ ?>
	<?php echo $this->Form->create('TwLogins',array('action' => 'twitter_login')); ?>
	<?php echo $this->Form->end('TwitterでLogin'); ?>
<?php else:	/* ログイン済みの場合はログアウトオプションへのリンクを表示 */ ?>
	ログイン済みです。
	<?php echo $user['NewUser']['username']; ?>
	<strong><?php echo $this->Html->link(__('Logout'),array('action' => 'logout')); ?></strong>
<?php endif; ?>
								