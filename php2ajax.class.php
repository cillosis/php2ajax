<?php

// Turn on during debug
/* error_reporting(E_ALL);
ini_set('display_errors',1); */

/**
 * php2ajax
 *
 * Object oriented approach to handling AJAX interfaces between
 * Javascript and PHP.
 *
 * @category   General
 * @package    php2ajax
 * @author     Jeremy Harris <cillosis@gmail.com>
 * @copyright  2012 Jeremy Harris
 * @license    http://www.gnu.org/licenses/gpl.html  GNU General Public License v3.0
 * @version    Release: 0.1
 * @since      Class available since Release 0.1
 * @deprecated Class deprecated in Release 0.0
 */ 
class php2Ajax
{

	// Public
	//**************************************************
	public $hasRequest = false;
	public $hasGET = false;
	public $hasPOST = false;
	
	// Private
	//**************************************************
	private $rawRequest = array('post' => null, 'get' => null);
	
	// Protected
	//**************************************************
	protected $dynamicProperties = array();
	
	public function __construct()
	{
		// Initialize class
	}
	
	public function __set($variable, $value)
	{
		// Save undefined class properties
		$this->dynamicProperties[$variable] = $value;
	}
	
	public function __get($variable)
	{
		// Get undefined class properties
		if( isset($this->dynamicProperties[$variable]) )
		{
			return $this->dynamicProperties[$variable];
		} else {
			return null;
		}
	}
	
	public function __isset($name)
	{
		return isset($this->dynamicProperties[$name]);
	}
	
	/**************************************************
	* Method:	getRequest()
	***************************************************
	* Checks for GET and POST variables. If it receives
	* them, stores in object. If not, returns false.
	***************************************************/
	public function getRequest()
	{
		// Do we have GET?
		if( count($_GET) > 0 )
		{
			$this->hasRequest = true;
			$this->hasGET = true;
			$this->rawRequest['get'] = $_GET;
		}
		
		if( count($_POST) > 0 )
		{
			$this->hasRequest = true;
			$this->hasPOST = true;
			$this->rawRequest['post'] = $_POST;
		}
		
		// Return $this for method chaining
		return $this;
	}

	/**************************************************
	* Method:	filter()
	***************************************************
	* Filter user-input request and save clean request
	***************************************************/
	public function filter(array $filters)
	{
		
		foreach( array_keys($this->rawRequest) as $request_type)
		{
			// Filter GET input
			if( count($this->rawRequest[$request_type]) > 0 )
			{
				foreach( array_keys($this->rawRequest[$request_type]) as $key )
				{
					
					// Get working copy of request variable
					$temp_property = $this->rawRequest[$request_type][$key];
						
					foreach($filters as $filter)
					{
							
						// Perform filters on raw request property
						$temp_property = $filter($temp_property);
							
					}
					
					// Create new dynamic class property to hold 
					// filtered/sanitized request variable
					$this->{$key} = $temp_property;
					
				}
			}
		}
		// Return $this for method chaining
		return $this;
		
	}
	
	/**************************************************
	* Method:	save(save_object, save_callback)
	***************************************************
	* Call provided callback function to pass this object
	* to user-defined save method.
	***************************************************/
	public function save($save_object, $save_callback)
	{
	
		if( is_object($save_object) )
		{
			if( method_exists($save_object,$save_callback) )
			{
				if ( is_callable(array($save_object,$save_callback)) )
				{
					call_user_func(array($save_object,$save_callback), $this);
				} else {
					throw new Exception("Method \"" . $save_callback . "()\" is not callable. Verify it is not a private or protected method.");
				}
			} else {
				throw new Exception("Undefined method \"" . $save_callback . "\" in class \"" . get_class($save_object) . "\".");
			}
		} else { 
			throw new Exception("Parameter 1 must be an object instance!");
			return false; 
		}
		
	}
	
	/**************************************************
	* Method:	outputJSON($data)
	***************************************************
	* Take data from application and output as JSON
	***************************************************/
	public function outputJSON($data)
	{
	
		if( !empty($data) )
		{
			//header("Content-type: text/json");
			echo( json_encode($data) );
		} else {
			echo("Error: Unable to convert data to JSON");
		}
		
	}
	
}

?>
