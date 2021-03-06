<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Babeltext Fieldtype Class
 *
 * @package   Babeltext
 * @author    Michael Van Dorth <michael@pseudoclass.com>
 * @copyright Copyright (c) 2013 Michael Van Dorth
 */

class Babeltext_ft extends EE_Fieldtype {
	
	// Fieldtype Info
	public $info = array(
		'name'		=> 'Babeltext',
		'version'	=> '0.3.3'
	);
	
	// Temp array structure of languages
	protected $languages = array();
	
	// Language Codes
	protected $language_codes = array(
		'aa','ab','af','am','ar','as','ay','az','ba','be','bg','bh','bi','bn','bo','br','ca','co','cs','cy','da','de',
		'dz','el','en','eo','es','et','eu','fa','fi','fj','fo','fr','fy','ga','gd','gl','gn','gu','ha','hi','he','hr',
		'hu','hy','ia','id','ie','ik','is','it','iu','ja','jw','ka','kk','kl','km','kn','ko','ks','ku','ky','la','ln',
		'lo','lt','lv','mg','mi','mk','ml','mn','mo','mr','ms','mt','my','na','ne','nl','no','oc','om','or','pa','pl',
		'ps','pt','qu','rm','rn','ro','ru','rw','sa','sd','sg','sh','si','sk','sl','sm','sn','so','sq','sr','ss','st',
		'su','sv','sw','ta','te','tg','th','ti','tk','tl','tn','to','tr','ts','tt','tw','ug','uk','ur','uz','vi','vo',
		'wo','xh','yi','yo','za','zh','zu'
	);
	
	// Required to use tag pairs
	var $has_array_data = TRUE;
	
	
	// --------------------------------------------------------------------
		
	/**
	 * Fieldtype Constructor
	 *
	 * @access	public
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Default language is english
		$this->languages = array(
			'en' => array('name' => 'English', 'required' => TRUE, 'dir' => 'ltr')
		);
		
	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Post delete custom logic after an entry is deleted
	 *
	 * @access	public
	 * @param	array of the deleted entry_ids
	 * @return	
	 *
	 */
	function delete($ids)
	{

	}


	// --------------------------------------------------------------------
	
	/**
	 * Display Field on Publish Page
	 *
	 * @access	public
	 * @param	existing data
	 * @return	field html
	 *
	 */
	function display_field($data)
	{
		
		// Load the language file
		$this->EE->lang->loadfile('babeltext');

		// Theme folder
		$theme_folder = defined( 'URL_THIRD_THEMES' )
			? $this->EE->config->item('url_third_themes') . '/babeltext/'
			: $this->EE->config->item('theme_folder_url') . 'third_party/babeltext/';
		
		// Decode the data into an array
		$data = (array) json_decode(htmlspecialchars_decode($data), TRUE);
		
		// Lets create another array to merge the settings and data
		$data_arr = array('fields' => array());
		
		// Loop through all the languages in the settings
		foreach($this->settings['languages'] as $key => $value)
		{
			
			// Start creating the array we will use in the view file
			$data_arr['fields'][$key] = array(
				'name' => $value['name'],
				'required' => ($this->settings['field_required'] == 'y' ? $value['required'] : FALSE)
			);
			
			// Check if the dummy field has already been submitted (to use previous submitted data as content in case of a validation error)
			if($this->EE->input->post('field_id_'.$this->field_id))
			{
				$content = $this->EE->input->post('bt_' . $key . '_field_id_' . $this->field_id);
			}
			else
			{
				$content = (array_key_exists($key, $data) ? $data[$key]['content'] : '');
			}
			
			// Generate and add the fields to the data array based on the content_type setting
			$params = array(
				'name'	=> 'bt_' . $key . '_field_id_' . $this->field_id,
				'id'	=> 'bt_' . $key . '_field_id_' . $this->field_id,
				'value'	=> $content,
				'dir'	=> $value['dir']
			);
			if($this->settings['content_type'] == 'text')
			{
				$data_arr['fields'][$key]['field'] = form_input($params);
			}
			else
			{
				$params['rows'] = $this->settings['field_ta_rows'];
				$data_arr['fields'][$key]['field'] = form_textarea($params);
			}
			
		}
		
		// Add in a dummy hidden field to override the default required check, otherwise it will never pass validation if set to required
		$data_arr['placeholder_field'] = form_hidden('field_id_'.$this->field_id, 'babeltext placeholder');
		
		$data_arr['all_languages'] = array();
		foreach($this->language_codes as $lang_id)
		{
			$data_arr['all_languages'][$lang_id] = lang('bt_lang_'.$lang_id);
		}
		
		// Convert the languages array into a json encoded string (for use in javascript)
		$data_arr['all_languages'] = json_encode($data_arr['all_languages']);
		
		// Add in the CSS and JS if it's not already cached
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . $theme_folder . 'styles/babeltext_display.css" />');
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="' . $theme_folder . 'scripts/babeltext_display.js"></script>');
		
