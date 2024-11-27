<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if( ! class_exists('Uvii\SameSiteNone')){
	require_once 'vendor/uvii/samesitenone/src/SameSiteNone.php';
}

use EllisLab\Addons\Consent\Service\Notifications;
use EllisLab\Addons\Consent\Service\Variables\Alert as AlertVars;
use EllisLab\Addons\Consent\Service\Variables\Consent as ConsentVars;
use Uvii\SameSiteNone;

class Ee_cookie_prefs_ext
{
	
	public $settings = array();
	public $name = 'EE Cookie Prefs';
	public $version = '0.1';
	public $description = 'Overrides Individual Cookie Settings';
	public $settings_exist = 'y';
	public $docs_url = '';
	
	private $consent_cookie_name = 'visitor_consents';
	private $module_name;
	
	public function __construct($settings = array())
	{
		$this->settings = $settings;
		
		$this->module_name = strtolower(str_replace(array('_ext', '_mcp', '_upd'), '', __CLASS__));
	}

	public function activate_extension()
	{
		ee()->db->insert(
			'extensions',
			array(
				'class' => __CLASS__,
				'method' => 'set_cookie_prefs',
				'hook' => 'set_cookie_end',
				'settings' => serialize($this->settings),
				'priority' => 10,
				'version' => $this->version,
				'enabled' => 'y',
			)
		);
		
		ee()->db->insert(
			'extensions',
			array(
				'class' => __CLASS__,
				'method' => 'set_consent_cookies',
				'hook' => 'core_template_route', // instead of core_boot
				'settings' => serialize($this->settings),
				'priority' => 10,
				'version' => $this->version,
				'enabled' => 'y',
			)
		);
	}
	
	public function update_extension($current = '')
	{
		if ( ! $current || $current === $this->version)
		{
			return FALSE;
		}
		
		ee()->db->where('class', __CLASS__);
		ee()->db->update(
			'extensions',
			array('version' => $this->version)
		);
		
	}

	public function disable_extension()
	{
		ee()->db->delete('extensions', array('class' => __CLASS__));
	}


	/**
	 * Settings Form
	 *
	 * @param   Array   Settings
	 * @return  void
	 */
	function settings_form($current)
	{
		if ($current == '')
		{
			$current = array();
		}

		ee()->load->helper('form');
		ee()->load->library('table');

		ee()->cp->load_package_js('ee_cookie_prefs');
		
		$base_url = ee('CP/URL', 'addons/settings/'.$this->module_name.'/save');
		
		if (REQ == 'CP' && ee()->config->item('multiple_sites_enabled') == 'y')
		{
			$cookie_prefix = ( ! ee()->config->cp_cookie_prefix) ? 'exp_' : ee()->config->cp_cookie_prefix;
		}
		else
		{
			$cookie_prefix = ( ! ee()->config->item('cookie_prefix')) ? 'exp_' : ee()->config->item('cookie_prefix').'_';
		}

		$defaults = array(
			'name' => '',
			'expires' => '',
			'path' => '',
			'domain' => '',
			'secure_cookie' => '',
			'httponly' => '',
			'samesite' => ''
		);

		$settings = array(
			'base_url' => $base_url,
			'cp_page_title' => lang('ee_cookie_prefs_name'),
			'save_btn_text' => 'btn_save_settings',
			'save_btn_text_working' => 'btn_saving',
			'alerts_name' => 'cookie-settings-save',
			'cookie_prefix' => $cookie_prefix,
			'samesite_none_fix' => FALSE,
			'enable_consent_cookies' => FALSE,
			'cookies' => array()
		);

		if (isset($current['samesite_none_fix']))
		{
			$settings['samesite_none_fix'] = $current['samesite_none_fix'];
		}
		if (isset($current['enable_consent_cookies']))
		{
			$settings['enable_consent_cookies'] = $current['enable_consent_cookies'];
		}
		if (isset($current['enable_consent_cookies_name']))
		{
			$settings['enable_consent_cookies_name'] = $current['enable_consent_cookies_name'];
		}
		if (isset($current['enable_consent_cookies_format']))
		{
			$settings['enable_consent_cookies_format'] = $current['enable_consent_cookies_format'];
		}
		if (isset($current['cookies']))
		{
			$settings['cookies'] = $current['cookies'];
		}
		
		// add last default fields to add rows
		$settings['cookies'][] = $defaults;

		return ee()->load->view('index', $settings, TRUE);
	}

