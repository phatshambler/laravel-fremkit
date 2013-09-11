<?php

class OrderReverseObserver extends MetaOrderObserver {

	//Put the item in the front
    public function saving($model)
    {	
    	if( is_null($model->id) ){

    		$reflect = get_class($model);

    		$items = $reflect::where('order', 1);

    		if(is_null($items)){
    			$reflect::increment('order');
    		}

    		$model->order = 1;

    	}
    }

}
