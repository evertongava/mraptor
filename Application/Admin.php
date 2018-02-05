<?

    namespace Application;
    
    $_ENV['MRAPTOR_ROOT'] = dirname(getcwd());
    
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Socket/IPv4/TCPServer.php';
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Posix/Process/Process.php';
    require_once $_ENV['MRAPTOR_ROOT'].'/Lib/Net/HTTP/Request/Document.php';
    
    class Admin {
        
        static private $o_app;
        private $in_pid;
        private $o_config;
        private $o_socket;
               
        function __construct() {
            $this->in_pid = posix_getpid();
            print('Start Process PID: '.$this->in_pid."\n");
            ob_implicit_flush();
            /*
             * Enable asynchronous signal handling
             */
            \Lib\Posix\Process\Process::set_asynchronous_signals(true);
            \Lib\Posix\Process\Process::signal_handler(SIGTERM,$this,"term_handler",false);
            $this->start_server();
        }
        
        static public function run() {
            self::$o_app = new \Application\Admin();
        }
        
        public function term_handler($sign,$signinfo) {
            $in_pgid = posix_getpgid($this->in_pid) * -1;
            posix_kill($in_pgid,$sign);
            exit(0);
        }
        
        private function  start_server() {
            # Load Config File
            $st_file = file_get_contents($_ENV['MRAPTOR_ROOT'].'/Config/Standard.json');
            # Decode Config JSON
            $this->o_config = json_decode($st_file);
            # Start Socket
            print('Start Webserver: '.$this->o_config->webservice->address.':'.$this->o_config->webservice->port."\t");
            $this->o_socket = new \Lib\Posix\Socket\IPv4\TCPServer(\SOCK_STREAM);
            try {
                $this->o_socket->set_reuse_address(true);
                $this->o_socket->listen($this->o_config->webservice->address,$this->o_config->webservice->port);
            } catch(\Lib\Posix\Socket\Base\Exception $e) {
                var_dump($e);
                exit(0);
            }
            print("[OK]\n");
            while(true)
                $this->start_process();
        }
        
        private function  start_process() {
            if($o_connection = $this->o_socket->accept()) {
                print('Client Connected: '.$o_connection->address().':'.$o_connection->port()."\n");
                try {
                    $in_pid = \Lib\Posix\Process\Process::fork();
                    if(!$in_pid) {
                        $this->in_pid = posix_getpid();
                        $this->child($o_connection);
                    }
                } catch(\Exception $e) {
                    $o_connection->close();
                }
            }
        }
        
        private function child($o_connection) {
            try {
                $st_message = $o_connection->receive(512);               
                $o_request = new \Lib\Net\HTTP\Request\Document();
                $o_request->parse($st_message);
                var_dump($o_request);
            } catch(\Exception $e){ }
            $o_connection->send("\nConectado ao servidor\n\n");
            $o_connection->close();
            print('Client Disconnected: '.$o_connection->address().':'.$o_connection->port()."\t");
            print('PID: '.\Lib\Posix\Process\Process::ppid()."\n");
            exit(0);
        }
        
    }
    
    \Application\Admin::run();
    
?>