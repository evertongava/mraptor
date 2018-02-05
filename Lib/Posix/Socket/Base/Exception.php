<?
    namespace Lib\Posix\Socket\Base;

	final class Exception extends \Exception {
		
	    function __construct($hnd_socket) {
	        $in_error = socket_last_error($hnd_socket);
	        $st_error = socket_strerror($in_error);
	        parent::__construct($st_error,$in_error);
		}		
	}
?>