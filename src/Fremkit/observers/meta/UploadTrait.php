<?php

trait UploadTrait{

	protected function upload($input, $image = array(), $path = '/uploads/', $suffix = '')
    {
		//Safest way for filenames
		$image['original'.$suffix] = $input->getClientOriginalName();
		$image['ext'.$suffix] = $input->getClientOriginalExtension();

		//Stupid but if it saves too fast the date ain't good enough
		$image['filename'.$suffix] = date('YmdHis').'-'.rand(1,100000).'.'.$image['ext'.$suffix];
		$image['path'.$suffix] = $path.$image['filename'.$suffix];

		$image['size'.$suffix] = $input->getSize();
		$image['mime'.$suffix] = $input->getMimeType();

		//Could do better that this?
		$destinationPath = public_path().$path;

		$input->move($destinationPath, $image['filename'.$suffix]);

		//Get the image data
		$metadata = getimagesize($destinationPath.$image['filename'.$suffix]);
		$image['width'.$suffix] = $metadata[0];
		$image['height'.$suffix] = $metadata[1];

    	return $image;
    }

}