	/**
	 * Save Settings
	 *
	 * @return  void
	 */
	function save_settings()
	{
		if (empty($_POST))
		{
			show_error(lang('unauthorized_access'));
		}
		
		$vars = array();
		
		if (ee()->input->post('cookies'))
		{
			$vars['cookies'] = ee()->input->post('cookies', TRUE);
			
			foreach($vars['cookies'] as $key => $cookie)
			{
				if ( ! isset($cookie['name']) || empty(trim($cookie['name'])))
				{
					unset($vars['cookies'][$key]);
					continue;
				}
				
				// expire must be numeric
				if ( ! is_numeric($cookie['expires']))
				{
					$vars['cookies'][$key]['expires'] = '';
				}
			}
		}
		
		if (ee()->input->post('samesite_none_fix'))
		{
			$vars['samesite_none_fix'] = ee()->input->post('samesite_none_fix', TRUE);
		}
		if (ee()->input->post('enable_consent_cookies'))
		{
			$vars['enable_consent_cookies'] = ee()->input->post('enable_consent_cookies', TRUE);
		}
		if (ee()->input->post('enable_consent_cookies_name'))
		{
			$vars['enable_consent_cookies_name'] = trim(ee()->input->post('enable_consent_cookies_name', TRUE));
		}
		if (ee()->input->post('enable_consent_cookies_format'))
		{
			$vars['enable_consent_cookies_format'] = trim(ee()->input->post('enable_consent_cookies_format', TRUE));
		}

		ee()->db->where('class', __CLASS__);
		ee()->db->update('extensions', array('settings' => serialize($vars)));

		if (empty($vars))
		{
			ee('CP/Alert')->makeStandard('cookie-settings-form')
			  ->asWarning()
			  ->withTitle(lang('ee_cookie_prefs_save_empty'))
			  ->addToBody(lang('ee_cookie_prefs_save_empty_desc'))
			  ->defer();
		}
		else
		{
			ee('CP/Alert')->makeStandard('cookie-settings-form')
			  ->asSuccess()
			  ->withTitle(lang('ee_cookie_prefs_save_success'))
			  ->addToBody(lang('ee_cookie_prefs_save_success_desc'))
			  ->defer();
		}
		
		//$base_url = ee('CP/URL', 'addons/settings/'.$this->module_name);
		//ee()->functions->redirect($base_url);
	}

	/* core_boot */
	function core_boot() 
	{
		/*
		// get cookie name
		$consent_class = ee('Consent');
		$reflector = new ReflectionClass($consent_class);
		$consent_constants = $reflector->getConstants();
		$this->consent_cookie_name = $consent_constants['COOKIE_NAME'];
		*/
		// get cookie prefix
		if (REQ == 'CP' && ee()->config->item('multiple_sites_enabled') == 'y')
		{
			$cookie_prefix = ( ! ee()->config->cp_cookie_prefix) ? 'exp_' : ee()->config->cp_cookie_prefix;
		}
		else
		{
			$cookie_prefix = ( ! ee()->config->item('cookie_prefix')) ? 'exp_' : ee()->config->item('cookie_prefix').'_';
		}
		
		$cookie_exists = isset($_COOKIE[$cookie_prefix.$this->consent_cookie_name]);
		
		// Check if consent form has actually been submitted
		$message = ee()->session->flashdata(md5('Consent/alerts')); // via html form from alert
		
		// Check if action is done (via ajax)
		$action_ids[] = $this->fetch_action_id('Consent', 'submitConsent'); 
		$action_ids[] = $this->fetch_action_id('Consent', 'grantConsent');
		$action_ids[] = $this->fetch_action_id('Consent', 'withdrawConsent');
		
		if (in_array(ee()->input->post('ACT'), $action_ids) || $message || ! $cookie_exists)
		{
			$this->set_consent_cookies();
		}
	}
	
	/* core_template_route */
	// use core_template_route intead of core_boot as this runs later - allowing for member_login action
	// -- Doesn't work - there is no way to capture the member_login ACT
	function core_template_route($uri_string) 
	{
		if (ee()->extensions->last_call !== false)
		{
			$uri_string = ee()->extensions->last_call;
		}
		
		// Check if action is done
		$action_ids[] = $this->fetch_action_id('Member', 'member_login'); // after logging in
		
		if (in_array(ee()->input->post('ACT'), $action_ids))
		{
			$this->set_consent_cookies();
		}
		
		return $uri_string;
	}

