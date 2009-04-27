<?

    require_once('Plugin.php');
    $flags = array();
    $plugins = array();
    $plugin_path = CONFIG::get('ACTIVEFRONT_ROOT') . '/Style/Plugin';
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
    				$plugins[$plugin_class] = new $plugin_class($flags);
    				$flags = array_merge($flags, $plugins[$plugin_class]->flags);
    			}
    		}
    		closedir($dir_handle);
    	}
    }


/******************************************************************************
 Grab the modified CSS file
 ******************************************************************************/
$css = get_style_constants() . PAGE::$content['STYLE'];

// Pre-process for importers
foreach($plugins as $plugin)
{
    $css = $plugin->pre_process($css);
}

// Process for heavy lifting
foreach($plugins as $plugin)
{
    $css = $plugin->process($css);
}

// Post-process for formatters
foreach($plugins as $plugin)
{
    $css = $plugin->post_process($css);
}

/*****************************************************************************
*/
$header  = '/* Processed ';
$header .= ' (with '.str_replace('Plugin', '', preg_replace('#,([^,]+)$#', " &$1", join(', ', array_keys($plugins)))).' enabled)';
$header .= ' on '.gmdate('r').' */'."\r\n";
$css = $header.$css;

PAGE::$content['STYLE'] = $css;