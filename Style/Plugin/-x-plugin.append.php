<?php 

/**
 * The class name
 * @var string
 */
$plugin_class = 'Append';

/**
 * The settings for this plugin
 * @var string
 */
$settings = array(
    'path' => 'plugins'
);

/**
 * Append class
 *
 * @package csscaffold
 **/
class Append extends CacheerPlugin
{
    function pre_process($css)
    {    
        foreach(read_dir($this->CORE->CONFIG->Append['path']) as $file)
        {
            // Check for css files and files beginning with - or . 
            if (!check_prefix($file) || !check_type($file, array('css')))
            { 
                continue; 
            }
            
            // Add it to our css
            $css .= load($file);
        }
        
        return $css;
    }
} // END Append

?>