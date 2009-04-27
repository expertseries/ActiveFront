<? 

// Pre-PHP 5.3
if (!function_exists('get_called_class')): 
  function get_called_class()
  {
    $bt = debug_backtrace();
    $lines = file($bt[1]['file']);
    preg_match('/([a-zA-Z0-9\_]+)::'.$bt[1]['function'].'/',
               $lines[$bt[1]['line']-1],
               $matches);
    return $matches[1];
  }
endif; 

class PAGE
{
    public static $config = array(
        'id'             => '',
        'name'           => '',
        'menu'           => '',
        'body'           => '',
        'subcontent'     => '',
        'root'           => '',
        'buffer'         => NULL,
        'page'           => false
    );
    
    // DEFAULT PARTIALS, ADD MORE IN webroot/_constants.php
    public static $content = array(
        'TITLE'        => '',
        'KEYWORDS'     => '',
        'DESCRIPTION'  => '',
        'SCRIPT'       => '',
        'STYLE'        => '',
        'MENU'         => '',
        'BODY'         => '',
        'PAGE'         => '',
        'COMMENT'      => '',
        'EXTRA'        => '',
        'FOOTER'       => '',
        'EXTENDED'     => ''
    );

    protected static function _capture($path)
    {
        require_once($path);
    }
    public static function render()
    {
        if ( get_called_class() == 'PAGE' ) {
            self::_capture( self::$config['body'] );
            self::_capture( self::$config['menu'] );

            require_once('Style/Parser.php');

            self::_capture( self::$config['page'] );
            if (self::$content[ self::$config['buffer'] ]) {
                self::$content[ self::$config['buffer'] ] .= ob_get_clean();
            } else {
                self::$content[ self::$config['buffer'] ] = ob_get_clean();
            }
        }
        echo (isset(self::$content[get_called_class()])) 
             ? self::$content[get_called_class()]
             : '';
    }

    protected function __construct() {} 
    final private function __clone() {}

    public static function set($arr) { foreach ($arr as $k => $v) { self::$config[$k] = $v; } }

    public static function id()   { return self::$config['id'];   }
    public static function name() { return self::$config['name']; }
 
    public static function append() {
        $className = get_called_class();
        if (isset(self::$config['buffer'])){
            if (isset(self::$content[ self::$config['buffer'] ])) {
                self::$content[ self::$config['buffer'] ] .= ob_get_clean();
            } else {
                self::$content[ self::$config['buffer'] ] = ob_get_clean();
            }
        }
        self::$config['buffer'] = $className;
        ob_start();
    }
        
        
        public static function i($file)
    {
        $paths = array(
            self::$config['subcontent'] . strtolower(self::$config['id']) . '/' . $file . (preg_match('/.php$/',$file)? '' : '.php'),
            self::$config['subcontent'] . $file . (preg_match('/.php$/',$file)? '' : '.php'),
            self::$config['root'] . $file . (preg_match('/.php$/',$file)? '' : '.php')
        );
        foreach ($paths as $p)
        {
            if ( file_exists($p) ) {
               include_once($p);
            }
        }
    }
}
class TITLE extends page {}
class DESCRIPTION extends page {}
class KEYWORDS extends page {}
class STYLE extends page {}
class SCRIPT extends page {}
class MENU extends page {}
class EXTRA extends page {}
class BODY extends page {}
class FOOTER extends page {}
class INLINE extends page { public static function end($id = 'default') { echo ob_get_clean(); } }
class COMMENT extends page {}
