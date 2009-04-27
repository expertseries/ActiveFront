<? 
// 
// class CONFIG
// {
//     private static $o = array();
// 
//     public static function g($k)     { return self::$o[$k]; }
//     public static function get($k)   { return self::$o[$k]; }
// 
//     public static function s($k,$v)  { self::$o[$k] = $v;   }
//     public static function set($k,$v){ self::$o[$k] = $v;   }
// }
// 
// 
// # .../path/to/web/public
// CONFIG::set('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']);
// 
// # .../a/styles/active.css?recache
// CONFIG::set('REQUEST_URI', $_SERVER['REQUEST_URI']);
// 
// // www.site.com
// CONFIG::set('HTTP_HOST', $_SERVER['HTTP_HOST']);
// 
// # .../path/to/web/public/a/styles/active.css?recache
// CONFIG::set('DOCUMENT', preg_replace("/\?.*$/", '',
//                       CONFIG::get('DOCUMENT_ROOT')
//                     . CONFIG::get('REQUEST_URI')));
// 
// # .../path/to/web/public/a/style
// CONFIG::set('DOCUMENT_PATH', substr(  CONFIG::get('DOCUMENT'),
//                                     0,
//                                     strrpos(CONFIG::get('DOCUMENT'),'\\')
//                            ));
// 
// # .../path/to/web/public/_
// 
// CONFIG::set('ACTIVEFRONT_ROOT', realpath( dirname( __FILE__ ) ));



require_once('Zend/Registry.php');
class CONFIG extends Zend_Registry {}

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

require_once(CONFIG::get('ACTIVEFRONT_ROOT') . '/Init.php');

