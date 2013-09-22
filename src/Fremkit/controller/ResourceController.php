<?php

/**
*
*	Resource Controller (extend this)
*
**/

class ResourceController extends \Illuminate\Routing\Controllers\Controller {

	/**
	*	The default configuration
	**/

	protected $_config = array(

		'view' => array(
			'base' => array(
				'routePath' => 'admin.pages',
				'name' => 'pages',
				'item' => 'page',
				'controller' => '',
			),
			'paths' => array(
				'index' => 'admin.default.index',
				'show' => 'admin.default.show',
				'edit' => 'admin.default.edit',
				'edit-ajax' => 'admin.default.edit-ajax',
			),
			'display' => array(
				'gender' => '',
				'label' => 'nom',
			),
			'table' => array(
				'id' => 'Id'
			),
		),
		'model' => array(
			'base' => array(
				'name' => '',
				'rpp' => 15,
				'csrf' => 1,
			),
		),
	);

	/**
	*	The real configuration (OVERRIDE THIS IN YOUR EXTENDED CONTROLLER)
	**/
	protected $config;

	/**
	*	Keeps the results to see if everything went right
	**/
	protected $_success = array();

	public function __construct()
	{
		
		//Merge the default and the controller config
		$this->config = $this->array_merge_recursive_distinct($this->_config, $this->config);

		//Get the name
		$this->config['view']['base']['controller'] = get_class($this);

		//Language shit?? bleh
		$flatName = 'models.'.$this->config['view']['base']['item'];

		$this->config['singular'] = Lang::get($flatName.'.singular');
		$this->config['plural'] = Lang::get($flatName.'.plural');
		$this->config['le_singular'] = Lang::get($flatName.'.le_singular');
		$this->config['le_plural'] = Lang::get($flatName.'.le_plural');

		//Share the config with the views (could be improved)
		View::share('controller', $this->config);

		//The model name for reflection
		$this->_model = $this->config['model']['base']['name'];
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{	
		$model = $this->_model;

		//Get all items
		if(Request::ajax()){
			$items = $model::getAllAjax();
		}else{
			$items = $model::getAll();
		}

		$this->exists($items);

		if( Request::ajax() ) {
			//Return all users
		    return $this->toJson( $this->success(), 'Here\'s many cookies.', $item->toArray() );
		}else{
			//Return the index / list layout
			$this->layout->content = View::make($this->config['view']['paths']['index'], array('items' => $items) );
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if(Request::ajax()){
			//Ajax form view
			return View::make($this->config['view']['paths']['edit-ajax'], array('item' => array()));
		}else{
			//Show the edit layout
			$this->layout->content = View::make($this->config['view']['paths']['edit'], array('item' => array()) );
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{	
		$this->csrf();

		$model = $this->_model;

		$item = new $model(Input::all());

		$this->exists($item);

		$this->_success[] = $item->save() === true ? true:false;

		$message = $this->getMessage($this->success(), 'save');

		if(Request::ajax()){
			return $this->toJson($this->success(), $message, $item->toArray());
		}else{
			
			if($this->success()){
				return Redirect::action(get_class($this).'@index')->with('flash_success', $message );
			}else{
				return Redirect::action(get_class($this).'@create')->withErrors($item->errors)->withInput();
			}
		}

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$item = $this->getItem($id);

		if( Request::ajax() ) {
			//Return the requested item
		    return $this->toJson($this->success(), 'Here\'s a komodo dragon', $item->toArray());
		}else{
			//Return the useless show page
			$this->layout->content = View::make($this->config['view']['paths']['show'], array('item' => $item) );
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$item = $this->getItem($id);

		if(Request::ajax()){
			//Ajax form view
			return View::make($this->config['view']['paths']['edit-ajax'], array('item' => $item));
		}else{
			//Show the edit layout
			$this->layout->content = View::make($this->config['view']['paths']['edit'], array('item' => $item) );
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{	

		$this->csrf();

		$item = $this->getItem($id);

		//Update is good and saves automatically
		$this->_success[] = $item->update( Input::all() ) === true ? true:false;

		$message = $this->getMessage($this->success(), 'update', $id);

		if(Request::ajax()){
			return $this->toJson($this->success(), $message, $item->toArray());
		}else{
			
			if($this->success()){
				return Redirect::action(get_class($this).'@index')->with('flash_success', $message );
			}else{
				return Redirect::action(get_class($this).'@edit', array( $item->getKey() ) )->withErrors($item->errors)->withInput();
			}
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->csrf();

		$item = $this->getItem($id);

		$this->_success[] = $item->delete() === true ? true:false;

		//The undelete URL
		$undo = ' <a href="'.URL::action(get_class($this).'@undelete', $id).'">Undo</a>';

		//The message to display
		$message = $this->getMessage($this->success(), 'delete', $id, $undo);

		//The response
		if(Request::ajax()){
			return $this->toJson($this->success(), $message, array());
		}else{
			return $this->toLast($message);
		}

	}

	/**
	 * Remove the specified resource from storage - without fucking around with fake methods
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function kill($id)
	{

		$item = $this->getItem($id);

		$this->_success[] = $item->delete() === true ? true:false;

		//The undelete URL
		$undo = ' <a href="'.URL::action(get_class($this).'@undelete', $id).'">Undo</a>';

		//The message to display
		$message = $this->getMessage($this->success(), 'delete', $id, $undo);

		//The response
		if(Request::ajax()){
			return $this->toJson($this->success(), $message, array());
		}else{
			return $this->toLast($message);
		}

	}

	/**
	 * Remove the specified resource from deleted state. Works only with soft deletes of course.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function undelete($id)
	{
		
		$model = $this->_model;

		$item = $model::getOneTrashed($id);

		$this->exists($item);

		$this->_success[] = $item->restore() === true ? true:false;

		$message = $this->getMessage($this->success(), 'undelete', $id);

		if(Request::ajax()){
			return $this->toJson($this->success(), $message, $item->toArray());
		}else{
			return $this->toLast($message);
		}

	}

	/**
	 * Move one item up intelligently (column name: order).
	 *
	 * @param  int  $id
	 * @return Response
	 */
    protected function up($id)
    {
        $model = $this->_model;

        $item = $model::getOne($id);

        $count = $model::getOrderCount($item);

        if ( !is_null($item->order) ){

            if($item->order > 1){

                //Number that we want
                $target = $item->order - 1;

                //Change the other one
                $other = $model::getOneByOrder($target, $item);

                if( isset($other) && !empty($other) ){
                	$other->order = $item->order;
                	$other->save();
            	}

                //Change this one
                $item->order = $target;
                $item->save();
                
            }elseif($item->order < 1){
            	$model::increment('order');
            	$item->order = 1;
                $item->save();
            }else{
        		$this->_success[] = false;
        	}

        	$this->testSort($item);

        }else{
        	$this->_success[] = false;
        }

        $message = $this->getMessage($this->success(), 'up', $id);

        if(Request::ajax()){
			return $this->toJson($this->success(), $message, $item->toArray());
		}else{
			return $this->toLast($message);
		}

    }

	/**
	 * Move one item down intelligently (column name: order).
	 *
	 * @param  int  $id
	 * @return Response
	 */
    protected function down($id)
    {
        $model = $this->_model;

        $item = $model::getOne($id);

        $count = $model::getOrderCount($item);

        if ( !is_null($item->order) ){

            if($item->order < $count){

                //Number that we want
                $target = $item->order + 1;

                //Change if there's another one
                $other = $model::getOneByOrder($target, $item);
                if( isset($other) && !empty($other) ){
                	$other->order = $item->order;
                	$other->save();
                }
                
                //Change this one
                $item->order = $target;
                $item->save();

                $this->_success[] = true;

            }elseif($item->order > $count){
            	$item->order = $count;
                $item->save();
            }else{
        		$this->_success[] = false;
        	}

        	$this->testSort($item);

        }else{
        	$this->_success[] = false;
        }

        $message = $this->getMessage($this->success(), 'down', $id);

        if(Request::ajax()){
			return $this->toJson($this->success(), $message, $item->toArray());
		}else{
			return $this->toLast($message);
		}

    }

    /**
	 * Activate a ressource (column name: active).
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function activate($id)
	{	
		$this->csrf();

		$item = $this->getItem($id);

		//Update is good and saves automatically
		$this->_success[] = $item->update( array('active' => 1) ) === true ? true:false;

		$message = $this->getMessage($this->success(), 'activate', $id);

		if(Request::ajax()){
			return $this->toJson($this->success(), $message, $item->toArray());
		}else{
			return $this->toLast($message);
		}
	}

	/**
	 * Deactivate a ressource (column name: active).
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deactivate($id)
	{	
		$this->csrf();

		$item = $this->getItem($id);

		//Update is good and saves automatically
		$this->_success[] = $item->update( array('active' => 0) ) === true ? true:false;

		$message = $this->getMessage($this->success(), 'deactivate', $id);

		if(Request::ajax()){
			return $this->toJson($this->success(), $message, $item->toArray());
		}else{
			return $this->toLast($message);
		}
	}

	/**
	 * Checks for sorting after up and down
	 *
	 * @param  Eloquent model $item
	 * @return void
	 */
    protected function testSort($item){

    	$model = $this->_model;

    	$items = $model::getOrderItems($item);

    	$i = 1;
    	foreach($items as $item){

    		if($item->order != $i){
    			$this->autoSort($items);
    			return false;
    		}
    		$i++;
    	}

    	return true;
    }

    protected function autoSort($items){
    	$i = 1;
    	foreach($items as $item){
    		$item->order = $i;
    		$item->save();
    		$i++;
    	}
    }

	/**
	 * Checks for csrf
	 *
	 * @param  int  $id
	 * @return true
	 */
	protected function csrf()
	{
		if($this->config['model']['base']['csrf'] == 1){
			$this->beforeFilter('csrf');
		}

		return true;
	}

	/**
	 * Get a single item
	 *
	 * @param  int  $id
	 * @return Eloquent model
	 */
	protected function getItem($id)
	{
		$_model = $this->_model;

		$item = $_model::getOne($id);

		$this->exists($item);

		return $item;
	}

	/**
	 * Build the message you want to show from language (/lang/rest.php) and model
	 *
	 * @param  int  $id
	 * @return string
	 */
	protected function getMessage($success, $action = '', $id = '', $extra = '')
	{
		$name = '';
		//The type of item we're changing and it's id
		$name = $this->config['view']['display']['label'];
		if(!empty($id)){
			$name .= ' (#'.$id.')';
		}

		//Check for gender in the lang array
		if( isset($this->config['view']['display']['gender']) && !empty($this->config['view']['display']['gender']) ){
			$action .= '.'.$this->config['view']['display']['gender'];
		}

		//get the language rest array key
		if($success){
			$key = 'rest.'.$action.'.success'; 
		}else{
			$key = 'rest.'.$action.'.failure';
		}

		//The message
		if(Lang::get($key) != $key){
			$message = Lang::get($key, array( 'item' => $name ) );
		}else{
			$message = $action;
		}

		//Anything else to add after the default message
		if(!empty($extra)){
			$message .= ' '.$extra;
		}

		return $message;
	}

	/**
	 * Build a JSON response
	 *
	 * @param  string  $success
	 * @param  string  $message
	 * @param  array  $data
	 * @return Response
	 */
	protected function toJson($success, $message, $data)
	{
		return Response::json( array('success' => $success, 'message' => $message, 'data' => $data ) );
	}

	/**
	 * Build a regular HTTP response
	 *
	 * @param  string  $message
	 * @return Response
	 */
	protected function toIndex($message)
	{
		if($this->success()){
			return Redirect::action(get_class($this).'@index')->with('flash_success', $message );
		}else{
			return Redirect::action(get_class($this).'@index')->with('flash_error', $message );
		}
		
	}

	/**
	 * Back to the last page
	 *
	 * @param  string  $message
	 * @return Response
	 */
	protected function toLast($message)
	{
		if($this->success()){
			return Redirect::back()->with('flash_success', $message );
		}else{
			return Redirect::back()->with('flash_error', $message );
		}
		
	}

	/**
	 * Checks for existence of things (yeah)
	 *
	 * @param  mixed  $item
	 * @return true
	 */
	protected function exists($item)
	{
		if(!isset($item) || empty($item)){

			//Catch it somewhere else if you want 
			throw new ModelNotFoundException;
		}
		return true;
	}

	/**
	 * Checks if all operations went well during the modifications to the model
	 *
	 * @return bool
	 */
	protected function success()
	{
		for($i = 0; $i < count($this->_success); $i++){
			if($this->_success[$i] === false){
				return false;
			}
		}
		return true;
	}

	protected function array_merge_recursive_distinct( array &$array1, array &$array2 )
	{
		$merged = $array1;

		foreach ( $array2 as $key => &$value ){
			if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ){
				$merged [$key] = $this->array_merge_recursive_distinct ( $merged [$key], $value );
			}
			else{
				$merged [$key] = $value;
			}
		}

		return $merged;
	}



}
