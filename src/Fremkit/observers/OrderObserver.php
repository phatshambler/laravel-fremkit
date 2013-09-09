<?php

class OrderObserver extends MetaOrderObserver{

	//Put the item as last
    public function saving($model)
    {	
    	if( is_null($model->id) ){
    		$count = DB::table( $model->getTable() )->count();
    		$model->order = $count + 1;
    	}
    }

}
