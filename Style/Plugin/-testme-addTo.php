<?php 

/**
 * The class name
 * @var string
 */
$plugin_class = 'AddTo';

/**
 * The plugin settings
 * @var array
 */
$settings = array();

/**
 * AddTo class
 *
 * @package csscaffold
 **/
class AddTo extends Plugins
{

    function post_process($css)
    {                
        // Get all selectors with the class attribute
        if(preg_match_all("/([^{}]*)\s*\{\s*[^}]*(add\-to\s*\:([^\;]*)\;).*?\s*\}/sx", $css, $has_class_property));
        {
            //print_r($has_class_property);exit;
            // Loop through each of the matched selectors
            foreach($has_class_property[1] as $key => $selector)
            {
                // Get its name and its properties
                $name                 = $has_class_property[1][$key];
                $property             = $has_class_property[2][$key];
                $property_value         = $has_class_property[3][$key];
                
                // Find all the selector()'s which tell us the sel to add this one to
                if(preg_match_all("/selector\(([^)]*)\)/sx", $property_value, $add_to_selectors))
                {
                    foreach($add_to_selectors[1] as $add_to_selector)
                    {
                        // Get rid of the quotes
                        $add_to_selector = preg_replace("/\'|\"/", "" ,$add_to_selector);
                        
                        // Add it to the array for this selector
                        $addto[$add_to_selector][] = trim($name);
                    }
                }
            }
        }
        
        if($addto)
        {
            $css = preg_replace("/add\-to\s*\:([^\;]*)\;/", "", $css);
            
            preg_match_all("/([^{]*?)\{([^}]*?)\}/xs", $css, $css_array);
    
            $selectors     = $css_array[1];
            $properties = $css_array[2];
                        
            foreach($selectors as $key => $selector)
            {
                $selector = explode(",",$selector);    
                
                foreach($selector as $selector_key => $selector_value)
                {
                    foreach($addto as $addto_key => $addto_value)
                    {
                        if(trim($selector_value) == $addto_key)
                        {
                            $selector[$selector_key] = implode(",", $addto_value) . "," . $selector[$selector_key];
                        }
                    }
                }
                $selector = implode(",", $selector);
                $selectors[$key] = $selector . "{" . $properties[$key] . "}";
            }
            $css = implode("", $selectors);
        }
        return $css;
    }
}
