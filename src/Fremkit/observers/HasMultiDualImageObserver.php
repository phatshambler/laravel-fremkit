<?php

//Has multiple versions of one image (multi-lingual for instance) and handles array uploading

class HasMultiDualImageObserver extends MetaMorphCatOrderObserver{
	use UploadTrait;

	protected $fieldName = 'dual_image';
	protected $functionName = 'dual_imageable';

    protected $imageModel = 'DualImage';
	protected $imageTable = 'dual_images';
	protected $imagePrimary = 'id';

    protected $dualAlt = '_alt';

    protected $path = '/uploads/';

    //ASSUMING YOURE USING ARRAYS. For the image, category and order. And put null by default. Please.

    public function __construct(){

        parent::__construct();

        $this->altFieldName = $this->fieldName.$this->dualAlt;
    }

    public function saving($model)
    {
    	$saving_model = $this->imageModel;

        if ( Input::hasFile( $this->fieldName ) || Input::hasFile( $this->altFieldName ) ){

            //The arrays
        	$input = Input::file( $this->fieldName );
            $input_alt = Input::file( $this->altFieldName );

            //var_dump($input)

        	$cat = Input::get( $this->category );
            $order = Input::get( $this->order );

        	for($i = 0; $i < count($input); $i++){

                $old_one = array();
                //Try to find the onld one
                if(!is_null($model->id)){
                    $old_one = $saving_model::where($this->id, $model->id)
                    ->where($this->type, get_class($model))
                    ->where($this->category, $cat[$i])
                    ->where($this->order, $order[$i])
                    ->first();
                }

                if( !isset($old_one) || empty($old_one) || is_null($old_one) ){
                    $old_one = array();
                }else{
                    $old_one = $old_one->toArray();
                }

        		//Skip if both empty
        		if(is_null($input[$i]) && is_null($input_alt[$i])){
                    continue;
                }else if(!is_null($input[$i]) && is_null($input_alt[$i])){

                    $image = $this->upload( $input[$i], $old_one, $this->path, '' );

                }else if(is_null($input[$i]) && !is_null($input_alt[$i])){

                    $image = $this->upload( $input_alt[$i], $old_one, $this->path, $this->dualAlt );

                }else{
                    $image = $this->upload( $input[$i], $old_one, $this->path, '' );
                    $image = $this->upload( $input_alt[$i], $image, $this->path, $this->dualAlt );
                }
        		
        		$image = $this->setTypeAndId( $image, $model );

                $image = $this->setCategory($image, $cat[$i]);

                //Try to clear the last one
                //$this->clear( $image, $cat[$i], $order[$i] );

                $image = $this->setOrder($image, $cat[$i], $order[$i]);

                //var_dump($old_one, $image); exit();
                //Save
                if(!isset($image['id'])){
                    $saving_model::create($image);
                }else{
                    $m = $saving_model::find($image['id']);
                    $m->update($image);
                }
        	}
			
		}

    }

}