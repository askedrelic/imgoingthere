<?php

class Example extends Model {

    function Example()
    {
        parent::Model();
    }
    
    function do_stuff(&$form, $data)
    {
		// do db stuff after the form was submit and validated

		// all validated post values are in $data
		// e.g. $data['username']
		
		/*
		 * NEW!!!
		 * uploaded data is not stored in $data['uploads'] anymore
		 * 
		 * uploaded data is now accessible via the upload field's name
		 * and contains all upload data in an associative array
		 * e.g. $data['file_upload']
		 */
		
		// add custom errors to the form by using
		// $form->add_error('username', 'The Username is not valid');
    }
}

/* End of file Example.php */
/* Location: /application/models/Example.php */