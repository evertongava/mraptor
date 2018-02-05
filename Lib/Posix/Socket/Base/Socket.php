<?
    #http://man7.org/linux/man-pages/man2/socket.2.html
    #http://man7.org/linux/man-pages/man7/socket.7.html

    namespace Lib\Posix\Socket\Base;
    
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Socket/Base/Exception.php';

	class Socket {
		
	    protected $hnd_socket;
	    protected $in_domain;
	    protected $in_type;
	    protected $in_protocol;
	    protected $bo_broadcast;
	    protected $bo_debug;
	    protected $bo_dont_route;
	    protected $bo_keep_alive;
	    protected $bo_oob_inline;
	    protected $in_receive_buffer;
	    protected $in_send_buffer;

	    function __construct($in_domain,$in_type,$in_protocol) {
	        $this->hnd_socket = socket_create($in_domain,$in_type,$in_protocol);
	        $this->in_domain = $in_domain;
	        $this->in_type = $in_type;
	        $this->in_protocol = $in_protocol;
	    }
		
		public function domain() {
		    return $this->in_domain;
		}
		
		public function type() {
		    return $this->in_type;
		}
		
		public function protocol() {
		    return $this->in_protocol;
		}
		
		public function get_broadcast() {
		    return $this->bo_broadcast;
		}
		
		public function get_debug() {
		    return $this->bo_debug;
		}
		
		public function get_dont_route() {
		    return $this->bo_dont_route;
		}
		
		public function get_keep_alive() {
		    return $this->bo_keep_alive;
		}
		
		public function get_oob_inline() {
		    return $this->bo_oob_inline;
		}
		
		public function get_receive_buffer() {
		    return $this->in_receive_buffer;
		}
		
		public function get_send_buffer() {
		    return $this->in_send_buffer;
		}
		
		public function close() {
		    @socket_close($this->hnd_socket);
		}
		
		function __destruct() {
		    $this->close();
		}
		
	}
?>