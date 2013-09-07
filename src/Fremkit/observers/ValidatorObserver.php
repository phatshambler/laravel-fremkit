<?php namespace Niko\Observers;

class ValidatorObserver
{
    /**
     * The Model
     *
     * @var Object
     */
    protected $model;

    /**
     * Errors returned by the validator.
     *
     * @var Object
     */
    public $errors;

    /**
     * An array of items to be removed from input before saving.
     *
     * @var array
     */
    private $cleanDefaults = array('_token', '_method', 'password_confirmation', 'image', 'rpp');

    /**
     * This method for the saving event is added to the event queue in filters.php
     *
     * @return mixed
     */
    public function saving($model)
    {
        $this->model = $model;
        return $this->validate($model->rules);
    }


    /**
     * Validator method. Takes the rules of the model and applies them.
     *
     * @return boolean
     */
    public function validate(array $rules = array())
    {
        // if the validator isn't enabled or there are no rules return true.
        if(!isset($this->model->rules)) return true;

        $rules = self::processRules($rules ? $rules : $this->model->rules);
        $validator = Validator::make($this->model->getAttributes(), $rules, $this->model->messages ?: array());

        // the the model attributes are valid, return true.
        if ($validator->passes()) {
            //var_dump('xxx');exit();
            $this->cleanAttributes();
            $this->autoHash();
            return true;
        }

        // set the error messages to be returned.
        $this->model->errors = $validator->messages();
        return false;
    }

    /**
     * Process validation rules and replace ID's in "unique" rules.
     *
     * @param  array  $rules
     * @return array  $rules
     */
    protected function processRules(array $rules)
    {
        // get the model's ID.
        // If the ID is null, it's a new Model, so let's strip out the id completely.
        $id = $this->model->getKey();
        
        $replacement = $id == null ? "" : "," . $id;

        if(!empty($replacement)){

            array_walk($rules, function(&$item) use ($replacement)
            {
                // Replace placeholders
                $item = stripos($item, ',:id:') !== false ? str_ireplace(',:id:', $replacement, $item) : $item;
            });

        }else{

            array_walk($rules, function(&$item)
            {
                // Clear the last part of the string
                $item = stripos($item, ',:id:') !== false ? substr($item, 0, stripos($item, ',:id:') ) : $item;
            });

        }

        return $rules;
    }

    /**
     * Removes any attributes from the model that should not be saved to the database.
     *
     * @return void
     */
    public function cleanAttributes()
    {
        $cleaning = isset($this->model->clean) && is_array($this->model->clean) ? array_merge($this->model->clean, $this->cleanDefaults) : $this->cleanDefaults;

        foreach($cleaning as $attr):
            unset($this->model->$attr);
        endforeach;
    }

    /**
     * Automatically hashes specified attributes before their saved to the database.
     *
     * @return void
     */
    public function autoHash()
    {
        if (isset($this->model->autoHash) && is_array($this->model->autoHash)) {
            foreach($this->model->autoHash as $attr):

                // We should only hash attributes in the model that have changed.
                // We should ignore blank attributes - for example, the password may not have changed
                // so we don't want to change it.
                //var_dump($this->model->getOriginal($attr));
                //var_dump($this->model->$attr);exit();

                if ($this->model->getOriginal($attr) != $this->model->$attr && strlen($this->model->$attr) > 0) {
                    $this->model->$attr = Hash::make($this->model->$attr);
                }
            endforeach;
        }
    }

}
