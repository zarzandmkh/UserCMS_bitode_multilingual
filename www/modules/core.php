<?php

/**
* user core class
* usercms 2.6.1 bitcode_multilingual_edition
*/
class core extends user_cms_core_edition {

	static public $languages = array();													// list of languages
	private $language_global;															// current site language
	private $user_preferred_language;													// language which system automaticaly detects as users preferred
	private $language_groups = array(													// language groups. if users preferred language is in group system automatically switch language to key language
		'ru' => array('ru', 'uk', 'uz', 'az', 'hy', 'be', 'ka', 'kk', 'ky', 'tg')

	);
	function __construct(){

		parent::__construct();

		self::$languages = explode(',', $this->config['site_languages']);
		//detecting prferred language
		$preferred_language = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])?substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2):'';;
		//if user has alraedy chosen language system remembers it 
		if(!empty($_COOKIE['usercms_language_chosen']))$_SESSION['usercms_language_chosen'] = $_COOKIE['usercms_language_chosen'];
		// searching preferred language by group
		foreach ($this->language_groups as $key => $language_group) {
			if(in_array($preferred_language, $this->language_groups[$key])){
				$this->user_preferred_language = $key;
				break;
			}else{
				$this->user_preferred_language = '';
			}
		}
		// if site is multilingual (defined in config.ini)
		if(count(self::$languages) > 0 && $this->config['multilingual']){
			define('IS_MULTILINGUAL', true);
		}else{
			define('IS_MULTILINGUAL', false);
		}
		// error if there is language in theme but default language is not chosen
		if(count(self::$languages) > 0 && !$this->config['multilingual']){
			if(empty($this->config['default_language']))exit('You chose single language mode, your theme has language options but you didn\'t set default language in config.ini. Please set default language or multilingual mode');
		}

		// setting current language
		$this->language_global = !empty($_SESSION['LANGUAGE_GLOBAL'])?$_SESSION['LANGUAGE_GLOBAL']:$this->config['default_language'];		
		//switching language manually via form
		if(!empty($_POST['language_global'])){
			$_SESSION['LANGUAGE_GLOBAL'] = $this->language_global = $_POST['language_global'];
			if(!empty($_GET['usercms_language']))$_GET['usercms_language'] = $_POST['language_global'];
			if(!empty($_GET['usercms_language'])){
				$location_url = SITE_URL . '?usercms_language=' . $_POST['language_global'];
			}else{
				$location_url = $_SERVER['REQUEST_URI'];
			}
			if(empty($_COOKIE['usercms_language_chosen']))$_SESSION['usercms_language_chosen'] = $_SESSION['LANGUAGE_GLOBAL'];
			setcookie('usercms_language_chosen', $_SESSION['LANGUAGE_GLOBAL'], time()+2592000);

			header('Location: '.$location_url);
		}else{ // setting language by GET
			if(!empty($_GET['usercms_language']) AND in_array($_GET['usercms_language'], self::$languages)){
				$_SESSION['LANGUAGE_GLOBAL'] = $this->language_global = $_GET['usercms_language'];
			}else{
				$_SESSION['LANGUAGE_GLOBAL'] = $this->language_global;
			}	
		}
		//if user has not chose language we set current language users preferred language
		if(!empty($this->user_preferred_language) && empty($_SESSION['usercms_language_chosen'])) $_SESSION['LANGUAGE_GLOBAL'] = $this->language_global = $this->user_preferred_language;

		//if user has already chosen any language we set that
		if(!empty($_SESSION['usercms_language_chosen']))$_SESSION['LANGUAGE_GLOBAL'] = $this->language_global = $_SESSION['usercms_language_chosen'];

		//if site is not multilingual we set default language as current
		if(!IS_MULTILINGUAL){
			$_SESSION['LANGUAGE_GLOBAL'] = $this->language_global = $this->config['default_language'];
		}else{
			//$_SESSION['LANGUAGE_GLOBAL'] = $this->languages[0];
		}

		//if we are in admin panel we choose default language 
		if(END_NAME == 'back_end' || !IS_MULTILINGUAL) $this->language_global = $this->config['default_language'];

		// setting constant with current language
		define('LANGUAGE_GLOBAL', $this->language_global);
	}

		function load_theme() {
		if(END_NAME == 'back_end') {
			$this->theme['name'] = 'default_admin';
		}
		$theme['url']            = SITE_URL .          '/themes/' . $this->theme['name'] ;	
		$theme['url_core']       = SITE_URL . '/user_cms/themes/' . $this->theme['name'] ;	
		$theme['full_name']      = ROOT_DIR .          '/themes/' . $this->theme['name'] . '/' . $this->theme['file'] . '.tpl';	
		$theme['full_name_core'] = ROOT_DIR . '/user_cms/themes/' . $this->theme['name'] . '/' . $this->theme['file'] . '.tpl';	
		$theme['config']         = ROOT_DIR .          '/themes/' . $this->theme['name'] . '/config.ini';	
		$theme['config_core']    = ROOT_DIR . '/user_cms/themes/' . $this->theme['name'] . '/config.ini';
		$theme['dir'] 			 = ROOT_DIR .          '/themes/' . $this->theme['name'];
		$theme['dir_core'] 		 = ROOT_DIR . '/user_cms/themes/' . $this->theme['name'];


		if (file_exists($theme['full_name'])) {
			ob_start();
	        
	        if(!empty(LANGUAGE_GLOBAL) && is_file($theme['dir']  . '/language/' . LANGUAGE_GLOBAL . '/language.php')){
				include $theme['dir']  . '/language/' . LANGUAGE_GLOBAL . '/language.php';
				extract($data_language);
			}

			include $theme['full_name'];
	        $this->html = ob_get_clean();

		} else {
			ob_start();
			if(!empty(LANGUAGE_GLOBAL) && is_file($theme['dir_core']  . '/language/' . LANGUAGE_GLOBAL . '/language.php')){
				include $theme['dir_core']  . '/language/' . LANGUAGE_GLOBAL . '/language.php';
				extract($data_language);
			}

	        include $theme['full_name_core'];	        
	        $this->html = ob_get_clean();
		}
		
		if (file_exists($theme['config'])) {
			$config_file = $theme['config'];
			$theme_url = $theme['url'];
		} elseif (file_exists($theme['config_core'])) {
			$config_file = $theme['config_core'];
			$theme_url = $theme['url_core'];
		}

		if (isset($config_file)) {
			$conf = parse_ini_file($config_file);
			foreach ($conf as $key => $value) {
				if ($key == 'js') {
					$js = '';
					$js_files = explode(',', $value);
					foreach ($js_files as $filename) {
						if(!empty($filename)) {
							$js .= "\t\t" . '<script src="' . $theme_url . '/' . trim($filename) . '" type="text/javascript"></script>' . "\n";
						}
					}
					
					$this->head .= $js;
				}
				
				if ($key == 'css') {
					$css = '';
					$css_files = explode(',', $value);

					foreach ($css_files as $filename) {
						if (!empty($filename)) {
							$css .= "\t\t" . '<link href="' . $theme_url . '/' . trim($filename) . '" rel="stylesheet"  type="text/css" >' . "\n";
						}
					}
					
					$this->head .= $css; 
				}
			}
		}
	}


	
}
