<?php




//echo $_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'];

$plugin_class = 'ServerImportPlugin';
class ServerImportPlugin extends CacheerPlugin
{
    function pre_process($css)
    {
        global $relative_file, $relative_dir;
        
        
        $imported    = array($relative_file);
        $context    = $relative_dir;
        while (preg_match_all('#@server\s+import\s+url\(([^\)]+)+\);#i', $css, $matches))
        {
            foreach($matches[1] as $i => $include)
            {
                // clean off quote characters
                $include = preg_replace('/^("|\')|("|\')$/', '', $include);

                // path to the file requested
                $abs_include = CONFIG::get('DOCUMENT_ROOT') . '/a/styles/' . $include;

                // path to the file in the aj library (backup)
                //$lib_include = config::get('activejack_styles_path') . '/' . $include;
                //echo $abs_include;
                // import each file once, only import css
                if (!in_array($abs_include, $imported) && substr($abs_include, -3) == 'css')
                {
                    $include = '';
                    if ( file_exists($abs_include) ) {
                        $include = $abs_include;
                    }// elseif ( file_exists($lib_include) ) {
                     //   $include = $lib_include;
                    //}

                    $imported[] = $include;
                    if ($include != '')
                    {
                        $include_css = file_get_contents($include);
                        $css = str_replace($matches[0][$i], $include_css, $css);
                    }
                    else
                    {
                        $css .= "\r\nerror { -si-missing: url('{$include}'); }";
                    }
                }
                $css = str_replace($matches[0][$i], '', $css);
            }
        }
        
        return $css;
    }
}