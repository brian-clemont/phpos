<?php	
/*
**********************************

	PHPOS Web Operating system
	MIT License
	(c) 2013 Marcin Szczyglinski
	szczyglis83@gmail.com
	GitHUB: https://github.com/phpos/
	File version: 1.2.5, 2013.10.15
 
**********************************
*/
	error_reporting(E_ERROR | E_PARSE);

	if(!isset($_SESSION)) 
	{
		session_start();
	}	

	//header('Content-Type: text/html; charset=utf-8');	
	 
/*
**************************
*/
 	
	define('PHPOS', true);	
	define('PHPOS_DIR','../');
	define('PHPOS_URL','../_phpos/');
	define('PHPOS_WEBROOT','');	
	define('PHPOS_WEBROOT_DIR', '../../web/');
	define('PHPOS_WEBROOT_URL','');
	
	define('PHPOS_TEMP', PHPOS_WEBROOT_DIR.'temp/');
	
	define('PHPOS_HOME_DIR', PHPOS_WEBROOT_DIR.'home/');
	define('PHPOS_HOME_URL', PHPOS_WEBROOT_URL.'home/');
	
	
	include PHPOS_WEBROOT_DIR.'version.php';
	
	define('ICONS',PHPOS_WEBROOT_URL.'_phpos/icons/');	
	define('PHPOS_APPS_DIR',PHPOS_DIR.'apps/');	
	define('PHPOS_APPS_URL',PHPOS_URL.'apps/');		
	define('PHPOS_IN_LOADER', true); 
	 
/*
**************************
*/
 		
	require_once(PHPOS_DIR.'config/core.php');	
	require_once(PHPOS_DIR.'config/database.php');
  require_once(PHPOS_DIR.'classes/class.phpos_filters.php');	
	
	if(file_exists(PHPOS_DIR.'config/security_key.php'))
	{		
		include PHPOS_DIR.'config/security_key.php';
		define('PHPOS_KEY', $phpos_key);
	}
	
	 
/*
**************************
*/
 	

	require_once(PHPOS_DIR.'controllers/databaseController.php');		
	
	
	require_once(PHPOS_DIR.'classes/class.users.php');	
	require_once(PHPOS_DIR.'classes/class.phpos_config.php');
	
	$config = new phpos_config;
	$config->set_id_user();
	
	require_once(PHPOS_DIR.'classes/class.helpers.php');	
	require_once(PHPOS_DIR.'classes/class.phpos_logs.php');		
	$phpos_log = new phpos_logs;
		 
/*
**************************
*/

	define("PHPOS_SYSTEM_LANG", cfg::get('lang'));	
	define("PHPOS_USER_LANG", cfg::uget('lang'));			
	
	
	require_once(PHPOS_DIR.'classes/class.languages.php');
	require_once(PHPOS_DIR.'controllers/languageController.php');		
	require_once(PHPOS_DIR.'common/functions.php');
	require_once(PHPOS_DIR.'classes/class.api_wintask.php');	
	require_once(PHPOS_DIR.'classes/class.api_processes.php');	
	require_once(PHPOS_DIR.'controllers/helpersController.php');	
	
	define('THEME_DIR', PHPOS_WEBROOT_DIR.'_phpos/themes/'.globalconfig('theme').'/');
	define('THEME_URL', PHPOS_WEBROOT_URL.'_phpos/themes/'.globalconfig('theme').'/');
	
	
	
	
	if($_SESSION['DEBUG'])
	{
		//define("PHPOS_IN_DEBUG", true);
	}
		 
/*
**************************
*/
 	
	require_once(PHPOS_DIR.'classes/class.phpos_debugger.php');	
	require_once(PHPOS_DIR.'classes/class.phpos_clipboard.php');
	require_once(PHPOS_DIR.'classes/class.phpos_layout.php');	
	require_once(PHPOS_DIR.'classes/class.phpos_forms.php');	
	require_once(PHPOS_DIR.'classes/class.phpos_app.php');
	require_once(PHPOS_DIR.'classes/class.phpos_filesystems.php');	
	require_once(PHPOS_DIR.'classes/class.phpos_ftp.php');	
	require_once(PHPOS_DIR.'classes/class.phpos_clouds.php');	
	require_once(PHPOS_DIR.'classes/class.phpos_groups.php');
	require_once(PHPOS_DIR.'classes/class.phpos_startmenu.php');
	require_once(PHPOS_DIR.'classes/class.phpos_shared.php');
	require_once(PHPOS_DIR.'classes/class.phpos_shortcuts.php');
	require_once(PHPOS_DIR.'classes/class.phpos_plugins.php');
	require_once(PHPOS_DIR.'classes/class.phpos_wallpapers.php');
	require_once(PHPOS_DIR.'classes/class.phpos_themes.php');
	require_once(PHPOS_DIR.'classes/class.phpos_icons.php');
	require_once(PHPOS_DIR.'classes/class.phpos_navigation.php');
	require_once(PHPOS_DIR.'classes/class.phpos_explorer_api.php');
	require_once(PHPOS_DIR.'classes/class.phpos_messages.php');

	activity();
		 
