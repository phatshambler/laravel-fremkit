<?php

//Has multiple versions of one image (multi-lingual for instance) and handles array uploading

class HasOneComplexImageObserver extends MetaImageObserver{

	protected $fieldName = 'banner';
	protected $functionName = 'bannerable';

    protected $imageModel = 'Image';
	protected $imageTable = 'images';
	protected $imagePrimary = 'id';

    public function saving($model)
    {
    	$saving_model = $this->imageModel;
        
        if ( Input::hasFile( $this->fieldName ) ){

        	$input = Input::file( $this->fieldName );

        	if(Input::has( $this->category )){
        		$cat = Input::get( $this->category );
        	}
        	
        	if(!is_array($input)){
        		$input = array( $input );
        	}

        	if(isset($cat) && !is_array($cat)){
        		$cat = array( $cat );
        	}

        	for($i = 0; $i < count($input); $i++){

        		//Skip if empty
        		if(is_null($input[$i])) continue;

        		//Upload the file and get it's properties
        		$image = $this->upload( $input[$i] );

        		//Set it's type and id
        		$image = $this->setTypeAndId( $image, $model );

        		if(isset($cat) && isset($cat[$i]) && !empty($cat[$i])){
        			
        			//Clear before setting the order
        			$this->clear( $image, $cat[$i] );

        			$image = $this->setCategory($image, $cat[$i]);
        			$image = $this->setOrder($image, $cat[$i]);

        		}else{

        			//Clear before setting the order
        			$this->clear( $image );
        			
        			$image = $this->setOrder( $image );
        			
        		}

        		//Save
        		$saving_model::create($image);
        	}
			
		}

    }

}