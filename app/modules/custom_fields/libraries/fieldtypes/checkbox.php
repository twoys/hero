<?php

/*
* Checkbox Fieldtype
*
* @extends Fieldtype
* @class Checkbox_fieldtype
*/

class Checkbox_fieldtype extends Fieldtype {
	function __construct () {
		parent::__construct();
	 
		$this->compatibility = array('publish','users','products','collections','forms');
		$this->enabled = TRUE;
		$this->fieldtype_name = 'Checkbox';
		$this->fieldtype_description = 'A single on/off checkbox.';
		$this->validation_error = '';
		$this->db_column = 'TINYINT(1)';
	}
	
	function output_shared () {
		$this->field_class('checkbox');
	
		$attributes = array(
						'type' => 'checkbox',
						'name' => $this->name,
						'value' => '1',
						'class' => implode(' ', $this->field_classes)
						);
						
		if (!empty($this->value)) {
			$attributes['checked'] = 'checked';
		}
		
		// compile attributes
		$attributes = $this->compile_attributes($attributes);
		
		return $attributes;
	}
	
	function output_admin () {
		if (empty($_POST) and empty($this->value)) {
			$this->value($this->default);
		}
	
		$attributes = $this->output_shared();
		
		$help = ($this->help == FALSE) ? '' : '<div class="help">' . $this->help . '</div>';
		
		// build HTML
		$return = '<li>
						<label for="' . $this->name . '">' . $this->label . '</label>
						<input ' . $attributes . ' />
						' . $help . '
					</li>';
					
		return $return;
	}
	
	function output_frontend () {
		if (empty($this->value)) {
			if (empty($_POST)) {
				$this->value($this->default);
			}
			elseif ($this->CI->input->post($this->name) != FALSE) {
				$this->value($this->CI->input->post($this->name));
			}
		}
		
		$attributes = $this->output_shared();
		
		// build HTML
		$return = '<input ' . $attributes . ' />';
					
		return $return;
	}
	
	function validation_rules () {
		$rules = array();
		
		// check required
		if ($this->required == TRUE) {
			$rules[] = 'required';
		}
		
		return $rules;
	}
	
	function validate_post () {
		// nothing extra to validate here other than the rulers in $this->validators
		return TRUE;
	}
	
	function post_to_value () {
		return ($this->CI->input->post($this->name) != FALSE) ? '1' : '0';
	}
	
	function field_form ($edit_id = FALSE) {
		// build fieldset with admin_form which is used when editing a field of this type
		$this->CI->load->library('custom_fields/form_builder');
		$this->CI->form_builder->reset();
		
		$default = $this->CI->form_builder->add_field('select');
		$default->label('Default State')
	          ->name('default')
	          ->options(array(
	          				array('value' => 'checked', 'name' => 'Checked'),
							array('value' => 'unchecked', 'name' => 'Unchecked')
	          			));
	          
	    $help = $this->CI->form_builder->add_field('textarea');
	    $help->label('Help Text')
	    	 ->name('help')
	    	 ->width('500px')
	    	 ->height('80px')
	    	 ->help('This help text will be displayed beneath the field.  Use it to guide the user in responding correctly.');
	    	 
	    $required = $this->CI->form_builder->add_field('checkbox');
	    $required->label('Required Field')
	    	  ->name('required')
	    	  ->help('If checked, this box must be checked for the form to be processed.');
	    	  
	    if (!empty($edit_id)) {
	    	$this->CI->load->model('custom_fields_model');
	    	$field = $this->CI->custom_fields_model->get_custom_field($edit_id);
	    	
	    	$default->value(!empty($field['default']) ? 'checked' : 'unchecked');
	    	$help->value($field['help']);
	    	$required->value($field['required']);
	    }	  
	          
		return $this->CI->form_builder->output_admin();      
	}
	
	function field_form_process () {
		// build array for database
		
		// $options will be automatically serialized by the custom_fields_model::new_custom_field() method
		
		return array(
					'name' => $this->CI->input->post('name'),
					'type' => $this->CI->input->post('type'),
					'default' => ($this->CI->input->post('default') == 'checked') ? 'checked' : '',
					'help' => $this->CI->input->post('help'),
					'required' => ($this->CI->input->post('required')) ? TRUE : FALSE
				);
	}
}