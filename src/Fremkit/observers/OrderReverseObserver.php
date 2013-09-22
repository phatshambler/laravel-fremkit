<?php

class OrderReverseObserver extends MetaOrderObserver {

	//Put the item in the front
    public function saving($model)
    {	
    	if( is_null($model->id) ){

    		$reflect = get_class($model);

    		$item = $reflect::getOneByOrder(1, $model);

            $i = 1;
    		if( is_null($item) ){
    			//$reflect::increment('order');
                $items = $refleft::getOrderItems($model);

                foreach($items as $it){
                    if($it->order == $i){
                        $it->order = $i + 1;
                    }
                    $i++;
                }
    		}

    		$model->order = 1;

    	}
    }

}
