<?php


Abstract Class WP_UserBase implements JsonSerializable
{
	public $ID;
	public $_user;

	public $usernameIsEmail = TRUE;
	public $prefix      = '';
	public $attributes  = [];
	protected $data     = [];
	
	public $default     = [];
	public $virtual     = [];
	public $filter      = [];

	public $new         = TRUE;
	public $dirty       = FALSE;
	public $booted      = FALSE;
	


	/**
	 * Create a new instace with data
	 *
	 * @param array $insert
	 * @return void
	 */
	public function __construct(Array $insert = [])
	{
		if(!empty($this->default)){
			foreach($this->default as $attribute => $value){
				$this->data[$attribute] = $value;
			}
		}

		foreach($insert as $attribute => $value){
			if(in_array($attribute, $this->attributes)){
				$this->set($attribute, $value);
			}
		}
	
		$this->boot();
	}

	/**
	 * Initalize the model, load in any addional data
	 *
	 * @return void
	 */
	protected function boot()
	{
		$this->triggerEvent('booting');

		if(isset($this->ID)){
			$this->new = FALSE;
			$this->_user = get_userdata($this->ID);
			$this->setRaw('username', ($this->usernameIsEmail)? $this->_user->user_email : $this->_user->user_login);
			$this->setRaw('password', $this->_user->user_pass);
			$this->setRaw('email', $this->_user->user_email);

			foreach($this->attributes as $attribute){
				$meta = $this->getMeta($attribute);
				if(empty($meta) && isset($this->default[$attribute])){
					$this->set($attribute, $this->default[$attribute]);
				}else{
					$this->set($attribute, $meta);
				}
			}
		}

		$this->booted = TRUE;
		$this->triggerEvent('booted');
	}

	/**
	 * Create a new model with data, save and return the model
	 *
	 * @param array $insert
	 */
	public static function insert(Array $insert = [])
	{
		return Self::newInstance($insert)->save();
	}

	// ----------------------------------------------------
	// FINDERS
	// ----------------------------------------------------
	/**
	 * Find model by it's post ID
	 *
	 * @param  int $ID
	 * @return Object|NULL
	 */
	public static function find($ID)
	{
		if(Self::exists($ID)){
			$user = Self::newInstance();
			$user->ID = $ID;
			$user->boot();
			return $user;
		}

		return NULL;
	}

	/**
	 * Get model by ID without booting the model
	 *
	 * @param  int $ID
	 * @return Object|NULL
	 */
	public static function findBypassBoot($ID)
	{
		if(Self::exists($ID)){
			$user = Self::newInstance();
			$user->ID = $ID;
			return $user;
		}

		return NULL;
	}

	/**
	 * Find the model by ID. If the post does not exist throw.
	 *
	 * @param  int $id
	 * @return object
	 *
	 * @throws  \Exception
	 */
	public static function findOrFail($ID)
	{
		if(!Self::exists($ID)){
			throw new Exception("User {$ID} not found");
		}

		return Self::find($ID);
	}

	/**
	 * Returns all models
	 *
	 * @param  string $limit
	 * @return array
	 */
	public static function all()
	{
		$return = [];
		foreach(get_users() as $user){
			$return[] = Self::find($user->id);
		}

		return $return;
	}

	/**
	 * Retun an array of models as asoc array. Key by $value
	 *
	 * @param  string  $value
	 * @param  array   $models
	 * @return array
	 */
	public static function asList($value = NULL, $users = FALSE)
	{
		if(!is_array($users)){
			$users = Self::all();
		}

		$return = [];
		foreach($users as $user){
			if($user instanceof WP_User){
				$user = $user->id;
			}else if($user instanceof WP_UserBase){
				$user = $user->ID;
			}

			if(Self::exists($user)){
				$return[$userID] = Self::find($user);
			}
		}

		return $return;
	}

	/**
	 * @return void
	 */
	public static function in(Array $IDs)
	{
		$results = []; 
		foreach($IDs as $id){
			if(Self::exists($id)){
				$results[] = Self::find($id);
			}
		}

		return $results;
	}


	// -----------------------------------------------------
	// EVENTS
	// -----------------------------------------------------
	/**
	 * Fire event if the event method exists
	 *
	 * @param  string $event event name
	 * @return bool
	 */
	protected function triggerEvent($event)
	{
		if(method_exists($this, $event)){
			$this->$event($this);
			return TRUE;
		}

		return FALSE;
	}


	// -----------------------------------------------------
	// HOOKS
	// -----------------------------------------------------
	/**
	 * Add hooks
	 *
	 * @return void
	 */
	public static function addHooks()
	{
		// add_action('save_post', [get_called_class(), 'onSave'], 9999999999);
	}

 	/**
	 * Remove hooks
	 *
	 * @return void
	 */
	public static function removeHooks()
	{
		// remove_action('save_post', [get_called_class(), 'onSave'], 9999999999);
	}

	/**
	 * save_post hook: Triggers save method for a given post
	 * 
	 * Note: Self::exists() checks if the post is of the correct post type
	 *
	 * @param int $ID
	 * @return void
	 */
	public static function onSave($ID)
	{
		if(Self::exists($ID)){
			$post = Self::find($ID);
			$post->save();
		}
	}


	// -----------------------------------------------------
	// UTILITY METHODS
	// -----------------------------------------------------
	/**
	 * Returns a new model
	 *
	 * @return object
	 */
	public static function newInstance($insert = [])
	{
		$class = get_called_class();
		return new $class($insert);
	}

	/**
	 * Create a new model without calling the constructor.
	 *
	 * @return object
	 */
	protected static function newWithoutConstructor()
	{
		$class = get_called_class();
		$reflection = new ReflectionClass($class);
		return $reflection->newInstanceWithoutConstructor();
	}

	public function isArrayOfModels($array)
	{
		if(!class_exists('WP_Model') || !is_array($array)){
			return FALSE;
		}

		$types = array_unique(array_map('gettype', $array));
		return (count($types) === 1 && $types[0] === "object" && $array[0] instanceof WP_Model);
	}

	public static function extract($array, $column)
	{
		$return = [];

		if(is_array($array)){
			foreach($array as $value){
				if(is_object($value)){
					$return[] = @$value->$column;
				}elseif(is_array($value)){
					$return[] = @$value[$column];
				}
			}
		}

		return $return;
	}

 	private function getAttributes()
 	{
 		return array_merge($this->attributes, []);
 	}

	/**
	 * Returns an array representaion of the model for serialization
	 *
	 * @return array
	 */
	public function jsonSerialize()
    {
        return $this->toArray();
    }

    // -----------------------------------------------------
	// VIRTUAL PROPERTIES
	// -----------------------------------------------------
    /**
     * Returns TRUE if $attribute is in the $virtual array
     * and has a corresponding vitaul property method
     *
     * @param  string $attribute
	 * @return bool
	 */
	public function isVirtualProperty($attribute)
	{
		return (
			(
				in_array($attribute, ['username', 'email', 'password'])
			) || (
				isset($this->virtual) &&
				in_array($attribute, $this->virtual) &&
				method_exists($this, ('_get'. ucfirst($attribute)))
			)
		);
	}

	/**
	 * Calls virtual property method
	 *
	 * @param  string $attribute
	 * @return mixed
	 */
	public function getVirtualProperty($attribute)
	{
		if($attribute === 'username'){
			if($this->usernameIsEmail === TRUE){
				return $this->getRaw('email');
			}

			return $this->getRaw('username');
		}elseif($attribute === 'email'){
			return $this->getRaw('email');
		}elseif($attribute === 'password'){
			if($this->new){
				return NULL;
			}

			return $this->_user->user_pass;
		}else{
			return call_user_func([$this, ('_get'. ucfirst($attribute))]);
		}
	}

	// -----------------------------------------------------
	// FILTER PROPERTIES
	// -----------------------------------------------------
	/**
     * Returns TRUE if $attribute is in the $filter array
     * and has a corresponding filter property method
     * OR
     * Returns TRUE if $attribute is in the $filter array
     * and the $filter array is an asoc array (:318)
     * and the value corresponding to the key ($attribute) has is the name of an exiting function.
     *
     * @param  string $attribute
	 * @return bool
	 */
	public function isFilterProperty($attribute)
	{
		return (
			(
				isset($this->filter) &&
				is_array($this->filter) &&
				in_array($attribute, $this->filter) &&
				method_exists($this, ('_filter'. ucfirst($attribute)))
			) || (	
				isset($this->filter) &&
				is_array($this->filter) &&
				count(array_filter(array_keys($this->filter), 'is_string')) > 0 &&
				in_array($attribute, array_keys($this->filter)) &&
				isset($this->filter[$attribute]) &&
				(
					function_exists($this->filter[$attribute]) || 
					$this->filter[$attribute] === 'the_content' ||
					class_exists($this->filter[$attribute])
				)
			)
		);
	}

	/**
	 * Calls filter property method
	 *
	 * @param  string $attribute
	 * @return mixed
	 */
	public function getFilterProperty($attribute)
	{
		if( count(array_filter(array_keys($this->filter), 'is_string')) > 0 &&
			isset($this->filter[$attribute])){

			if(function_exists($this->filter[$attribute])){
				return call_user_func_array([$this->filter[$attribute]], [$this->get($attribute)]);
			}elseif(class_exists($this->filter[$attribute])){


				// AB: Look into this
				$className = $this->filter[$attribute];
				if(is_array($this->get($attribute))){
					if($this->isArrayOfModels($this->get($attribute))){
						return $this->get($attribute);
					}

					$return = [];
					foreach($this->get($attribute) as $model){
						if($className::exists($model)){
							$return[] = $className::find($model);
						}
					}

					return $this->{$attribute} = &$return;
				}else{
					if(is_object($this->get($attribute))){
						return $this->get($attribute);
					}
					return $this->{$attribute} = $className::find($this->get($attribute)); 
				}
			}

			return NULL;
		}

		return call_user_func_array([$this, ('_filter'. ucfirst($attribute))], [$this->get($attribute)]);
	}


	// -----------------------------------------------------
	// Meta
	// -----------------------------------------------------
	/**
	 * Returns meta value for a meta key
	 *
	 * @param  string meta_key
	 * @return string
	 */
    public function getMeta($key)
    {
		return get_user_meta($this->ID, ($this->prefix.$key), TRUE);
	}

	/**
	 * Set meta value for a meta key
	 *
	 * @param  string meta_key
	 * @param  string meta_value
	 * @return void
	 */
	public function setMeta($key, $value)
	{
		if(is_object($value) && $value instanceof WP_Model){
			if($value->new || $value->dirty){
				$value->save();
			}

			$value = $value->ID;
		}elseif(is_object($value) && $value instanceof WP_UserBase){
			if($value->new || $value->dirty){
				$value->save();
			}

			$value = $value->ID;
		}elseif($this->isArrayOfModels($value)){
		   	$IDs = [];
			foreach($value as $model){
				if($model->new || $model->dirty){
					$model->save();
				}

				$IDs[] = $model->ID;
			}

			$value = $IDs;	
		}

		update_user_meta($this->ID, ($this->prefix.$key), $value);
	}

	/**
	 * Delete meta's meta
	 *
	 * @param  string meta_key
	 * @return void
	 */
	public function deleteMeta($key)
	{
		delete_user_meta($this->ID, ($this->prefix.$key));
	}


    // -----------------------------------------------------
	// GETTERS & SETTERS
	// -----------------------------------------------------
	/**
	 * Get property of usee
	 *
	 * @param  property $attribute
	 * @return mixed
	 */
	public function get($attribute){
		return $this->$attribute;
	}

	public function getRaw($attribute, $default = NULL){
		if(!isset($this->data[$attribute])){
			return $default;
		}

		return $this->data[$attribute];
	}

	public function set($attribute, $value){
		$this->$attribute = $value;
	}

	public function setRaw($attribute, $value){
		$this->data[$attribute] = $value;
		return TRUE;
	}

	// -----------------------------------------------------
	// MAGIC METHODS
	// -----------------------------------------------------
	/**
	 * @return void
	 */
	public function __set($attribute, $value)
	{
		if($this->booted){
			$this->dirty = true;
		}

		$this->setRaw($attribute, $value);
	}

	/**
	 * @return void
	 */
	public function __get($attribute)
	{
		if(in_array($attribute, $this->getAttributes())){
			if($this->isFilterProperty($attribute)){
				return $this->getFilterProperty($attribute);
			}

			return $this->getRaw($attribute);
		}else if($this->isVirtualProperty($attribute)){
			return $this->getVirtualProperty($attribute);
		}

		return NULL;
	}


	// -----------------------------------------------------
	// HELPER METHODS
	// -----------------------------------------------------
	/**
	 * Check if the post exists by Post ID
	 *
	 * @param  string|int   $ID   Post ID
	 * @return bool
	 */
	public static function exists($ID)
	{
		return (Self::getWPUserByID($ID) instanceof WP_USER);
	}

	public static function getWPUserByID($ID){
		return get_userdata($ID);
	}

	public function checkPassword($password){
		return wp_check_password($password, $this->_user->user_pass, $this->ID);
	}

	public function updatePassword($password){
		return wp_set_password($password, $this->ID);
	}

	/**
	 * Clear Auth cookies and Login as user
	 * @return bool TRUE
	 */
	public function login()
	{
		wp_clear_auth_cookie();
    	wp_set_current_user($this->ID);
    	wp_set_auth_cookie($this->ID);
    	return TRUE;
	}

	public static function auth()
	{
		if(!is_user_logged_in()){
			return FALSE;
		}

		return Self::find(get_current_user_id());
	}

	/**
	 * Returns the original WP_User object
	 *
	 * @return WP_Post
	 */
	public function user()
	{
		return $this->_user;
	}

	/**
	 * Returns an asoc array representaion of the model
	 *
	 * @return array
	 */
	public function toArray()
	{
		$model = [];

		foreach($this->attributes as $key => $attribute){
			if(!empty($this->protected) && !in_array($attribute, $this->protected)){
				// Do not add to $model
			}else{
				$model[$attribute] = $this->get($attribute);
			}
		}

		if(!empty($this->serialize)){
			foreach($this->serialize as $key => $attribute){
				if(!empty($this->protected) && !in_array($attribute, $this->protected)){
					// Do not add to $model
				}else{
					$model[$attribute] = $this->get($attribute);
				}
			}
		}

		$model['ID'] = $this->ID;

		return $model;
	}

	// -----------------------------------------------------
	// SAVE
	// -----------------------------------------------------
	/**
	 * Save the model and all of it's associated data
	 *
	 * @param Array $overrides  List of parameters to override for wp_insert_post(), such as post_status
	 *
	 * @return Object $this
	 */
	public function save($overrides = [])
	{
		$this->triggerEvent('saving');

		$overwrite = array_merge($overrides, [
			// 
		]);

		Self::removeHooks();

		if(!$this->new){
			$defaults = [
				'ID' => $this->ID,
			];

			wp_update_user(array_merge($defaults, $overwrite));
		}else{
			$this->triggerEvent('inserting');
			$this->ID = wp_create_user($this->get('username'), $this->get('password'), $this->get('email'));
			$this->_user = Self::getWPUserByID($this->ID);
			$this->triggerEvent('inserted');
		}

		Self::addHooks();

		foreach($this->attributes as $attribute){
			$this->setMeta($attribute, $this->get($attribute, ''));
		}

		$this->triggerEvent('saved');
		$this->dirty = FALSE;
		$this->new = FALSE;
		return $this;
	}

	// -----------------------------------------------------
	// DELETE
	// -----------------------------------------------------
	/**
	 * @return void
	 */
	public function delete()
	{
		$this->triggerEvent('deleting');
		wp_delete_user($this->ID);
		$this->triggerEvent('deleted');
	}
}