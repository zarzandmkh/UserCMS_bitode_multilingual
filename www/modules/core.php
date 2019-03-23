<?php

/**
* user core class
* usercms 2.6.1 bitcode_multilingual_edition
*/
class core extends user_cms_core_edition {

	static public $languages = array();
	private $language_global;

	function __construct(){
		parent::__construct();
		/*$language_directory = is_dir($this->theme['dir'] . '/language')?$this->theme['dir'] . '/language':(is_dir($this->theme['dir_core'] . '/language')?$this->theme['dir_core'] . '/language':'');
		$this->languages = is_dir($language_directory)?array_values(array_diff(scandir($language_directory), array('.','..'))):array();*/
		self::$languages = explode(',', $this->config['site_languages']);
		
		if(count(self::$languages) > 0 && $this->config['multilingual']){
			define('IS_MULTILINGUAL', true);
		}else{
			define('IS_MULTILINGUAL', false);
		}

		if(count(self::$languages) > 0 && !$this->config['multilingual']){
			if(empty($this->config['default_language']))exit('You chose single language mode, your theme has language options but you didn\'t set default language in config.ini. Please set default language or multilingual mode');
		}

		$this->language_global = !empty($_SESSION['LANGUAGE_GLOBAL'])?$_SESSION['LANGUAGE_GLOBAL']:$this->config['default_language'];

		if(!empty($_POST['language_global'])){
			$_SESSION['LANGUAGE_GLOBAL'] = $this->language_global = $_POST['language_global'];
			header('Location: '.$_SERVER['REQUEST_URI']);
		}else{
			$_SESSION['LANGUAGE_GLOBAL'] = $this->language_global;
		}

		if(END_NAME == 'back_end') $this->language_global = $this->config['default_language'];

		if(!IS_MULTILINGUAL){
			$_SESSION['LANGUAGE_GLOBAL'] = $this->language_global = $this->config['default_language'];
		}else{
			//$_SESSION['LANGUAGE_GLOBAL'] = $this->languages[0];
		}

		define('LANGUAGE_GLOBAL', $_SESSION['LANGUAGE_GLOBAL']);
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
		// загружаем css и js файлы из конфига темы
		
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