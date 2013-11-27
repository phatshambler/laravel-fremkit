<?php

/* 

BAD but useful

*/

/*Redo this one if needed with the new ordering*/

class ComplexImageObserver {

    public function saving($model)
    {
    	//echo 'working...';
    }

    //Remove and reorder
    public function deleting($model)
    {	
    	if( !is_null($model->bannerable_order) )
    	{
	    	$reflect = get_class($model);

			$query = $reflect::where('bannerable_order', '>', $model->bannerable_order);

			if ( !is_null($model->bannerable_cat) ){
				$query->where('bannerable_cat', $model->bannerable_cat);
			}

			//$query->decrement('bannerable_order');
		}
    }
    //Put the item back at it's original order
    public function restoring($model)
    {	
    	if( !is_null($model->bannerable_order) )
    	{
	    	$reflect = get_class($model);

			$query = $reflect::where('bannerable_order', '>=', $model->bannerable_order);

			if ( !is_null($model->bannerable_cat) ){
				$query->where('bannerable_cat', $model->bannerable_cat);
			}

			//$query->increment('bannerable_order');
		}
    }

}