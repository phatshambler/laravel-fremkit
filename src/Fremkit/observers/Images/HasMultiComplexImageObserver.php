<?php

//Has multiple versions of one image (multi-lingual for instance) and handles array uploading

class HasMultiComplexImageObserver extends MetaImageObserver{

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

            if(Input::has( $this->order )){
                $order = Input::get( $this->order );
            }
        	
        	if(!is_array($input)){
        		$input = array( $input );
        	}

        	if(isset($cat) && !is_array($cat)){
        		$cat = array( $cat );
        	}

            if(isset($order) && !is_array($order)){
                $order = array( $order );
            }

            //var_dump($input, $cat, $order);exit();

        	for($i = 0; $i < count($input); $i++){

        		//Skip if empty
        		if(is_null($input[$i])) continue;

        		//Upload the file and get it's properties
        		$image = $this->upload( $input[$i] );

        		//Set it's type and id
        		$image = $this->setTypeAndId( $image, $model );

        		if(isset($cat) && isset($cat[$i]) && !empty($cat[$i])){
        			
        			$image = $this->setCategory($image, $cat[$i]);

                    if(isset($order) && isset($order[$i]) && !empty($order[$i])){

                        //var_dump($image, $cat[$i], $order[$i]);exit();

                        $this->clear( $image, $cat[$i], $order[$i] );
                        $image = $this->setOrder($image, $cat[$i], $order[$i]);

                    }else{

                        $image = $this->setOrder($image, $cat[$i]);
                    }

        		}else{

        			if(isset($order) && isset($order[$i]) && !empty($order[$i])){
                        
                        $this->clear( $image, '', $order[$i] );
                        $image = $this->setOrder( $image, '', $order[$i] );

                    }else{
                        $image = $this->setOrder( $image );
                    }
        			
        		}

        		//Save
        		$saving_model::create($image);
        	}
			
		}

    }

}