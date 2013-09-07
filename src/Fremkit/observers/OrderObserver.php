<?php namespace Niko\Observers;

class OrderObserver extends MetaOrderObserver{

    public function saving($model)
    {	
    	if( is_null($model->id) ){
    		$count = DB::table($model->table)->count();
    		$model->order = $count + 1;
    	}
    }

}
