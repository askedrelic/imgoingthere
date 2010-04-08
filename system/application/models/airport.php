<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * undocumented class
 *
 * @package default
 * @author author
 **/
class Airport extends DataMapper {

	var $has_many = array('city');
	/**
	 * classname constructor
	 *
	 * @return void
	 * @author author
	 **/
	function __contsruct()
	{
		parent::DataMapper();
		log_message('debug', 'classname Model Initialized');
		
	}

}

/* End of file classname_model.php */
/* Location: ./system/application/models/classname_model.php */