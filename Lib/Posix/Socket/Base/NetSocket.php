<?
    namespace Lib\Posix\Socket\Base;
    
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Socket/Base/Socket.php';
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Socket/Base/Exception.php';

    class NetSocket extends \Lib\Posix\Socket\Base\Socket {
		
	    protected $st_address;
	    protected $in_port;

	    public function address() {
	        return $this->st_address;
	    }
	    
	    public function port() {
	        return $this->in_port;
	    }
	    
	    public function send($st_message,$in_flag = 0) {
	        if(!socket_send($this->hnd_socket,$st_message,strlen($st_message),$in_flag))
	            throw new \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
	    }
	    
	    public function receive($in_length,$in_flags = 0) {
	        $st_message = '';
	        if(!@socket_recv($this->hnd_socket,$st_message,$in_length,$in_flags))
	            throw new \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
            return $st_message;
	    }

	}
?>