<?php

class UndeleteableObserver {

    public function deleting($model)
    {
    	if( !is_null($model->undeleteable) && $model->undeleteable == 1 ){
        	return false;
        }
    }

}
