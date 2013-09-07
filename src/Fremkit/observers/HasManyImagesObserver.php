<?php

class HasManyImagesObserver {

    public function saving($model)
    {
        if (Input::hasFile('image')){

			$image = array();
			//Safest way
			$image['original'] = Input::file('image')->getClientOriginalName();
			$image['ext'] = Input::file('image')->getClientOriginalExtension();
			$image['filename'] = date('YmdHis').'.'.$image['ext'];
			$image['path'] = '/uploads/'.$image['filename'];

			$image['size'] = Input::file('image')->getSize();
			$image['mime'] = Input::file('image')->getMimeType();

			$destinationPath = public_path().'/uploads/';

			Input::file('image')->move($destinationPath, $image['filename']);

			$image['imageable_type'] = get_class($model);

			if(isset($model->id) && !empty($model->id)){
				$image['imageable_id'] = $model->id;
			}else{
				$image['imageable_id'] = DB::table($model->getTable())->count() + 1;
			}

			$Image = Image::create($image);
			
		}
    }

}