<?
	namespace controller;
			
	use controller\persist\Bootstrap;
	require_once $_ENV['PATH'].'prototypes/object.php';
	require_once $_ENV['PATH'].'libs/docXML/docXML.php';
	require_once $_ENV['PATH'].'libs/database/ConfigDB.php';
	require_once $_ENV['PATH'].'libs/database/Database.php';
	require_once $_ENV['PATH'].'controller/Database.php';
	require_once $_ENV['PATH'].'controller/persist/Bootstrap.php';
	require_once $_ENV['PATH'].'controller/persist/System.php';
	require_once $_ENV['PATH'].'controller/persist/SystemCall.php';
	require_once $_ENV['PATH'].'controller/persist/Params.php';
	require_once $_ENV['PATH'].'controller/persist/Sendmail.php';
	require_once $_ENV['PATH'].'controller/persist/Config.php';
	require_once $_ENV['PATH'].'controller/persist/Logged.php';
	require_once $_ENV['PATH'].'controller/persist/Image.php';
	require_once $_ENV['PATH'].'controller/persist/Document.php';
	require_once $_ENV['PATH'].'libs/email/Config.php';
	require_once $_ENV['PATH'].'libs/zeusLib/zeusLib.php';

	final class Controller extends \prototypes\Object
	{
		private $in_command;
		private $o_bootstrap;
		private $o_params;
		private $o_db;
		private $o_system_config;
		private $o_system_log;
		private $v_debug;

		function __construct($st_system = null)
		{
			$this->v_debug = array();
			$tm_start = microtime(true);
			$this->load_configs();
			try 
			{
				if($this->o_system_config->get_status())
					$this->load_databases();
				switch($st_system)
				{
					case 'setup':
						require_once $_ENV['PATH'].'setup/Setup.php';
						$this->in_command = $_REQUEST['command'];
						unset($_REQUEST['command']);
						$this->o_params = new \controller\persist\PParams();
						foreach($_REQUEST as $name => $value)
						{
							$object = json_decode($value);
							if(is_a($object, stdClass))
								$this->to_params($object,$this->o_params);
							else
								$this->o_params->set_param($name, $value);
						}
						$o_call = new \controller\persist\PSystemCall($this);
						$o_call->set_params($this->o_params);
						$o_setup = new \setup\Setup($this);
						$o_setup->call_command($o_call);
						break;
					case 'script':
						$this->o_params = new \controller\persist\PParams();
						$n = count($_SERVER['argv']);
						for($c = 1;$c < $n;$c++)
						{
							$arg = $_SERVER['argv'][$c];
							if(strpos($arg, '='))
							{
								$arg = explode('=', $arg);
								$this->o_params->set_param(trim($arg[0]), trim($arg[1]));
							}
							else
								$this->o_params->set_param(trim($arg),null);
						}
						$st_script = $this->o_params->get_param('script');
						$st_task = $this->o_params->get_param('task');
						require_once $_ENV['PATH']."scripts/$st_script.php";
						$st_script = ucfirst($st_script);
						$o_exec = new $st_script($this);
						$o_exec->$st_task($this->o_params);
						break;
					case 'facebook':
						require_once $_ENV['PATH'].'website/Website.php';
						require_once $_ENV['PATH'].'libs/facebook/FacebookSession.php';
						require_once $_ENV['PATH'].'libs/facebook/FacebookRedirectLoginHelper.php';
						require_once $_ENV['PATH'].'libs/facebook/FacebookSDKException.php';
						require_once $_ENV['PATH'].'libs/facebook/GraphPermissions.php';
						$o_app = $this->gets_config('/website/login/facebook/', true);
						$in_id = $o_app->get_node_by_name('id')->get_value();
						$st_secret = $o_app->get_node_by_name('secret')->get_value();
						\libs\facebook\FacebookSession::setDefaultApplication($in_id, $st_secret);
						if($_GET['code'] && $_GET['state'])
						{
							$st_redirect_url = $_ENV['DATA'].'facebook/';
							$o_facebook = new \libs\facebook\FacebookRedirectLoginHelper($st_redirect_url);
							$o_session = $o_facebook->getSessionFromRedirect();
							if($o_session)
								$_SESSION['FB_TOKEN'] = $o_session->getLongLivedSession()->getToken();
							else
							{
								$o_error = $this->insert_log();
								$o_error->set_value('Não Achou a Session');
							}
						}
						if($_SESSION['FB_TOKEN'])
							try
							{
								
								$o_session = new \libs\facebook\FacebookSession($_SESSION['FB_TOKEN']);
								$o_session->validate();
							}
							catch(\Exception $e)
							{
								$o_session = null;
								$this->clear_logged_website();
								$this->in_command = -6;
							}
						if($o_session)
						{
							$request = new \libs\facebook\FacebookRequest($o_session, 'GET', '/me/permissions');
							$response = $request->execute();
							$graphObject = $response->getGraphObject();
							$o_permissions = new \libs\facebook\GraphPermissions($graphObject->asArray());
							if($o_permissions->get_permission('public_profile') && $o_permissions->get_permission('email') && $o_permissions->get_permission('user_friends'))
							{
								$request = new \libs\facebook\FacebookRequest($o_session, 'GET', '/me');
								$response = $request->execute();
								$graphObject = $response->getGraphObject();
								$st_hash = \libs\zeusLib\zeusLib::generate_hash(40);
								$st_facebook = $graphObject->getProperty('id');
								$st_email = $graphObject->getProperty('email');
								$st_name = $graphObject->getProperty('name');
								$st_birth = $graphObject->getProperty('birthday');
								if($graphObject->getProperty('gender') == 'male')
									$bo_male = true;
								if($st_birth)
								{
									try
									{
										$dt_birth = new \types\TDate($st_birth,'m/d/y');
									}
									catch(\Exception $e)
									{
										$dt_birth = new \types\TDate('1800-01-01','y-m-d');
									}	
								}
								else
									$dt_birth = new \types\TDate('1800-01-01','y-m-d');
								$st_password = \libs\zeusLib\zeusLib::generate_hash(8);
								$in_author = (int) $_SESSION['webuser_subscribe_author'];
								unset($_SESSION['webuser_subscribe_author']);
								$in_user = \website\CDatabase::user_register($this, $st_hash, $st_email, $st_name, $bo_male, $dt_birth, $st_password, $st_facebook, $in_author);
								if($o_logged = \website\CDatabase::user_facebook_login($this, $st_facebook))
									$this->set_logged_website($o_logged);
								if($in_user < 0 && !$o_logged)
									$this->in_command = -6;
							}
							else
							{
								$request = new \libs\facebook\FacebookRequest($o_session, 'DELETE', '/me/permissions');
								$response = $request->execute();
								$this->in_command = -1;
							}
						}
						if(!$_SESSION['website'])
							$_SESSION['website'] = serialize(new \controller\persist\PParams());
						$this->o_params = new \controller\persist\PParams();
						if($in_user > 0)
							$this->o_params->set_param('subscribe', $in_user);
						$o_website = new \website\Website($this);
						$o_call = new \controller\persist\PSystemCall($this);
						$o_call->set_params($this->o_params);
						$o_website->call_page($o_call);
						break;
					case 'admin':
						require_once $_ENV['PATH'].'admin/Admin.php';
						if($st_bootstrap = $_REQUEST['bootstrap'])
						{
							$this->o_bootstrap = new \controller\persist\Bootstrap($st_bootstrap);
							$this->in_command = $this->o_bootstrap->get_param(0);
							unset($_REQUEST['bootstrap']);
						}
						else
						{
							$this->in_command = $_REQUEST['command'];
							unset($_REQUEST['command']);
						}
						$this->o_params = new \controller\persist\PParams();
						foreach($_REQUEST as $name => $value)
						{
							$value = \libs\zeusLib\zeusLib::decode_url($value);
							$object = json_decode($value);
							if(is_a($object, stdClass))
								$this->to_params($object,$this->o_params);
							else
								$this->o_params->set_param($name, $value);
						}
						$o_admin = new \admin\Admin($this);
						$o_call = new \controller\persist\PSystemCall($this);
						$o_call->set_params($this->o_params);
						$o_admin->callCommand($o_call);
						break;
					default:
						require_once $_ENV['PATH'].'website/Website.php';
						if(!$_SESSION['website'])
							$_SESSION['website'] = serialize(new \controller\persist\PParams());
						$this->o_params = new \controller\persist\PParams();
						if($arg = $_REQUEST['arg'])
						{
							$arg = ereg_replace('/$', '', $arg);
							$arg = explode('/', $arg);
							$this->o_params->set_param('arg', $arg);
							unset($_REQUEST['arg']);
						}
						else
						{
							$this->in_command = $_REQUEST['command'];
							unset($_REQUEST['command']);
						}
						foreach($_REQUEST as $name => $value)
						{
							$object = json_decode($value);
							if(is_a($object, stdClass))
								$this->to_params($object,$this->o_params);
							else
								$this->o_params->set_param($name, $value);
						}
						$o_website = new \website\Website($this);
						$o_call = new \controller\persist\PSystemCall($this);
						$o_call->set_params($this->o_params);
						$o_website->call_page($o_call);
				}
			}
			catch(\Exception $e)
			{
				$o_log = $this->insert_log();
				$o_log->set_value($e->getMessage());
			}
			$this->save_log();
			if($n = count($this->v_debug))
				if($f = fopen($_ENV['PATH'].'debug.txt','w+'))
				{
					for($c = 0;$c < $n;$c++)
						fwrite($f, $this->v_debug[$c]."\n\n");
					fclose($f);
				}
		}
		
		public function to_params(\stdClass $object,\controller\persist\PParams &$o_params)
		{
			foreach($object as $name => $value)
			{
				if(is_array($value))
				{
					$n_objects = count($value);
					$o_subparams = new \controller\persist\PParams();
					for($c = 0;$c < $n_objects;$c++)
						$this->to_params($value[$c], $o_subparams);
					$o_params->set_param($name, $o_subparams);
				}
				else
					$o_params->set_param($name, $value);
			}
		}
		
		public function get_logged_admin()
		{
			return unserialize($_SESSION['admin']);
		}
		
		public function get_logged_website()
		{
			$v_logged = unserialize($_SESSION['website']);
			return $v_logged->get_param($_ENV['SERVER']);
		}
		
		public function set_logged_website($o_logged)
		{
			$v_logged = unserialize($_SESSION['website']);
			$v_logged->set_param($_ENV['SERVER'],$o_logged);
			$_SESSION['website'] = serialize($v_logged);
		}
		
		public function clear_logged_website()
		{
			$v_logged = unserialize($_SESSION['website']);
			$v_logged->unset_param($_ENV['SERVER']);
			$_SESSION['website'] = serialize($v_logged);
		}
		
		public function set_command($in_command)
		{
			$this->in_command = $in_command;
		}
		
		public function get_command()
		{
			return $this->in_command;
		}
		
		public function bootstrap()
		{
			return $this->o_bootstrap;
		}
		
		public function get_system_config()
		{
			return $this->o_system_config;
		}
		
		public function get_database()
		{
			return $this->o_db;
		}
		
		public function insert_debug($st_value)
		{
			array_push($this->v_debug, $st_value);
		}
		
		private function load_configs()
		{
			$this->o_system_log = new \libs\docXML\nodeXML('logs');
			$this->o_system_config = new \controller\persist\PSystem();
			$o_xml = new \libs\docXML\docXML();
			$o_xml->load_xml($_ENV['PATH'].'.system/config/system.xml');
			$this->o_system_config->set_name($o_xml->get_root()->get_node_by_name('name')->get_value());
			$this->o_system_config->set_user($o_xml->get_root()->get_node_by_name('user')->get_value());
			$this->o_system_config->set_password($o_xml->get_root()->get_node_by_name('password')->get_value());
			$this->o_system_config->set_status($o_xml->get_root()->get_node_by_name('status')->get_value());
			$this->o_system_config->set_timeout($o_xml->get_root()->get_node_by_name('timeout')->get_value());
			try
			{
				$this->o_system_config->set_email($o_xml->get_root()->get_node_by_name('email')->get_value());
				$this->o_system_config->set_database($o_xml->get_root()->get_node_by_name('database')->get_value());
				$this->o_system_config->get_smtp()->set_host($o_xml->get_root()->get_node_by_name('smtp')->get_node_by_name('host')->get_value());
				$this->o_system_config->get_smtp()->set_port($o_xml->get_root()->get_node_by_name('smtp')->get_node_by_name('port')->get_value());
				$this->o_system_config->get_smtp()->set_authenticate($o_xml->get_root()->get_node_by_name('smtp')->get_node_by_name('authentic')->get_value());
				$this->o_system_config->get_smtp()->set_user($o_xml->get_root()->get_node_by_name('smtp')->get_node_by_name('user')->get_value());
				$this->o_system_config->get_smtp()->set_password($o_xml->get_root()->get_node_by_name('smtp')->get_node_by_name('password')->get_value());
				$st_address = $o_xml->get_root()->get_node_by_name('smtp')->get_node_by_name('address')->get_value();
				$st_name = $o_xml->get_root()->get_node_by_name('smtp')->get_node_by_name('name')->get_value();
				$this->o_system_config->get_smtp()->set_from($st_address,$st_name);
			}
			catch(\Exception $e)
			{
				$this->o_system_config->set_status(0);
			}
		}
		
		private function load_databases()
		{
			$this->o_db = new \libs\database\CDatabase();
			$st_database = $this->o_system_config->get_database();
			$o_xml = new \libs\docXML\docXML();
			$o_xml->load_xml($_ENV['PATH'].'.system/config/database.xml');
			try 
			{
				$o_pgsql = $o_xml->get_root()->get_node_by_name($this->o_system_config->get_database());
				$o_config = new \libs\database\PConfigDB();
				$o_config->set_application($o_pgsql->get_node_by_name('application')->get_value());
				$o_config->set_host($o_pgsql->get_node_by_name('host')->get_value());
				$o_config->set_port($o_pgsql->get_node_by_name('port')->get_value());
				$o_config->set_name($o_pgsql->get_node_by_name('name')->get_value());
				$o_config->set_user($o_pgsql->get_node_by_name('user')->get_value());
				$o_config->set_password($o_pgsql->get_node_by_name('password')->get_value());
				$this->o_db->add_database('zeus', $o_config);
			}
			catch(\libs\database\PExceptionDB $e)
			{
				$this->o_system_config->set_status(0);
			}
		}
		
		private function save_log()
		{
			$o_xml = new \libs\docXML\docXML();
			$o_xml->load_xml($_ENV['PATH'].'.system/config/log.xml');
			$o_xml->get_root()->insert_nodes($this->o_system_log->get_nodes());
			$o_xml->save_xml($_ENV['PATH'].'.system/config/log.xml');
		}
		
		public function insert_log()
		{
			$o_error = new \libs\docXML\nodeXML('error');
			$st_datetime = date('Y-m-d h:i:s');
			$o_log = new \libs\docXML\nodeXML('log');
			$o_log->insert_node('datetime',$st_datetime);
			if($_ENV['SYSTEM'])
				$o_log->insert_node('application',$_ENV['SERVER']);
			$o_log->insert_node('session',session_id());
			$o_log->insert_node_object($o_error);
			$this->o_system_log->insert_node_object($o_log);
			return $o_error;
		}
		
		public function path_config($in_id)
		{
			if($in_id)
			{
				$st_query = "SELECT cfg_st_name,cfg_in_father FROM control.tbl_config WHERE cfg_in_id = $in_id;";
				$rs = $this->o_db->execute_query('zeus',$st_query);
				$path = $rs[0]->cfg_st_name.'/';
				while($in_id = $rs[0]->cfg_in_father)
				{
					$st_query = "SELECT cfg_st_name,cfg_in_father FROM control.tbl_config WHERE cfg_in_id = $in_id;";
					$rs = $this->o_db->execute_query('zeus',$st_query);
					$path = $rs[0]->cfg_st_name.'/'.$path;
				}
				$path = '/'.$path;
			}
			else
				$path = '/';
			return $path;
		}
		
		public function id_config($st_path)
		{
			if(preg_match('/\/$/', $st_path))
				$in_length = strlen($st_path) - 2;
			else
				$in_length = strlen($st_path) - 1;
			$st_path = substr($st_path, 1,$in_length);
			$v_config = explode('/', $st_path);
			$n_config = count($v_config);
			$in_father = 0;
			for($c = 0;$c < $n_config;$c++)
			{
				$st_config = $v_config[$c];
				if($in_father)
					$st_query = "SELECT cfg_in_id FROM control.tbl_config WHERE cfg_in_father = $in_father AND cfg_st_name = '$st_config';";
				else
					$st_query = "SELECT cfg_in_id FROM control.tbl_config WHERE cfg_in_father IS NULL AND cfg_st_name = '$st_config';";
				$rs = $this->o_db->execute_query('zeus',$st_query);
				$in_father = $rs[0]->cfg_in_id;
			}
			return $in_father;
		}
		
		/*
		 * @param string $st_path
		 * @param boolean $bo_complete
		 * @return \libs\docXML\nodeXML
		 */
		public function gets_config($st_path,$bo_complete)
		{
			if($bo_complete)
				$st_complete = 'TRUE';
			else
				$st_complete = 'FALSE';
			$st_query = "SELECT * FROM public.sp_config_gets('$st_path',$st_complete) AS wxml";
			$rs = $this->o_db->execute_query('zeus',$st_query);
			$st_xml = $rs[0]->wxml;
			$o_xml = new \libs\docXML\docXML();
			if($st_xml)
				$o_xml->set_xml($st_xml);
			else
				$o_xml->create('error');
			$o_xml = $o_xml->get_root();
			return $o_xml;
		}
		
		public function put_config($st_path,\libs\docXML\nodeXML $o_node)
		{
			$in_father = $this->id_config($st_path);
			if(!$in_father)
			{
				$st_type = $o_node->get_attribute_by_name('type')->get_value();
				$st_name = $o_node->get_name();
				$st_description = \libs\zeusLib\zeusLib::conf_database_char($o_node->get_attribute_by_name('description')->get_value());
				if($st_type == 'config')
				{
					$st_value = \libs\zeusLib\zeusLib::conf_database_char($o_node->get_value());
					$o_log = $this->insert_log();
					$o_log->set_value("SELECT control.sp_config_put(false, NULL, E'$st_name', E'$st_value', E'$st_description')");
					$st_query = "SELECT control.sp_config_put(false, NULL, E'$st_name', E'$st_value', E'$st_description')";
					$this->o_db->execute_query('zeus',$st_query);
				}
				else
				{
					$o_log = $this->insert_log();
					$o_log->set_value("SELECT control.sp_config_put(false, NULL, E'$st_name', E'$st_value', E'$st_description')");
					$st_query = "SELECT control.sp_config_put(true, NULL, E'$st_name', NULL, E'$st_description') AS res";
					$rs = $this->o_db->execute_query('zeus',$st_query);
					$in_father = $rs[0]->res;
					for($c = 0;$c < $o_node->count_nodes();$c++)
					{
						$o_subnode = $o_node->get_node_by_position($c);
						$this->put_child_config($in_father, $o_subnode);
					}
				}
			}
			else
				$this->put_child_config($in_father, $o_node);
		}
		
		public function del_config($st_path)
		{
			$in_id = $this->id_config($st_path);
			$st_query = "DELETE FROM control.tbl_config WHERE cfg_in_id = $in_id;";
			try 
			{
				$this->o_db->execute_query('zeus',$st_query);
				return $in_id;
			}
			catch(\libs\database\PExceptionDB $e)
			{
				return -1000;
			}
		}
		
		private function get_child_config($in_father,\libs\docXML\nodeXML &$o_node)
		{
			$st_query = "SELECT * FROM control.tbl_config WHERE cfg_in_father = $in_father ORDER BY cfg_in_id;";
			$rs = $this->o_db->execute_query('zeus',$st_query);
			$n_child = count($rs);
			for($c = 0;$c < $n_child;$c++)
			{
				$o_subnode = new \libs\docXML\nodeXML($rs[$c]->cfg_st_name);
				$o_subnode->insert_attribute('description', $rs[$c]->cfg_st_description);
				$o_node->insert_node_object($o_subnode);
				if($rs[$c]->cfg_bo_group == 't')
				{
					$o_subnode->insert_attribute('type', 'group');
					$this->get_child_config($rs[$c]->cfg_in_id,$o_subnode);
				}
				else
				{
					$o_subnode->insert_attribute('type', 'config');
					$o_subnode->set_value($rs[$c]->cfg_st_value);
				}
			}
		}
		
		private function put_child_config($in_father, \libs\docXML\nodeXML &$o_node)
		{
			$st_type = $o_node->get_attribute_by_name('type')->get_value();
			$st_name = $o_node->get_name();
			if($o_node->get_attribute_by_name('description'))
				$st_description = \libs\zeusLib\zeusLib::conf_database_char($o_node->get_attribute_by_name('description')->get_value());
			if($st_type == 'config')
			{
				$st_value = \libs\zeusLib\zeusLib::conf_database_char($o_node->get_value());
				$st_query = "SELECT control.sp_config_put(false, $in_father, E'$st_name', E'$st_value', E'$st_description')";
				$this->o_db->execute_query('zeus',$st_query);
			}
			else
			{
				$st_query = "SELECT control.sp_config_put(true, $in_father, E'$st_name', NULL, E'$st_description') AS res";
				$rs = $this->o_db->execute_query('zeus',$st_query);
				$in_father = $rs[0]->res;
				for($c = 0;$c < $o_node->count_nodes();$c++)
				{
					$o_subnode = $o_node->get_node_by_position($c);
					$this->put_child_config($in_father, $o_subnode);
				}
			}
		}
	}
?>