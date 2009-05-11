<? 

require_once('Config.php');
require_once('Page.php');
require_once('Sitemap.php');
require_once('Utility.php');

class ActiveFront
{
    public static $p = array
    (
        'request_uri'    => '',
        'domain_name'    => '',
        'route_id'       => '',
        'source_file'    => '',
        'listing_format' => '',
        'id'             => '',
        'name'           => ''
    );

    public static function init($uri)
    {
        $root = CONFIG::get('DOCUMENT_ROOT') . '/';

        if (file_exists($root.'_constants.php')) require_once($root.'_constants.php');

        if ( isset($uri) && is_readable($root.$uri) && ($uri != '/') )
        {
            self::handleAsset($uri);
            exit();
        }

        if (file_exists($root.'_helpers.php'))  require_once($root.'_helpers.php');
        if (file_exists($root.'_footer.php'))   require_once($root.'_footer.php');
        if (file_exists($root.'_extended.php')) require_once($root.'_extended.php');
        
        $meta = self::mapRequest($uri);

        PAGE::set(array(
            'name'       => $meta['name'],
            'id'         => $meta['id'],
            'body'       => $root . $meta['file'],
            'page'       => $root . '_index.php',
            'menu'       => $root . '_menu.php',
            'subcontent' => $root . 'a/views/',
            'root'       => $root
        ));

        PAGE::render();
        
        return true;
    }

    public static function handleAsset($uri)
    {
        //$filext = end(explode('.', $filename));
        $filext = substr(strrchr($uri,'.'),1);
        $inc = '';

        if ($filext == false) {
            $filext = $uri;
        }
        switch ( $filext )
        {
            case 'js'         : require_once('Script.php'); break;
            case 'css'        : require_once('Style.php'); STYLECACHEER::init();  break;
            case '/a/scripts/': require_once('Script/Folder.php'); break;
            case '/a/styles/' : require_once('Style/Folder.php'); break;
            case '/a/scripts/': require_once('Script/Folder.php'); break;
            case '/a/styles/' : require_once('Style/Folder.php'); break;
        }
    }

    public static function mapRequest($uri)
    {
        self::$p['request_uri'] = $uri;
        self::$p['listing_format'] = 'ADVANCED';
        
        $files = self::readDirectory( CONFIG::get('DOCUMENT_ROOT') );
        $metafiles = array();

        foreach ($files as $file)
        {
            if ( $file == 'index.php' ) {
                self::$p['listing_format'] = 'BASIC';
                break;
            }
        }

        switch (self::$p['listing_format']) {

            case 'BASIC':

            	if ( preg_match("#^/?$#",self::$p['request_uri']) )
            	{
    	            self::$p['source_file'] = 'index.php';
            	}
            	else
            	{
            	    // /some/path/ (request)
            	    self::$p['source_file'] = preg_replace('#^\/|\/$#', '', self::$p['request_uri']);
            	    // some/path
            	    self::$p['source_file'] = preg_replace('#\/#', '.', self::$p['source_file']);
            	    // some.path
            	    self::$p['source_file'] .= '.php';
            	    // some.path.php (filename)
            	    if ( !file_exists(CONFIG::get('DOCUMENT_ROOT') . '/' . self::$p['source_file']) ) {
            	        self::$p['source_file'] = '_404.php';
            	    }
            	}
        	    // Create Page Name and ID
            	self::$p['id'] = self::$p['source_file'];
            	// _page.not.found.php
            	self::$p['id'] = preg_replace('/^_/','',self::$p['id']);
            	// page.not.found.php
            	self::$p['id'] = preg_replace('/[\-\.](php)?/',' ',self::$p['id']);
            	// page not found
            	self::$p['name'] = ucwords(self::$p['id']);
            	// Page Not Found (name)
            	self::$p['id'] = preg_replace('/ /','',self::$p['name']);
            	// PageNotFound (id)

            	return array('file'=>self::$p['source_file'],
            	             'name'=>self::$p['name'],
            	             'id'  =>self::$p['id']);
            	break;
            
            case 'ADVANCED':

                $req = array(
                    'host' => CONFIG::get('HTTP_HOST'),
                    'uri' => join(',',explode('/',substr($uri, 1, -1)))
                );

                $tgt = array();

                //trim any leading subdomain info from the request
                $req['host'] = preg_replace('/.*?([^.]+\.\w+\.\w+)$/','\\1',$req['host']);

                foreach ($files as $file)
                {
                    $a = explode(',', $file, 3);
                    $mf = array(
                        'file'  => $file,
                        'host'  => $a[0],
                        'route' => $a[1],
                        'uri'   => substr($a[2],0,strpos($a[2],'.php'))
                    );

                    if ( $mf['host'] == $req['host'] ) {
                        if (($mf['uri'] == $req['uri'])
                            || (($mf['route'] == 0) && ($req['uri'] == ''))
                            ){
                                
                                $tgt = $mf;
                                break;
                                break;
                            }
                    }

                }
                
//                if (!isset($tgt['file'])) {
//                    $tgt['file'] = '_404.php';
//                }
                
        	    // Create Page Name and ID
            	$tgt['id'] = isset($tgt['uri']) ? $tgt['uri'] : '';
            	// _page.not.found.php
            	$tgt['id'] = preg_replace('/^_/','',$tgt['id']);
            	// page.not.found.php
            	$tgt['id'] = preg_replace('/[\-\.](php)?/',' ',$tgt['id']);
            	// page not found
            	$tgt['name'] = ucwords($tgt['id']);
            	// Page Not Found (name)
            	$tgt['id'] = preg_replace('/ /','',$tgt['name']);
            	// PageNotFound (id)
        	
            	
            	return $tgt;
            	break;                
        }
    }

    public static function is($var) 
    { 
        return UTILITY::is($var);
    }    

    protected static function readDirectory($path) {
        return UTILITY::readDirectory($path);
    }

}

