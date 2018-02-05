<?
    namespace Lib\Net\HTTP\Request;
    
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Net/HTTP/Request/Header.php';

	final class Document {
		
	    private $fl_version;
	    private $st_method;
	    private $st_path;
	    private $st_query;
	    private $o_header;
	    
	    function __construct() {
	        $this->o_header = new \Lib\Net\HTTP\Request\Header();
	    }
	    
	    public function header() {
	        return $this->o_header;
	    }

	    public function parse($st_message) {
		    $st_message = str_replace("\r",'', $st_message);
		    $in_pos = strpos($st_message,"\n\n");
		    $st_header = substr($st_message, 0,$in_pos);
		    $in_pos += 2;
		    $st_body = substr($st_message,$in_pos);
		    $this->parse_header($st_header);
		}
		
		public function path_to_array() {
		    $st_path = substr($this->st_path[0], 1);
		    if(!$this->st_path)
		        return;
	        $in_length = strlen($st_path);
	        if($st_path[$in_length - 1] == '/')
	            $st_path = substr($st_path, 0,$in_length - 1);
            $v_path = explode("/",$st_path);
            var_dump($v_path);
		}
		
		private function parse_header($st_header) {
		    $v_header = explode("\n",$st_header);
		    /*
		     * Parse Method
		     */
		    $st_header = array_shift($v_header);
		    if(!preg_match("/^[A-Z]+/",$st_header,$this->st_method))
		        throw new \Exception('Not Found Method',-1);
		    $this->st_method = $this->st_method[0];
		    $st_header = str_replace($this->st_method,'',$st_header);
		    /*
		     * Parse Version
		     */
		    if(!preg_match("/HTTP[\/\.0-9]+$/",$st_header,$this->fl_version))
		        throw new \Exception('Not Found Version',-2);
            $this->fl_version = $this->fl_version[0];
            $st_header = trim(str_replace($this->fl_version,'',$st_header));
            preg_match("/[\.0-9]+$/",$this->fl_version,$this->fl_version);
            $this->fl_version = $this->fl_version[0];
            /*
             * Parse Path
             */
            if(!preg_match("/^[a-zA-Z0-9\/\_\-\+\.\%]+/",$st_header,$this->st_path))
                throw new \Exception('Not Found Path',-3);
            $this->st_path = $this->st_path[0];
            $in_length = strlen($this->st_path);
            $st_header = substr($st_header,$in_length);
            /*
             * Parse Query
             */
            $this->st_query = $st_header;
            /*
             * Parse Header
             */
            $h_header = array();
            while($v_header) {
                $st_header = array_shift($v_header);
                $in_pos = strpos($st_header,':');
                $st_name = substr($st_header,0,$in_pos);
                $st_value = substr($st_header,++$in_pos);
                $h_header[$st_name] = trim($st_value);
            }
            $this->o_header->parser($h_header);
		}
		
	}
?>