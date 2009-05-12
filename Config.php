<? 

class CONFIG
{
    private static $o = array();

    public static function g($k)     { return self::$o[$k]; }
    public static function get($k)   { return self::$o[$k]; }

    public static function s($k,$v)  { self::$o[$k] = $v;   }
    public static function set($k,$v){ self::$o[$k] = $v;   }
}

$config = array(
    'DOCUMENT_ROOT'   => $_SERVER['DOCUMENT_ROOT'],
    'REQUEST_URI'     => $_SERVER['REQUEST_URI'],
    'HTTP_HOST'       => $_SERVER['HTTP_HOST'],
    'DOCUMENT'        => preg_replace("/\?.*$/", '',$_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI']),
    'ACTIVEFRONT_ROOT' => realpath( dirname( __FILE__ ) )
);
foreach ($config as $k => $v) {
    CONFIG::set($k,$v);
}
unset($config);