/*
**************************
*/
 	
	if(!empty($_POST['id']))
	{
		$_GET['id'] = filter::num($_POST['id']);
	}

	 
/*
**************************
*/
 	
	$apiWindow = new api_wintask;	
	$apiWindow->setID(filter::num($_GET['id'])); // set ID of my window	
	$apiWindow->getWindow(); // get Window data
	define("WIN_ID", $apiWindow->getID());
	
	
// Get APP ID
	$app = $apiWindow->getAPPID();	
	$app_name = $apiWindow->get_app_name();
	if(!empty($_GET['action']))
	{
		$apiWindow->setAPPID($app_name.'@'.filter::alfas($_GET['action']));
		$apiWindow->updateWindow();
	}	
	$app_action = $apiWindow->get_app_action();
	
	define("APP_ID", $app_name);
	define("APP_ACTION", $app_action);	
	
	define("WIN_TYPE", $apiWindow->getParam('wintype'));
	
	 
/*
**************************
*/
 	
	if(file_exists(PHPOS_DIR.'plugins/window.'.$apiWindow->getParam('wintype').'Plugin.php')) 
	{
	
		define('PHPOS_PLUGIN', true);	
		
		if(!empty($app_name))
		{
			$my_app = new phpos_app;
			$my_app->set_app_id($app_name);
			$my_app->set_app_action($app_action);
			$my_app->set_window($apiWindow);
			$my_app->load_config();		
			
			// esc - close
			if(WIN_ID != 1)
			{
				$tips = "
				//Select all anchor tag with rel set to tooltip
				$('a[rel=tooltip]').mouseover(function(e) {
				
				//Grab the title attribute's value and assign it to a variable
				var tip = $(this).attr('title');	
				
				//Remove the title attribute's to avoid the native tooltip from the browser
				$(this).attr('title','');
				
				//Append the tooltip template and its value
				$(this).append('<div id=\"tooltip\"><div class=\"tipHeader\"></div><div class=\"tipBody\">' + tip + '</div><div class=\"tipFooter\"></div></div>');		
						
				//Show the tooltip with faceIn effect
				$('#tooltip').fadeIn('500');
				$('#tooltip').fadeTo('10',0.9);
				
			}).mousemove(function(e) {
			
				//Keep changing the X and Y axis for the tooltip, thus, the tooltip move along with the mouse
				$('#tooltip').css('top', e.pageY - 100 );
				$('#tooltip').css('left', e.pageX - 80 );
				
			}).mouseout(function() {
			
				//Put back the title attribute's value
				$(this).attr('title',$('.tipBody').html());
			
				//Remove the appended tooltip template
				$(this).children('div#tooltip').remove();
				
			});
			";
				
				
				
				
				$keyboard = '	
				$(document).keyup(function(e)
				{
					switch(e.which)
					{
						// user presses the "a"
						case 97:	alert("a");
									break;	
									
						// user presses the "s" key
						case 27:	phpos.windowClose(window.PHPOS_ACTIVE_WINDOW);
						
					}
				});
				'.$tips.'
				//$(".bslink").bstip();
				//tooltip();
				
									
				';
				$my_app->jquery_onready($keyboard);			
			}
		}
		
		$my_user = new phpos_users;
		$my_user->set_id_user($my_user->get_logged_user());		
		if($my_user->user_id_exists())	$my_user->get_user_by_id();					
		$my_app->set_user($my_user);		
		$my_user->assign_config($config);
		$my_user->get_logged_user();
		
		define('MY_HOME_DIR', PHPOS_HOME_DIR.$my_user->get_home_dir_hash().'/');
	  define('MY_HOME_URL', PHPOS_HOME_URL.$my_user->get_home_dir_hash().'/');			
			
		define('PHPOS_ACCESS', true);
		include PHPOS_DIR.'plugins/window.'.$apiWindow->getParam('wintype').'Plugin.php';	
		echo $my_app->render_javascript_jquery();
		
		if(!$_POST['phpos_keep_result'])
		{
			$_SESSION['RESULT'] = NULL;			
			$_SESSION['RESULT_STATUS'] = NULL;			
		}		
			
			
		
	} else {
	
		helper::alert('error', 'Plugin not installed: '.$apiWindow->getParam('wintype'));
		helper::window('close');
	}	


	 
/*
**************************
*/
 	

	if(!$_GET['ajax_include'] &&  !$_POST['ajax_include'])
	{	
		if(file_exists(PHPOS_WEBROOT_DIR.'_phpos/resources/'.$app_name.'/window_icon.png'))
		{
			// Custom icon
			echo '<style>
			.phpos_window_icon'.$apiWindow->getID().' { background:url("'.PHPOS_WEBROOT_URL.'_phpos/resources/'.$app_name.'/window_icon.png") no-repeat center center;
			}	</style>';	
		} else {
		
			// Default window icon
			echo '<style>
			.phpos_window_icon'.$apiWindow->getID().' { background:url("'.PHPOS_WEBROOT_URL.'_phpos/themes/default/windows/window_default_icon.png") no-repeat center center;
			}	</style>';	
		}
	}

?>