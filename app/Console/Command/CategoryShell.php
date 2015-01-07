<?php
// app/Console/Command/CategoryShell.php
class CategoryShell extends AppShell{
	//モデルを参照する（Categoryモデル）
	public $uses = array('Category');
	
	public function main(){
		$this->out('Hello world');
	}
	
	public function index(){
		$this->out("id\tname");
		foreach($this->Category->find('all') as $category){
			$this->out($category['Category']['id']
				."\t".$category['Category']['name']);
		}
	}
	
	public function add(){
		$this->Category->create();
		$this->Category->save(array('name' => $this->args[0]));
		$this->out('登録しました');
	}
	
	public function delete(){
		$category = $this->Category->findById($this->args[0]);
		$this->out($category['Category']['id']."\t".$category['Category']['name']);
		if(strtolower($this->in('本当に削除してよろしいですか？',
									array('y','n'),'n' )) == 'n'){
			$this->out('終了します');
			return;
		}
		$this->Category->delete($this->args[0]);
		$this->out('削除しました');
	}
}
?>