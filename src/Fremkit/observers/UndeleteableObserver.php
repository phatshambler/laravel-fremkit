<?php

class UndeleteableObserver {

	//Prevents deletion of important items with undeletable => 1
    public function deleting($model)
    {
    	if( !is_null($model->undeleteable) && $model->undeleteable == 1 ){
        	return false;
        }
    }

}
