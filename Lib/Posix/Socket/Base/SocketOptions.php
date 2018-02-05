<?
    namespace Lib\Posix\Socket\Base;

    Trait SocketOptions {

        public function set_broadcast($bo_broadcast) {
            if(@socket_set_option($this->hnd_socket, \SOL_SOCKET, \SO_BROADCAST, $bo_broadcast))
                $this->bo_broadcast = $bo_broadcast;
            else
                throw \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
        }
        
        public function set_debug($bo_debug) {
            if(@socket_set_option($this->hnd_socket, \SOL_SOCKET, \SO_DEBUG, $bo_debug))
                $this->bo_debug = $bo_debug;
            else
                throw \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
        }
        
        public function set_dont_route($bo_dont_route) {
            if(@socket_set_option($this->hnd_socket, \SOL_SOCKET, \SO_DONTROUTE, $bo_dont_route))
                $this->bo_dont_route = $bo_dont_route;
            else
                throw \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
        }
        
        public function set_keep_alive($bo_keep_alive) {
            if(@socket_set_option($this->hnd_socket, \SOL_SOCKET, \SO_KEEPALIVE, $bo_keep_alive))
                $this->bo_keep_alive = $bo_keep_alive;
            else
                throw \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
        }
        
        public function set_oob_inline($bo_oob_inline) {
            if(@socket_set_option($this->hnd_socket, \SOL_SOCKET, \SO_OOBINLINE, $bo_oob_inline))
                $this->bo_oob_inline = $bo_oob_inline;
            else
                throw \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
        }
        
        public function set_receive_buffer($in_receive_buffer) {
            if(@socket_set_option($this->hnd_socket, \SOL_SOCKET, \SO_RCVBUF, $in_receive_buffer))
                $this->in_receive_buffer = $in_receive_buffer;
            else
                throw \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
        }
        
        public function set_send_buffer($in_send_buffer) {
            if(@socket_set_option($this->hnd_socket, \SOL_SOCKET, \SO_SNDBUF, $in_send_buffer))
                $this->in_send_buffer = $in_send_buffer;
            else
                throw \Lib\Posix\Socket\Base\Exception($this->hnd_socket);
        }

	}
?>