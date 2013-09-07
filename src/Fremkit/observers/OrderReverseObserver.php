<?php

class OrderReverseObserver extends MetaOrderObserver {

	//Put the item in the front
    public function saving($model)
    {	
    	if( is_null($model->id) ){

    		$reflect = get_class($model);
    		$reflect::increment('order');
    		$model->order = 1;

    	}
    }

}
