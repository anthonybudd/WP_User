<?php

Abstract Class WP_UserBase{

	public $ID;
	public $_user;
	public $_data = [];


	public function __construct()
	{
		
	}

	protected static function newWithoutCallingConstructor()
	{
		$reflector = new ReflectionClass(Self::class);
		return $reflector->newInstanceWithoutConstructor();
	}

	public static function find($ID)
	{
		if(Self::exists($ID)){
			return FALSE;
		}

		$user = Self::newWithoutCallingConstructor();
		$user->ID = $ID;
		$user->boot();
		return $user;
	}
   

    public static function boot()
    {
    	if(!isset($this->ID)){
    		throw new Exception("Can not boot without an ID");
    	}

    	if(isset($this->attributes)){
			foreach($this->attributes as $attribute){
				$user->_data[$attribute] = get_user_meta($user->ID, $attribute, TRUE);
			}
		}
    }
	
	public static function current()
	{
		if(!is_user_logged_in()){
			return FALSE;
		}

		return Self::find(get_current_user_id());
	}

	public function getWPUser(){
		return $this->_user;
	}

	public function __get($attribute){
		if(in_array($attribute, array_keys($this->_data))){
			return $this->_data[$attribute];
		}

		return NULL;
	}

	public function __set($attribute, $value){
		$this->_data[$attribute] = $value;
	}



	public static function get($attribute){
		$user = Self::current();
		if(!is_null($user)){
			return $user->$attribute;
		}
	}

	public static function set($attribute, $value){
		$user = Self::current();
		if($user instanceof Self){
			$user->$attribute = $value;
			$user->save();
			return TRUE;
		}

		return FALSE;
	}


	public function save(){
		foreach($this->attributes as $attribute){
			update_user_meta($this->ID, $attribute, $this->_data[$attribute]);
		}
	}

	public function auth(){
		wp_clear_auth_cookie();
    	wp_set_current_user($this->ID);
    	wp_set_auth_cookie($this->ID);
    	return TRUE;
	}

	public function delete(){

	}

	public static function exists($ID){
		return (get_userdata($ID) instanceof WP_USER);
	}
}