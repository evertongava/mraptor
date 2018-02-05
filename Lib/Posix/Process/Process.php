<?
    #http://man7.org/linux/man-pages/man2/socket.2.html
    #http://man7.org/linux/man-pages/man7/socket.7.html

    namespace Lib\Posix\Process;
    
    class Process {
		
	    static private $in_ppid;
	    
	    public static function ppid() {
	        return self::$in_ppid;
	    }
	    
	    public static function set_asynchronous_signals($bo_signal) {
	        pcntl_async_signals($bo_signal);
	    }
	    
	    public static function get_asynchronous_signals() {
	        return pcntl_async_signals();
	    }
	    
	    public static function signal_handler($in_signo,&$o_callback,$fc_callback,$bo_restart_syscalls = TRUE) {
	        pcntl_signal($in_signo,array($o_callback,$fc_callback),$bo_restart_syscalls);
	    }
		
	    public static function fork() {
	        $in_ppid = posix_getpid();
	        $in_pgid = posix_getpgid($in_ppid);
	        $in_pid = pcntl_fork();
	        if($in_pid == -1)
	            throw new \Exception('could not fork',-1);
            if($in_pid) {
                posix_setpgid($in_pid,$in_pgid);
                return $in_pid;
            }
            else
                self::$in_ppid = $in_ppid;
            return;
	    }
		
	}
?>