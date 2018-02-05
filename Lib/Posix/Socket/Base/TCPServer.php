<?
    namespace Lib\Posix\Socket\Base;
    
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Socket/Base/Exception.php';
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Socket/Base/Socket.php';
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Socket/Base/Connection.php';
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Socket/Base/SocketOptions.php';

    class TCPServer extends \Lib\Posix\Socket\Base\Socket {
        
        use \Lib\Posix\Socket\Base\SocketOptions;
        
        protected $bo_reuse_address;
        
        function __construct($in_domain,$in_type) {
            parent::__construct($in_domain, $in_type, 6);
        }
        
        public function listen($st_address, $in_port, $in_max_connection = \SOMAXCONN) {
            if(!@socket_bind($this->hnd_socket,$st_address,$in_port))
                throw new \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
            if(!@socket_listen($this->hnd_socket,$in_max_connection))
                throw \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
        }
        
        public function accept() {
            if($hnd_connection = @socket_accept($this->hnd_socket)) {
                socket_getpeername($hnd_connection,$st_address,$in_port);
                return new \Lib\Posix\Socket\Base\Connection($this,$hnd_connection,$st_address,$in_port);
            }
            return;
        }
        
        public function get_reuse_address() {
            return $this->bo_reuse_address;
        }
        
        public function set_reuse_address($bo_reuse_address) {
            if(@socket_set_option($this->hnd_socket, \SOL_SOCKET, \SO_REUSEADDR, $bo_reuse_address))
                $this->bo_reuse_address = $bo_reuse_address;
            else
                throw new \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
        }
        
	}
?>