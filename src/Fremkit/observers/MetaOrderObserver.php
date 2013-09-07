<?php

class MetaOrderObserver {

    public function deleting($model)
    {	
    	$reflect = get_class($model);
		$reflect::where('order', '>', $model->order)->decrement('order');

		//$x = $reflect::where('order', '>', $model->order)->get();

		//$model->order = 0;
    }

    public function restoring($model)
    {	
    	$reflect = get_class($model);
		//$count = $reflect::count();
		$reflect::where('order', '>=', $model->order)->increment('order');
    }

}
