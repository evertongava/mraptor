<?
    namespace Lib\Posix\Socket\IPv4;
    
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Socket/Base/TCPServer.php';

    class TCPServer extends \Lib\Posix\Socket\Base\TCPServer {
                
        function __construct($in_type) {
            parent::__construct(\AF_INET, $in_type);
        }
        
	}
?>