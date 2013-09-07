<?php

class OrderReverseObserver extends MetaOrderObserver {

    public function saving($model)
    {	
    	if( is_null($model->id) ){

    		$reflect = get_class($model);
    		$reflect::increment('order');
    		//$count = DB::table($model->getTable())->count();
    		$model->order = 1;

    	}
    }

}
