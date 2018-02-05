<?
    namespace Lib\Net\HTTP\Request;

	final class Header {
		
	    private $st_accept;
	    private $st_accept_charset;
	    private $st_accept_encoding;
	    private $st_accept_language;
	    private $st_allow;
	    private $st_authorization;
	    private $st_cache_control;
	    private $st_connection;
	    private $st_content_encoding;
	    private $st_content_language;
	    private $st_content_length;
	    private $st_content_location;
	    private $st_content_md5;
	    private $st_content_range;
	    private $st_content_type;
	    private $st_cookie;
	    private $st_cookie2;
	    private $st_dnt;
	    private $st_date;
	    private $st_expect;
	    private $st_expires;
	    private $st_forwarded;
	    private $st_from;
	    private $st_host;
        private $st_if_match;
	    private $st_if_modified_since;
	    private $st_if_none_match;
	    private $st_if_range;
	    private $st_if_unmodified_since;
	    private $st_last_modified;
	    private $st_max_forwards;
	    private $st_origin;
	    private $st_pragma;
	    private $st_proxy_authorization;
	    private $st_range;
	    private $st_referer;
	    private $st_te;
	    private $st_transfer_encoding;
	    private $st_upgrade;
	    private $st_upgrade_insecure_requests;
	    private $st_user_agent;
	    private $st_vary;
	    private $st_via;
	    private $st_warning;

	    public function parser($h_header) {
	        if(isset($h_header['Accept']))
	            $this->st_accept = $h_header['Accept'];
            if(isset($h_header['Accept-Charset']))
                $this->st_accept_charset = $h_header['Accept-Charset'];
            if(isset($h_header['Accept-Encoding']))
                $this->st_accept_encoding = $h_header['Accept-Encoding'];
            if(isset($h_header['Accept-Language']))
                $this->st_accept_language = $h_header['Accept-Language'];
            if(isset($h_header['Allow']))
                $this->st_allow = $h_header['Allow'];
            if(isset($h_header['Authorization']))
                $this->st_authorization = $h_header['Authorization'];
            if(isset($h_header['Cache-Control']))
                $this->st_cache_control = $h_header['Cache-Control'];
            if(isset($h_header['Connection']))
                $this->st_connection = $h_header['Connection'];
            if(isset($h_header['Content-Encoding']))
                $this->st_content_encoding = $h_header['Content-Encoding'];
            if(isset($h_header['Content-Language']))
                $this->st_content_language = $h_header['Content-Language'];
            if(isset($h_header['Content-Length']))
                $this->st_content_length = $h_header['Content-Length'];
            if(isset($h_header['Content-Location']))
                $this->st_content_location = $h_header['Content-Location'];
            if(isset($h_header['Content-MD5']))
                $this->st_content_md5 = $h_header['Content-MD5'];
            if(isset($h_header['Content-Range']))
                $this->st_content_range = $h_header['Content-Range'];
            if(isset($h_header['Content-Type']))
                $this->st_content_type = $h_header['Content-Type'];
            if(isset($h_header['Cookie']))
                $this->st_cookie = $h_header['Cookie'];
            if(isset($h_header['Cookie2']))
                $this->st_cookie2 = $h_header['Cookie2'];
            if(isset($h_header['DNT']))
                $this->st_date = $h_header['DNT'];
            if(isset($h_header['Date']))
                $this->st_date = $h_header['Date'];
            if(isset($h_header['ETag']))
                $this->st_etag = $h_header['ETag'];
            if(isset($h_header['Expect']))
                $this->st_expect = $h_header['Expect'];
            if(isset($h_header['Expires']))
                $this->st_expires = $h_header['Expires'];
            if(isset($h_header['Forwarded']))
                $this->st_forwarded = $h_header['Forwarded'];
            if(isset($h_header['From']))
                $this->st_from = $h_header['From'];
	        if(isset($h_header['Host']))
                $this->st_host = $h_header['Host'];
            if(isset($h_header['If-Match']))
                $this->st_if_match = $h_header['If-Match'];
            if(isset($h_header['If-Modified-Since']))
                $this->st_if_modified_since = $h_header['If-Modified-Since'];
            if(isset($h_header['If-None-Match']))
                $this->st_if_none_match = $h_header['If-None-Match'];
            if(isset($h_header['If-Range']))
                $this->st_if_range = $h_header['If-Range'];
            if(isset($h_header['If-Unmodified-Since']))
                $this->st_if_unmodified_since = $h_header['If-Unmodified-Since'];
            if(isset($h_header['Last-Modified']))
                $this->st_last_modified = $h_header['Last-Modified'];
            if(isset($h_header['Max-Forwards']))
                $this->st_max_forwards = $h_header['Max-Forwards'];
            if(isset($h_header['Origin']))
                $this->st_origin = $h_header['Origin'];
            if(isset($h_header['Pragma']))
                $this->st_pragma = $h_header['Pragma'];
            if(isset($h_header['Proxy-Authorization']))
                $this->st_proxy_authorization = $h_header['Proxy-Authorization'];
            if(isset($h_header['Range']))
                $this->st_range = $h_header['Range'];
            if(isset($h_header['Referer']))
                $this->st_referer = $h_header['Referer'];
            if(isset($h_header['TE']))
                $this->st_te = $h_header['TE'];
            if(isset($h_header['Transfer-Encoding']))
                $this->st_transfer_encoding = $h_header['Transfer-Encoding'];
            if(isset($h_header['Upgrade']))
                $this->st_upgrade = $h_header['Upgrade'];
            if(isset($h_header['Upgrade-Insecure-Requests']))
                $this->st_upgrade_insecure_requests = $h_header['Upgrade-Insecure-Requests'];
            if(isset($h_header['User-Agent']))
                $this->st_user_agent = $h_header['User-Agent'];
            if(isset($h_header['Vary']))
                $this->st_vary = $h_header['Vary'];
            if(isset($h_header['Via']))
                $this->st_via = $h_header['Via'];
            if(isset($h_header['Warning']))
                $this->st_warning = $h_header['Warning'];
		}
		
		public function get_accept() {
		    return $this->st_accept;
		}
		
		public function set_accept($st_accept) {
		    $this->st_accept = $st_accept;
		}
		
		public function get_accept_charset() {
		    return $this->st_accept_charset;
		}
		
		public function get_accept_encoding() {
		    return $this->st_accept_encoding;
		}
		
		public function get_accept_language() {
		    return $this->st_accept_language;
		}
		
		public function get_allow() {
		    return $this->st_allow;
		}
		
		public function get_authorization() {
		    return $this->st_authorization;
		}
		
		public function get_cache_control() {
		    return $this->st_cache_control;
		}
		
		public function get_connection() {
		    return $this->st_connection;
		}
		
		public function get_content_encoding() {
		    return $this->st_content_encoding;
		}
		
		public function get_content_language() {
		    return $this->st_content_language;
		}
		
		public function get_content_length() {
		    return $this->st_content_length;
		}
		
		public function get_content_location() {
		    return $this->st_content_location;
		}
		
		public function get_content_md5() {
		    return $this->st_content_md5;
		}
		
		public function get_content_range() {
		    return $this->st_content_range;
		}
		
		public function get_content_type() {
		    return $this->st_content_type;
		}
		
		public function get_cookie() {
		    return $this->st_cookie;
		}
		
		public function get_cookie2() {
		    return $this->st_cookie2;
		}
		
		public function get_dnt() {
		    return $this->st_dnt;
		}
		
		public function get_date() {
		    return $this->st_date;
		}
		
		public function get_expect() {
		    return $this->st_expect;
		}
		
		public function get_expires() {
		    return $this->st_expires;
		}
		
		public function get_forwarded() {
		    return $this->st_forwarded;
		}
		
		public function get_from() {
		    return $this->st_from;
		}
		
		public function get_host() {
		    return $this->st_host;
		}
		
		public function get_if_match() {
		    return $this->st_if_match;
		}
		
		public function get_if_modified_since() {
		    return $this->st_if_modified_since;
		}
		
		public function get_if_none_match() {
		    return $this->st_if_none_match;
		}
		
		public function get_if_range() {
		    return $this->st_if_range;
		}
		
		public function get_if_unmodified_since() {
		    return $this->st_if_unmodified_since;
		}
		
		public function get_last_modified() {
		    return $this->st_last_modified;
		}
		
		public function get_max_forwards() {
		    return $this->st_max_forwards;
		}
		
		public function get_origin() {
		    return $this->st_origin;
		}
		
		public function get_pragma() {
		    return $this->st_pragma;
		}
		
		public function get_proxy_authorization() {
		    return $this->st_proxy_authorization;
		}
		
		public function get_range() {
		    return $this->st_range;
		}
		
		public function get_referer() {
		    return $this->st_referer;
		}
		
		public function get_te() {
		    return $this->st_te;
		}
		
		public function get_transfer_encoding() {
		    return $this->st_transfer_encoding;
		}
		
		public function get_upgrade() {
		    return $this->st_upgrade;
		}
		
		public function get_upgrade_insecure_requests() {
		    return $this->st_upgrade_insecure_requests;
		}
		
		public function get_user_agent() {
		    return $this->st_user_agent;
		}
		
		public function get_vary() {
		    return $this->st_vary;
		}
		
		public function get_via() {
		    return $this->st_via;
		}
		
		public function get_warning() {
		    return $this->st_warning;
		}

	}
?>