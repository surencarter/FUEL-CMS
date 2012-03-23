<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL base library
 * 
 * This class is the base class that many other FUEL objects inherit from.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_base_library
 */

// --------------------------------------------------------------------

class Fuel_base_library {
	
	protected $CI; // reference to the CI super object
	protected $fuel; // reference to the fuel object
	protected $permission = ''; // permission required to run
	protected $init_permission_check = FALSE; // whether to check permissions on initialization or not
	protected $_errors = array(); // array to keep track of errors
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
		if (isset($this->CI->fuel))
		{
			$this->fuel =& $this->CI->fuel;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 * Also will set the values in the parameters array as properties of this object
	 *
	 * @access	public
	 * @param	array	Config preferences
	 * @return	void
	 */	
	function initialize($params = array())
	{
		if ($this->init_permission_check === TRUE)
		{
			$this->_check_permissions();
		}
		
		$this->set_params($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set object parameters
	 *
	 * @access	public
	 * @param	array	Config preferences
	 * @return	void
	 */
	function set_params($params)
	{
		if (!is_array($params) OR empty($params)) return;

		foreach ($params as $key => $val)
		{
			if (isset($this->$key) AND substr($key, 0, 1) != '_')
			{
				$this->$key = $val;
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns either an array of errors or a formatted string error message
	 *
	 * @access	public
	 * @param	boolean	Whether to format the error messages
	 * @param	string	The opening tag to append before the error messages
	 * @param	string	The closing tag to append before the error messages
	 * @return	mixed	Will return either an array of error messages or a formatted string if the first parameter is set to TRUE
	 */	
	function errors($formatted = FALSE, $open = '', $close = "\n\n")
	{
		if ($formatted === FALSE)
		{
			return $this->_errors;
		}

		$error = '';
		foreach($this->_errors as $e)
		{
			$error .= $open.$e.$close;
		}
		return $error;
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the last error message
	 *
	 * @access	public
	 * @return	string
	 */	
	function last_error()
	{
		$error = '';
		if (!empty($this->_errors))
		{
			$key = count($this->_errors) -1;
			$error = $this->_errors[$key];
		}
		return $error;
		
	}
	// --------------------------------------------------------------------
	
	/**
	 * Returns whether there were errors or not set on the object
	 *
	 * @access	public
	 * @return	boolean
	 */	
	function has_errors()
	{
		return count($this->_errors) > 0;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds an error to the _errors array
	 *
	 * @access	protected
	 * @param	string	Error message
	 * @return	void
	 */	
	protected function _add_error($error, $use_lang = FALSE)
	{
		
		if (is_array($error))
		{
			foreach ($error as $val)
			{
				if ($use_lang AND $this->CI->lang->line($val) != FALSE)
				{
					$val = $this->CI->lang->line($val);
				}
				$this->_errors[] = $val;
				log_message('error', $val);
			}
		}
		else
		{
			if ($use_lang AND $this->CI->lang->line($error) != FALSE)
			{
				$error = $this->CI->lang->line($error);
			}
			$this->_errors[] = $error;
			log_message('error', $error);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Checks if the logged in user is authenticated to use this item based on specified permission
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _has_permission()
	{
		// check if the library requires permissions to run
		if (!empty($this->permission))
		{
			if (!$this->fuel->auth->has_permission($this->permission))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Checks if the logged in user is authenticated to use this item based on specified permission
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _check_permission()
	{
		// check if the library requires permissions to run
		if (!$this->_has_permission())
		{
			show_error(lang('error_no_lib_permissions', ucfirst(get_class($this))));
		}
	}
}

/* End of file Fuel_base_library.php */
/* Location: ./modules/fuel/libraries/Fuel_base_library.php */