		// Set the RTE JS if it has been selected as the fieldtype
		if($this->settings['content_type'] == 'rte')
		{
			
			// Access the RTE library package
			$this->EE->load->add_package_path(PATH_MOD.'rte');
			$this->EE->load->library('rte_lib');
			
			// Load JS lib
			$this->EE->load->library('javascript');

			// Add RTE JS to CP
			$this->EE->javascript->output(
				$this->EE->rte_lib->build_js(0, '.bt_tabs textarea', NULL, TRUE)
			);

			// Add FileManager JS to CP
			$this->EE->load->library(array('filemanager', 'file_field'));
			$this->EE->file_field->browser();

		}
		
		// Send back the redered view
		return $this->EE->load->view('ft_display', $data_arr, TRUE);
		
	}


	// --------------------------------------------------------------------
	
	/**
	 * Display Settings Screen for single field
	 *
	 * @access	public
	 * @return	default settings
	 *
	 */
	function display_settings($data)
	{
		
		// Load the language file
		$this->EE->lang->loadfile('babeltext');

		// Theme folder
		$theme_folder = defined( 'URL_THIRD_THEMES' )
			? $this->EE->config->item('url_third_themes') . '/babeltext/'
			: $this->EE->config->item('theme_folder_url') . 'third_party/babeltext/';
		
		// Content type options (Basic)
		$type_options = array(
			'text' => lang('bt_text_input'),
			'textarea' => lang('bt_textarea')
		);
		
		// Check if the RTE is installed to add it as an option as well
		$this->EE->load->library('api');
		$this->EE->api->instantiate('channel_fields');
		$inst_fields = $this->EE->api_channel_fields->fetch_installed_fieldtypes();
		if(array_key_exists('rte', $inst_fields))
		{
			$type_options['rte'] = lang('bt_rich_text_editor');
		}
		
		// If there is no data, use the global setting defaults
		$data['languages'] = (array_key_exists('languages', $data) ? $data['languages'] : $this->settings['languages']);
		$data['content_type'] = (array_key_exists('content_type', $data) ? $data['content_type'] : $this->settings['content_type']);
		$data['field_ta_rows'] = (array_key_exists('field_ta_rows', $data) ? $data['field_ta_rows'] : $this->settings['field_ta_rows']);
		
		// Add required checkboxes to the output and get a list of the language keys and names in their correct order
		$lang_keys = array();
		$lang_names = array();
		$is_required = $data['field_required'];
		
		// Create form elements
		foreach($data['languages'] as $key => $value)
		{
			
			// Required Checkbox
			$req_params = array(
				'name' => 'bt_required[]',
				'value' => $key,
				'checked' => $value['required']
			);
			if($is_required == 'n')
			{
				$req_params['disabled'] = 'disabled';
			}
			
			$data['languages'][$key]['req_checkbox'] = form_checkbox($req_params);
			
			// Text Direction Radio Group
			$data['languages'][$key]['dir_radios'] = '<label>' . form_radio('bt_' . $key . '_dir', 'ltr', ($value['dir'] == 'ltr' ? TRUE : FALSE)) . ' ' . lang('bt_ltr') . '</label> ';
			$data['languages'][$key]['dir_radios'] .= NBS . ' <label>' . form_radio('bt_' . $key . '_dir', 'rtl', ($value['dir'] == 'rtl' ? TRUE : FALSE)) . ' ' . lang('bt_rtl') . '</label>';
			
			// Append Language Key and Name
			$lang_keys[] = $key;
			$lang_names[] = $value['name'];
			
		}
		
		// Add the hidden language key and name fields
		$data['lang_keys_hidden'] = form_hidden('bt_language_keys', implode(',', $lang_keys));
		$data['lang_names_hidden'] = form_hidden('bt_language_names', implode(',', $lang_names));
		
		// Get all of the available languages into a select but without any current languages
		$data['lang_dropdown'] = form_dropdown(
			'bt_lang_select',
			$this->_create_lang_array($lang_keys),
			'',
			'id="bt_lang_select"'
		);
		
		// Send the data to the view file to parse it into html
		$lang_table = $this->EE->load->view('ft_settings', $data, TRUE);
		
		// Create the texts for the labels
		$type_label = '<label for="bt_content_type">' . lang('bt_field_type_label') . '</label><br/>' . lang('bt_field_type_desc');
		$rows_label = '<label for="bt_ta_rows">' . lang('bt_num_rows_label') . '</label><br/>' . lang('bt_num_rows_desc');
		$lang_label = '<label for="bt_lang_select">' . lang('bt_languages_label') . '</label><br/>' . lang('bt_languages_desc');
		
		// Create the input fields using the CI form helper
		
		// Content Type
		$type_control = form_dropdown(
			'bt_content_type',
			$type_options,
			$data['content_type'],
			'id="bt_content_type"'
		);
		
		// Textarea and RTE rows
		$ta_rows_control = form_input(array(
			'name' => 'bt_ta_rows',
			'id' => 'bt_ta_rows',
			'value' => $data['field_ta_rows'],
			'size' => '5'
		));
		
		// Output the field options in rows
		$this->EE->table->add_row($type_label, $type_control);
		$this->EE->table->add_row($rows_label, $ta_rows_control);
		$this->EE->table->add_row($lang_label, $lang_table);
		
		// Add in the CSS and JS if it's not already cached
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' . $theme_folder . 'styles/babeltext_settings.css" />');
		$this->EE->cp->add_to_foot('<script type="text/javascript">
			var  BT_LANG_LTR = "' . lang('bt_ltr') . '";
			var  BT_LANG_RTL = "' . lang('bt_rtl') . '";
			var  BT_LANG_ERR_ONE_ROW = "' . lang('bt_error_one_row') . '";
			var  BT_LANG_ERR_REQUIRED = "' . lang('bt_error_required') . '";
		</script>');
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="' . $theme_folder . 'scripts/babeltext_settings.js"></script>');

	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Create language array of all languages
	 *
	 * @access	private
	 * @return	array of all available languages
	 *
	 */
	private function _create_lang_array($excluded)
	{
		
		// Load the language file
		$this->EE->lang->loadfile('babeltext');
		
		// Create the array looping through codes and getting names from the language file and excluding items
		$lang_options = array();
		foreach($this->language_codes as $code)
		{
			if(!in_array($code, $excluded))
			{
				$lang_options[$code] = lang('bt_lang_' . $code);
			}
		}
		
		// Order alphabetically by language name and add 'select language' label
		asort($lang_options);
		$lang_options = array('' => lang('bt_select_language')) + $lang_options;

		return $lang_options;
		
	}


	// --------------------------------------------------------------------
	
	/**
	 * Install Fieldtype
	 *
	 * @access	public
	 * @return	array of default global settings
	 *
	 */
	function install()
	{
		
		// Default settings
		$defaults = array(
			'languages' => $this->languages,
			'content_type' => 'text',
			'field_ta_rows' => 10
		);		

		return $defaults;
	}

	// --------------------------------------------------------------------
		
	/**
	 * Preprocess data on frontend
	 *
	 * @access	public
	 * @param	field data
	 * @return	prepped data
	 *
	 */
	function pre_process($data)
	{
		
		// De-serialize the data before it gets to the tag replace function
		return json_decode(htmlspecialchars_decode($data), TRUE);
		
	}

	// --------------------------------------------------------------------
		
	/**
	 * Post save custom logic after an entry is saved
	 *
	 * @access	public
	 * @param	submitted field data and entry_id
	 * @return	
	 *
	 */
	function post_save($data)
	{

	}
	
	// --------------------------------------------------------------------
		
	/**
	 * Post save settings additional processing after the field is created/modified
	 *
	 * @access	public
	 * @param	submitted settings for the field
	 * @return	
	 *
	 */
	function post_save_settings($data)
	{
	
		// If this is not a new setup only
		if($data['field_id'] != '')
		{
		
			// Field column name in channel_data db table
			$field_name = 'field_id_' . $data['field_id'];
			
			// Get all channel data that use this field
			$select_sql = "entry_id, $field_name AS bt_content";
			$this->EE->db->select($select_sql, FALSE);
			$this->EE->db->from('channel_data');
			$this->EE->db->where("$field_name !=", '');
			$query = $this->EE->db->get();
			
			// If we have results lets loop through them
			if($query->num_rows())
			{
				
				// Array to hold the data for the update
				$update_data = array();
				
				// Loop through the results
				foreach($query->result() as $row)
				{
					
					// Get the old data in the entry and prep the new data
					$old_data = $this->pre_process($row->bt_content);
					$new_data = array();
					
					// Loop though the setting languages that were just saved
					foreach($this->settings['languages'] as $key => $value)
					{
						// Get the existing data for each language
						$new_data[$key] = array(
							'name' => $value['name'],
							'content' => (array_key_exists($key, $old_data) ? $old_data[$key]['content'] : '')
						);
						
					}
					
					// Serialize the new data into a json string
					$new_data_string = json_encode($new_data);
					
					// Add the content and the entry id to the update array
					$update_data[] = array(
						'entry_id' => $row->entry_id,
						$field_name => $new_data_string
					); 
					
				}
				
				// Use CI batch update to update the data in the channel data table
				$this->EE->db->update_batch('channel_data', $update_data, 'entry_id');
				
			}
		
		}
		
	}	


	// --------------------------------------------------------------------
		
	/**
	 * Replace tag
	 *
	 * @access	public
	 * @param	field data
	 * @param	field parameters
	 * @param	data between tag pairs
	 * @return	replacement text
	 *
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		
		// If no data, return empty string
		if(empty($data))
		{
			return;
		}
		
		// Reteiving and prepping the data ------------------------------------------------
		
		// Data we will prep to return
		$return_data = array();
		$params = is_array($params) ? $params : array();
		
		// Get the language parameter if it exists
		if((array_key_exists('language', $params)) && ($params['language'] !== 'dynamic') && ($params['language'] !== ''))
		{
			
			// Get the keys to iterate through and use them on the data array to get the content in the order supplied by the tag param
			$lang_keys = explode('|', $params['language']);
			
			foreach($lang_keys as $key)
			{
			
				if(array_key_exists($key, $data))
				{
					$return_data[] = array(
						'bt_id' => $key,
						'bt_name' => $data[$key]['name'],
						'bt_content' => $data[$key]['content']
					);
				}
				
			}
			
			// Make sure we have some data, if not return empty strings
			if(!$return_data) {
				$return_data[] = array(
					'bt_id' => '',
					'bt_name' => '',
					'bt_content' => ''
				);
			}
			
			
		} else {
			
			// There is no language parameter. Try and get the language key from the URL
			
			// Get the current URL string and parse it to check the first segment after the domain (i.e.; http://example.com/es/)
			$current_url = $this->EE->functions->fetch_current_uri();
			$parsed_url = parse_url($current_url);
			if(array_key_exists('path', $parsed_url))
			{
				$path = $parsed_url['path'];
				$path_parts = explode('/', $path);
				$segment = $path_parts[1]; // 1 not 0 because the path string begins with a slash
			}
			else
			{
				$segment = FALSE;
			}
			
			// If the segment matches a language key, add that languages data to the return data
			if($segment && array_key_exists($segment, $data))
			{
			
				$return_data[] = array(
					'bt_id' => $segment,
					'bt_name' => $data[$segment]['name'],
					'bt_content' => $data[$segment]['content']
				);
			
			}
			else
			// The segment doesn't match a langauge key. Get the first language in the settings as the default
			{
				
				$setting_langs = $this->settings['languages'];
				reset($setting_langs);
				$default_key = key($setting_langs);
				
				$return_data[] = array(
					'bt_id' => $default_key,
					'bt_name' => $data[$default_key]['name'],
					'bt_content' => $data[$default_key]['content']
				);
				
			}
			
		}
		
		// Outputting the data to the tag ------------------------------------------------
		
		// Tag Pair Output
		if($tagdata !== FALSE)
		{
			$output = $this->EE->TMPL->parse_variables($tagdata, $return_data);
		}
		
		// Single Tag Output
		else
		{
			$output = $return_data ? $return_data[0]['bt_content'] : ''; // First content element in the return data array (in case they have input multiple in a single tag)
		}
		
		return $output;
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Save Data
	 *
	 * @access	public
	 * @param	submitted field data
	 * @return	string to save
	 *
	 */
	function save($data)
	{
		
		// Flag for all empty fields
		$has_data = FALSE;
		
		// Convert the input into an associative array structure
		$data_arr = array();
		foreach($this->settings['languages'] as $key => $value)
		{
			$content = trim($this->EE->input->post('bt_' . $key . '_field_id_' . $this->field_id));
			if($content !== '')
			{
				$data_arr[$key] = array(
					'name' => $value['name'],
					'content' => $content
				);
				$has_data = TRUE;
			}
		}
		
		// Set the field to null if no data is present, or serialize the data with JSON to save it as a string
		return ($has_data ? json_encode($data_arr) : '');
		
	}
	
	

	// --------------------------------------------------------------------
	
	/**
	 * Save Global Settings
	 *
	 * @access	public
	 * @return	global settings
	 *
	 */
	function save_global_settings()
	{
		return array_merge($this->settings, $_POST);
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Save Settings
	 *
	 * @access	public
	 * @param	submitted settings for single field	
	 * @return	field settings
	 *
	 */
	function save_settings($data)
	{
		
		// $data_arr = array();
		
		// Loop through the keys and names to create the array to save
		
		$language_keys = explode(',', $this->EE->input->post('bt_language_keys'));
		$language_names = explode(',', $this->EE->input->post('bt_language_names'));
		$language_req = $this->EE->input->post('bt_required');
		
		$data['languages'] = array();
		
		if(count($language_keys) == count($language_names))
		{
			for($i = 0; $i < count($language_keys); $i++)
			{
				$key = $language_keys[$i];
				$dir = $this->EE->input->post('bt_' . $key . '_dir');
				$data['languages'][$key] = array(
					'name' => $language_names[$i],
					'required' => ($language_req ? in_array($key, $language_req) : FALSE),
					'dir' => $dir
				);
			}
		}
		
		$data['content_type'] = $this->EE->input->post('bt_content_type');
		$data['field_ta_rows'] = ($data['content_type'] == 'text' ? 10 : $this->EE->input->post('bt_ta_rows'));
		
		// If this field has been marked as required, make sure at least one language has been set to required
		if(($this->EE->input->post('field_required') == 'y') && (count($language_req) < 1))
		{
			return 'There has been an error';
		}
		else
		{
			return $data;
		}
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Uninstall Fieldtype
	 *
	 * @access	public
	 * @param	field settings and action indicator
	 * @return	fields to modify
	 *
	 */
	function settings_modify_column($params)
	{

	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Uninstall Fieldtype - channel_data is dropped automatically
	 *
	 * @access	public
	 * @return	
	 *
	 */
	function uninstall()
	{

	}		
	
	// --------------------------------------------------------------------
	
	/**
	 * Validate field input
	 *
	 * @access	public
	 * @param	submitted field data
	 * @return	TRUE or an error message
	 *
	 */
	function validate($data)
	{
		
		// Load the language file
		$this->EE->lang->loadfile('babeltext');
		
		// Counter vars and error array
		$total_req = 0;
		$total_valid = 0;
		$error_langs = array();
		
		// Empty RTE Values
		$empty_rte = array(
			'',
			'<br>',
			'<br/>',
			'<br />',
			'<p></p>',
			'<p>​</p>' // Zero-width character
		);
		
		// Loop through the settings to test on required fields 
		foreach($this->settings['languages'] as $key => $value)
		{
			
			if($value['required'] === TRUE)
			{
				
				$post_data = trim($this->EE->input->post('bt_' . $key . '_field_id_' . $this->field_id));
				$post_data = strip_tags($post_data, '<img>');
				$post_data = str_replace("\xe2\x80\x8b", '', $post_data);
				$total_req += 1;
				
				if(empty($post_data) || in_array($post_data, $empty_rte))
				{
					$error_langs[] = lang('bt_lang_' . $key);
				}
				else
				{
					$total_valid += 1;
				}
				
			}
			
		}
		
		// Check if this field is required, if so check that all required languages are included
		if(($this->settings['field_required'] == 'y') && ($total_valid != $total_req))
		{
			return (lang('bt_err_req_langs') . implode(', ', $error_langs));
		}
		else
		{
			return TRUE;
		}
		
	}
	
}

/* End of file ft.name.php */
/* Location: ./system/expressionengine/third_party/babeltext/ft.babeltext.php */