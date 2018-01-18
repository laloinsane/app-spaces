<?php
namespace app\components;
use yii\helpers\Html;
use yii\base\Component;

class MessageComponent extends Component{
	public $content;
	
	public function init(){
		parent::init();
		$this->content= 'Hello Yii 2.0';
	}
	
	public function display($content=null){
		if($content!=null){
			$this->content= $content;
		}
		echo Html::encode($this->content);
	}
	
}
?>