<?php

class MetaOrderObserver {

    //Remove and reorder
    public function deleting($model)
    {	
    	$reflect = get_class($model);
		$reflect::where('order', '>', $model->order)->decrement('order');
    }
    //Put the item back at it's original order
    public function restoring($model)
    {	
    	$reflect = get_class($model);
		$reflect::where('order', '>=', $model->order)->increment('order');
    }

}
