<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * undocumented class
 *
 * @package default
 * @author author
 **/
class BusCache extends DataMapper {

	var $table = "bus_cache";
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