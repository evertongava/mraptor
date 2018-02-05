<?
    namespace Lib\Posix\Socket\Base;
    
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Socket/Base/NetSocket.php';

    class Connection extends \Lib\Posix\Socket\Base\NetSocket {
		
	    function __construct(\Lib\Posix\Socket\Base\Socket $o_socket,$hnd_socket,$st_address,$in_port) {
	        $this->hnd_socket = $hnd_socket;
	        $this->in_domain = $o_socket->domain();
	        $this->in_type = $o_socket->type();
	        $this->in_protocol = $o_socket->protocol();
	        $this->st_address = $st_address;
	        $this->in_port = $in_port;
	        $this->bo_broadcast = socket_get_option($this->hnd_socket, \SOL_SOCKET, \SO_BROADCAST);
		}

	}
?>