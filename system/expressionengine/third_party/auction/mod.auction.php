<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Auction {

	public $return_data;

	// Constructor
	public function __construct() 
	{
		$this->EE =& get_instance();
	}

	/**
	 * The module's Control Panel default controller
	 *
	 * @return void
	 */
	public function index()
	{
		
	}

}