	/* Maintain Cookie Consent cookies as client side/javascript cookies */
	function set_consent_cookies() 
	{
		if ( ! isset($this->settings['enable_consent_cookies']) || $this->settings['enable_consent_cookies'] !== 'y')
		{
			return FALSE;
		}
/*
		// get cookie prefix
		if (REQ == 'CP' && ee()->config->item('multiple_sites_enabled') == 'y')
		{
			$cookie_prefix = ( ! ee()->config->cp_cookie_prefix) ? 'exp_' : ee()->config->cp_cookie_prefix;
		}
		else
		{
			$cookie_prefix = ( ! ee()->config->item('cookie_prefix')) ? 'exp_' : ee()->config->item('cookie_prefix').'_';
		}

		// get cookie name
		$consent_class = ee('Consent');
		$reflector = new ReflectionClass($consent_class);
		$consent_constants = $reflector->getConstants();
		$this->consent_cookie_name = $consent_constants['COOKIE_NAME'];
		
		$cookie_exists = isset($_COOKIE[$cookie_prefix.$cookie_name]);
		
		// Check if consent form has actually been submitted
		$message = ee()->session->flashdata(md5('Consent/alerts')); // via html form from alert
		
		$action_ids = array();
		$action_ids[] = $this->fetch_action_id('Consent', 'submitConsent'); // for via ajax
		$action_ids[] = $this->fetch_action_id('Consent', 'grantConsent');
		$action_ids[] = $this->fetch_action_id('Consent', 'withdrawConsent');
		$action_ids[] = $this->fetch_action_id('Member', 'member_login'); // after logging in

		if ( ! in_array(ee()->input->post('ACT'), $action_ids) && empty($message) && $cookie_exists)
		{
			return FALSE;
		}
*/
		
		

		$consent_requests = ee('Model')->get('ConsentRequest')->fields('consent_name');
		$consent_names = $consent_requests->all()->pluck('consent_name');

		// get requests
		$requests = ee('Consent')->getConsentDataFor($consent_names);

		$consented_to = array();

		foreach ($requests as $request)
		{
			$request_vars = new ConsentVars($request);
			$consent = $request_vars->getTemplateVariables();

			$consented_to[$consent['consent_id']] = array(
				'has_granted' => $consent['consent_granted'],
				'timestamp' => $consent['consent_response_date']
			);
		}

		if (empty($consented_to))
		{
			return FALSE;
		}

		$cookie_name = $this->consent_cookie_name;
		$payload = ee('Encrypt/Cookie')->signCookieData($consented_to);
		

		// save to different cookie
		if (isset($this->settings['enable_consent_cookies_name']) && ! empty($this->settings['enable_consent_cookies_name']))
		{
			$cookie_name = $this->settings['enable_consent_cookies_name'];
			$cookie_format = isset($this->settings['enable_consent_cookies_format']) ? $this->settings['enable_consent_cookies_format'] : false;

			$simple_array = array();
			foreach ($consented_to as $key => $val)
			{
				$simple_array[$key] = $val['has_granted'];
			}
			if ($cookie_format == 'json')
			{
				$payload = json_encode($simple_array);
			}
			elseif ($cookie_format == 'serialize')
			{
				$payload = serialize($simple_array);
			}
			elseif  ($cookie_format == 'comma_separated_values')
			{
				$payload = implode(',', $simple_array);
			}
			else 
			{
				$payload = json_encode($consented_to);
			}
		}

		$data = array(
			'name' => $cookie_name,
			'value' => $payload,
			'expire' => ee()->localize->now + 31556952, // 60 * 60 * 24 * 365 = 31556952; A year of seconds
			'httponly' => false, // enable client side cookies
			'samesite' => 'Lax'
		);
		
		// Set prefix, path and domain. We'll pull em out of config.
		if (REQ == 'CP' && ee()->config->item('multiple_sites_enabled') == 'y')
		{
			$data['path']	= ( ! ee()->config->cp_cookie_path) ? '/' : ee()->config->cp_cookie_path;
			$data['domain'] = ( ! ee()->config->cp_cookie_domain) ? '' : ee()->config->cp_cookie_domain;
			$data['prefix'] = ( ! ee()->config->cp_cookie_prefix) ? 'exp_' : ee()->config->cp_cookie_prefix;
		}
		else
		{
			$data['path']	= ( ! ee()->config->item('cookie_path'))	? '/'	: ee()->config->item('cookie_path');
			$data['domain'] = ( ! ee()->config->item('cookie_domain')) ? '' : ee()->config->item('cookie_domain');
			$data['prefix'] = ( ! ee()->config->item('cookie_prefix')) ? 'exp_' : ee()->config->item('cookie_prefix').'_';
		}
		
		// Deal with secure cookies.
		$data['secure_cookie'] = bool_config_item('cookie_secure');
		
		// Cookies with SameSite=None must also specify Secure, meaning they require a secure context.
		if ($data['samesite'] === 'None') {
			$data['secure_cookie'] = true;
		}

		$this->set_cookie($data);
	}

	
	/**
	 * Take control of setting cookies after cookie parameters have been normalized according to the cookie configuration settings.
	 *
	 * @param array: Array of prepped cookie parameters, which include the following keys: prefix, name, value, expire, path, domain, secure_cookie
	 * @return bool
	 */
	public function set_cookie_prefs($data)
	{
		$return_data = FALSE;
		
		$cookie_name = $data['prefix'].$data['name'];
		
		// Just in case this becomes available in the future
		if ( ! isset($data['samesite']))
		{
			$data['samesite'] = '';
		}
		
		if (isset($this->settings['cookies']) && is_array($this->settings['cookies']))
		{
			foreach($this->settings['cookies'] as $cookie_settings)
			{
				if (isset($cookie_settings['name']) && $cookie_settings['name'] === $cookie_name)
				{
					if ( ! empty($cookie_settings['expires']))
					{
						// Handle expiration dates.
						if ( ! is_numeric($cookie_settings['expires']))
						{
							$cookie_settings['expires'] = ee()->localize->now - 86500;
						}
						else if (is_numeric($cookie_settings['expires']) && $cookie_settings['expires'] > 0)
						{
							$cookie_settings['expires'] = ee()->localize->now + $cookie_settings['expires'];
						}
						else
						{
							$cookie_settings['expires'] = 0;
						}
						$data['expire'] = $cookie_settings['expires'];
					}
					if ( ! empty($cookie_settings['path']))
					{
						$data['path'] = $cookie_settings['path'];
					}
					if ( ! empty($cookie_settings['domain']))
					{
						$data['domain'] = $cookie_settings['domain'];
					}
					if ( ! empty($cookie_settings['secure_cookie']))
					{
						$data['secure_cookie'] = $cookie_settings['secure_cookie'] === 'y' ? 1 : 0;
					}
					if ( ! empty($cookie_settings['httponly']))
					{
						$data['httponly'] = $cookie_settings['httponly'] === 'y' ? 1 : 0;
					}
					if ( ! empty($cookie_settings['samesite']))
					{
						$data['samesite'] = $cookie_settings['samesite'];
					}
					
					$return_data = $this->set_cookie($data);

					ee()->extensions->end_script = TRUE;
				}
			}
		}

		return $return_data;
	}
	
	
	private function set_cookie($data)
	{
		$isSafe = TRUE;
		
		// Not all browsers are compatible with SameSite=None
		// https://www.chromium.org/updates/same-site/incompatible-clients
		if (isset($this->settings['samesite_none_fix']) && $this->settings['samesite_none_fix'] === 'y')
		{
			$userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
			$isSafe = SameSiteNone::isSafe($userAgent);
		}
		
		if (PHP_VERSION_ID < 70300) {
			// Older versions of PHP do not support an array as the 3rd parameter,
			// thus the SameSite setting must be hacked in with the path option.
			return setcookie($data['prefix'].$data['name'], $data['value'],
				$data['expire'],
				$data['path'] . ($isSafe === TRUE ? '; SameSite=' . $data['samesite'] : ''),
				$data['domain'],
				$data['secure_cookie'],
				$data['httponly']
			);
		} else {
			$cookieParams = [
				'expires' => $data['expire'],
				'path' => $data['path'],
				'domain' => $data['domain'],
				'secure' => $data['secure_cookie'],
				'httponly' => $data['httponly']
			];
			if ($isSafe === TRUE) {
				$cookieParams['samesite'] = $data['samesite'];
			}
			return setcookie($data['prefix'].$data['name'], $data['value'], $cookieParams);
		}
		return FALSE;
	}
	
	/**
	 * Fetch action id
	 *
	 * @param $class
	 * @param $method
	 * @return bool
	 */
	private function fetch_action_id($class, $method)
	{
		ee()->db->select('action_id');
		ee()->db->where('class', $class);
		ee()->db->where('method', $method);
		$query = ee()->db->get('actions');

		if ($query->num_rows() == 0)
		{
			return false;
		}

		return $query->row('action_id');
	}
	
}