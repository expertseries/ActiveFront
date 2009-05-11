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
        'page'           => false,
        'layout_default' => '',
        'layout_id'      => ''
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
        'LAYOUT'       => '',
        'PAGE'         => '',
        'COMMENT'      => '',
        'EXTRA'        => '',
        'FOOTER'       => '',
        'EXTENDED'     => ''
    );

    protected function __construct() {} 
    final private function __clone() {}
    public    static function set($a) { foreach ($a as $k => $v) self::$config[$k] = $v; }
    public    static function id()    { return self::$config['id'];   }
    public    static function name()  { return self::$config['name']; }

    protected static function _capture($path) { require_once($path); }
    protected static function _flush()
    {
        // if a content section was already started
        if ( !is_null(self::$config['buffer']) ){
            $bufferid = self::$config['buffer'];
            if ( !isset(self::$content[ $bufferid ])) {
                self::$content[ $bufferid ] = '';
            }
            // append everything in the cache to its content section id
            self::$content[ $bufferid ] .= ob_get_clean();
            self::$config['buffer'] = NULL;
        }
    }

    public static function append($id = 'default')
    {
        if ( get_called_class() == 'BODY' ){
            if ( self::$config['layout_id'] == '' ) {
                self::$config['layout_id'] = $id;
            }
        }
        
        // store previous buffer
        self::_flush();

        // start this buffer
        self::$config['buffer'] = get_called_class();
        ob_start();
    }

    public static function render()
    {
        if ( get_called_class() == 'PAGE' ){
            ob_start();
                self::_capture( self::$config['body'] );
                self::_capture( self::$config['menu'] );
                require_once('Style/Parser.php');
                self::_capture( self::$config['page'] );
            self::$content[ 'PAGE' ] .= ob_get_clean();
        }
        echo (isset(self::$content[get_called_class()])) 
             ? self::$content[get_called_class()]
             : '';
    }
        
    public static function i($file)
    {
        $paths = array(
            self::$config['subcontent'] . strtolower(self::$config['id']) . '/' . $file . (preg_match('/.php$/',$file)? '' : '.php'),
            self::$config['subcontent'] . $file . (preg_match('/.php$/',$file)? '' : '.php'),
            self::$config['root'] . $file . (preg_match('/.php$/',$file)? '' : '.php')
        );
        foreach ($paths as $p) if ( file_exists($p) ) include_once($p);
    }
}
class TITLE extends page {}
class DESCRIPTION extends page {}
class KEYWORDS extends page {}
class STYLE extends page {}
class SCRIPT extends page {}
class MENU extends page {}
class EXTRA extends page {}
class INLINE extends page { public static function append($id = 'default') { return; } }
class BODY extends page {}
class FOOTER extends page {}
class COMMENT extends page {}
class LAYOUT extends page {
    public static function def($id = 'default') { ob_start(); }
    public static function end($id = 'default')  {

        // first layout specified is the default
        if ( self::$config['layout_default'] == '' ) {
            self::$config['layout_default'] = $id;
        }

        if ((
                // set when BODY::append('layout_id') was called
                self::$config['layout_id'] == $id
            )
            ||
            (
                (self::$config['layout_id'] == 'default') &&
                ($id == self::$config['layout_default'])
            ))
        {
            echo ob_get_clean();
            //self::_flush();
        } else {
            ob_end_clean();
        }
    }
}
