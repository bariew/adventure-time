<?php

class AdminSearchWidget extends CWidget
{
	public function run()
	{
		$searchString = @$_GET['adminSearch'];
		if($addQuery = @$_GET){
			unset($addQuery['adminSearch']);
		}else{
			$addQuery = array();
		}
		$this->render('adminSearch', compact('searchString', 'addQuery'));
	}
}
