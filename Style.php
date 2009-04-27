<? 

function checkpath($path){
//    echo $path;
}

class STYLECACHEER
{
    public static $p = array(

        'requested_file' => '',

        'css_dir'        => '',
        'cssc_cache_dir' => '',

        'cached_dir'     => '',
        'relative_dir'   => '',

        'cached_file'    => '',
        'relative_file'  => '',

        'plugins_path'   => '',
        'plugins'        => '',
        'requested_dir'  => '',
        
        'requested_mod_time' => '',
        
        'recache'        => false
        
        
    );
    public static function _config()
    {
        $style_path = $_SERVER['DOCUMENT_ROOT'] . '/a/styles';
        self::$p['requested_file'] = isset($_GET['request']) ? $_SERVER['DOCUMENT_ROOT'] . $_GET['request'] : '';
        self::$p['requested_dir']  = preg_replace('#/[^/]*$#', '', self::$p['requested_file']);
        self::$p['css_dir']        = $style_path;
        self::$p['cssc_cache_dir'] = $_SERVER['DOCUMENT_ROOT'] . '/a/cache/';
        self::$p['relative_file']  = substr(self::$p['requested_file'], strlen(self::$p['css_dir']) + 1);;
        self::$p['absolute_file']  = self::$p['requested_file'];
        self::$p['relative_dir']   = (strpos(self::$p['relative_file'], '/') === false) ? '' : preg_replace("/\/[^\/]*$/", '', self::$p['relative_file']);
        self::$p['cached_dir']     = self::$p['cssc_cache_dir'].self::$p['relative_dir'];
        self::$p['cached_dir']     = $_SERVER['DOCUMENT_ROOT'] . '/a/cache/';
        self::$p['plugins_path']   = $_SERVER['DOCUMENT_ROOT'] . '/_/Jack/Style/Plugin';
        self::$p['recache']        = isset($_GET['recache']);
    }
    public static function init()
    {


        $requested_file = isset($_GET['request']) ? $_SERVER['DOCUMENT_ROOT'] . $_GET['request'] : '';
        

        self::_config();

        $flags = self::loadPlugins();
        $checksum = self::serializeQueryString($flags);
        
        self::$p['cached_file'] = self::$p['cssc_cache_dir']
                                . preg_replace(
                                    '#(.+)(\.css)$#i',
                                    "$1-{$checksum}$2",
                                    self::$p['relative_file'] );

        if (self::$p['recache'] && file_exists(self::$p['cached_file']))
        {
        	unlink(self::$p['cached_file']);
        }

        $requested_mod_time	= filemtime(self::$p['absolute_file']);
        $cached_mod_time	= (int) @filemtime(self::$p['cached_file']);
        // cache may not exist, silence error with @

        // Recreate the cache if stale or nonexistent
        if ($cached_mod_time < $requested_mod_time)
        {
            extract(self::$p);
            include_once('Style/Cacher.php');  
        }
        // Or send 304 header if appropriate
        else if 
        (
            isset($_SERVER['HTTP_IF_MODIFIED_SINCE'], $_SERVER['SERVER_PROTOCOL']) && 
            $requested_mod_time <= strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])
        )
        {
            header("{$_SERVER['SERVER_PROTOCOL']} 304 Not Modified");
            exit();
        }
        
        self::$p['requested_mod_time'] = $requested_mod_time;

        self::render();
                                    
        exit();
        // ^ path to cache of requested file, relative to css directory, eg. css-cacheer/cache/nested/sample.css
        

    }
    
    public static function loadPlugins()
    {
        require_once('Style/Plugin.php');
        $flags = array();
        $plugins = array();
        $plugin_path = $plugin_path = CONFIG::get('ACTIVEFRONT_ROOT') . '/Style/Plugin';
        //$plugin_path = 'Style/Plugin';
        if (is_dir($plugin_path))
        {
            if ($dir_handle = opendir($plugin_path)) 
            {
                while (($plugin_file = readdir($dir_handle)) !== false) 
                {
                    if (substr($plugin_file, 0, 1) == '.' || substr($plugin_file, 0, 1) == '-')
                    { 
                        continue; 
                    }
                    require_once($plugin_path.'/'.$plugin_file);
                    if (isset($plugin_class) && class_exists($plugin_class))
                    {
                        self::$p['plugins'][$plugin_class] = new $plugin_class($flags);
                        $flags = array_merge($flags, self::$p['plugins'][$plugin_class]->flags);
                    }
                }
                closedir($dir_handle);
            }
        }
        // Create hash of query string to allow variables to be cached
        return $flags;
    }
    
    public static function render()
    {
        header('Content-Type: text/css');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', (int) self::$p['requested_mod_time']).' GMT');
        //@include(self::$p['cached_file']);
        @require_once(self::$p['cached_file']);
    }
    
    public static function serializeQueryString($flags)
    {
        $args = $flags;
        ksort($args);
        $checksum = md5(serialize($args));
        return $checksum;
    }
    
}