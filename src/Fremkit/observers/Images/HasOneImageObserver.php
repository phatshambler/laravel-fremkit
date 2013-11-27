<?php

class HasOneImageObserver {

    public function saving($model)
    {
        if (Input::hasFile('image')){
        	
			$image = array();

			//Safest way for filenames
			$image['original'] = Input::file('image')->getClientOriginalName();
			$image['ext'] = Input::file('image')->getClientOriginalExtension();
			$image['filename'] = date('YmdHis').'.'.$image['ext'];
			$image['path'] = '/uploads/'.$image['filename'];

			$image['size'] = Input::file('image')->getSize();
			$image['mime'] = Input::file('image')->getMimeType();

			//Could do better that this?
			$destinationPath = public_path().'/uploads/';

			Input::file('image')->move($destinationPath, $image['filename']);

			//Get the image data
			$metadata = getimagesize($destinationPath.$image['filename']);
			$image['width'] = $metadata[0];
			$image['height'] = $metadata[1];

			//Link with the model polymorphic style
			$image['imageable_type'] = get_class($model);

			if(isset($model->id) && !empty($model->id)){
				$image['imageable_id'] = $model->id;
			}else{
				$image['imageable_id'] = DB::table($model->getTable())->count() + 1;
			}

			//Clean the old one(s)
			$items = Image::where('imageable_id', $image['imageable_id'])->where('imageable_type', $image['imageable_type'])->get();
			
			foreach($items as $i){
				$i->delete();
			}

			$Image = Image::create($image);
			
		}

		//unset($model->image);
    }

}