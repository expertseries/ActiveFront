<?php 

/**
 * The class name
 * @var string
 */
$plugin_class = 'Math';

/**
 * The plugin settings
 * @var array
 */
$settings = array();


/**
 * Math class
 *
 * @package csscaffold
 **/
class Math extends CacheerPlugin
{    
    function post_process($css)
    {    
        if(preg_match_all('/math\([\"|\'](.*?)[\"|\']\)/', $css, $matches))
        {    
            foreach($matches[1] as $key => $match)
            {    
                $match = str_replace('px', '', $match);
                $match = preg_replace('/[^*|\/|\(|\)|0-9|+|-]*/sx','',$match); // Only include the simple math operators
                eval("\$result = ".$match.";");
                $css = str_replace($matches[0][$key], $result, $css);
            }
        }
        
        // If the layout plugin is being used
        // Then enable the round() function
        if(isset($this->CORE->CONFIG->Layout))
        {    
            if(preg_match_all('/round\((\d+)\)/', $css, $matches))
            {
                foreach($matches[1] as $key => $match)
                {
                    $num = round_nearest($match,$this->CORE->CONFIG->Layout['baseline']);
                    $css = str_replace($matches[0][$key],$num."px",$css);
                }
            }
        }        
        return $css;
    }
    
}

?>