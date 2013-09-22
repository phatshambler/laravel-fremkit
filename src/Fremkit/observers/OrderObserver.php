<?php

class OrderObserver extends MetaOrderObserver{

	//Put the item as last
    public function saving($model)
    {	
    	if( is_null($model->id) ){

    		$reflect = get_class($model);

    		$count = $reflect::getOrderCount();
    		
    		$model->order = $count + 1;
    	}
    }

}
