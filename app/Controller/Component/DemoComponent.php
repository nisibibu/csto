<?php
// app/Controller/Component/DemoComonent.php
class DemoComponent extends Component{
	
	public function hello($name = 'World'){
		return 'Hello'.$name;
	}
}