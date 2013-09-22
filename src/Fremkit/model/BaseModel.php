<?php

/**
*
*   Base Model (extend this)
*
**/

class BaseModel extends \Illuminate\Database\Eloquent\Model
{

    /**
     *  The primary key of the table
     */
    protected $primaryKey = 'id';

    /**
     *  Enable or disable the soft deletes
     */
    protected $softDelete = true;

    /**
     *  The results per page you wanna have
     */
    protected static $rpp = 10;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array();

    /**
     * The accesible attributes
     *
     * @var array
     */
    protected $fillable = array();

    /**
     * Guarded attributes on the model
     *
     * @var array
     */
    protected $guarded = array();

    /**
     * Validation Rules
     *
     * @var array
     */
    public $rules = array();

    /**
     * Custom validation messages
     *
     * @var array
     */
    public $messages = array();

    /**
     * Attributes to automatically clean from the Input
     *
     * @var array
     */
    public $clean = array('image', 'video');

    /**
     * Attributes to be automatically hashed when saving
     */
    public $autoHash = array();

    /**
     * Override those functions in the module controllers
     */

    //For index
    public static function getAll($parent = null, $options = array()){
        return static::paginate(static::$rpp);
    }

    //For index/ajax
    public static function getAllAjax($parent = null, $options = array()){
        return static::all();
    }

    //Trashed
    public static function getTrashed($parent = null, $options = array()){
        return static::onlyTrashed()->get();
    }

    //Most things
    public static function getOne($id, $parent = null, $options = array()){
        return static::find($id);
    }

    //Undelete
    public static function getOneTrashed($id, $parent = null, $options = array()){
        return static::withTrashed()->find($id);
    }

    //Up and down the orders
    public static function getOrderCount($parent = null, $options = array()){
        return static::count();
    }

    //Get the items for a sub-category
    public static function getOrderItems($parent = null, $options = array()){
        return static::orderBy('order', 'asc')->get();
    }

    //Get/modify an order
    public static function getOneByOrder($order, $parent = null, $options = array()){
        return static::where('order', $order)->first();
    }

}
