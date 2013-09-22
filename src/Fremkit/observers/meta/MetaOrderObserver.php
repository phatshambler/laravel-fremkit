<?php

//Redo this so the filter adds the conditions dynamically; you know what i mean.

class MetaOrderObserver {

    //Remove and reorder
    public function deleting($model)
    {	
    	$reflect = get_class($model);

        $items = $reflect::getOrderItems($model);

        foreach($items as $i){
            if($i->order > $model->order){
                $i->order = $i->order - 1;
                $i->save();
            }
        }

		//$reflect::where('order', '>', $model->order)->decrement('order');
    }
    //Put the item back at it's original order
    public function restoring($model)
    {	
    	$reflect = get_class($model);

        $items = $reflect::getOrderItems($model);

        foreach($items as $i){
            if($i->order >= $model->order){
                $i->order = $i->order - 1;
                $i->save();
            }
        }

		//$reflect::where('order', '>=', $model->order)->increment('order');
    }

}
