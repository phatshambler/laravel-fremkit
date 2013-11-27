<?php

class MetaImageObserver {

    protected $fieldName = 'banner';
	protected $functionName = 'bannerable';

	protected $imageModel = 'Image';
	protected $imageTable = 'images';
	protected $imagePrimary = 'id';

	public function __construct(){

		$this->id = $this->functionName.'_id';
		$this->type = $this->functionName.'_type';
		$this->category = $this->functionName.'_cat';
		$this->order = $this->functionName.'_order';
	}

    protected function setTypeAndId($image, $model)
    {
    	//Link with the model polymorphic style
    	$reflect = get_class($model);
		$image[$this->type] = $reflect;

		if(isset($model->id) && !empty($model->id)){
			//If old
			$image[$this->id] = $model->id;
		}else{
			//If new
			$image[$this->id] = $reflect::count() + 1;
		}

		return $image;
    }

    protected function setCategory($image, $cat = '')
    {
    	if ( Schema::hasColumn( $this->imageTable , $this->category ) && !empty($cat) )
		{
		    $image[$this->category] = $cat;
		}
		return $image;
    }

    protected function setOrder($image, $cat = '', $order = '')
    {
    	$model = $this->imageModel;
    	//Manage the ordering on save - no updates for now
		if ( Schema::hasColumn( $this->imageTable , $this->order) )
		{
		    //When creating a new image
		    if( !isset($image[$this->imagePrimary]) && empty($order) ){

		    	$query = $model::where($this->id, $image[$this->id])->where($this->type, $image[$this->type]);

		    	//Check for the category situation
		    	if( Schema::hasColumn( $this->imageTable, $this->category) && isset($image[$this->category]) && !empty($image[$this->category]) ){
		    		$query->where($this->category, $image[$this->category]);
		    	}

    			$count = $query->count();
    			$image[$this->order] = $count + 1;

    		}else if(!empty($order)){
    			$image[$this->order] = $order;
    		}
		}
		return $image;

    }

    protected function clear($image, $cat = '', $order = '')
    {
    	//Stupid php reflection shite
    	$model = $this->imageModel;
		//Clean the old one(s)
		$query = $model::where($this->id, $image[$this->id])->where($this->type, $image[$this->type]);
		
		//Test for type
		if ( !empty($cat) ){
			$query->where($this->category, $cat);
		}
		if ( !empty($order) ){
			$query->where($this->order, $order);
		}

		$items = $query->get();

		foreach($items as $i){
			$i->delete();
		}

    }

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

		//Get the image data if it's an image
		if( in_array(strtolower($image['ext'.$suffix]), array('png', 'jpeg', 'jpg', 'gif')) ){
			$metadata = getimagesize($destinationPath.$image['filename'.$suffix]);
			$image['width'.$suffix] = $metadata[0];
			$image['height'.$suffix] = $metadata[1];
		}

    	return $image;
    }

}