<?php 

$plugin_class = 'CSS3HelperPlugin';

$settings = array();


class CSS3HelperPlugin extends CacheerPlugin
{
    function post_process($css)
    {
        
        $css = $this->borderRadius($css);
        $css = $this->boxShadow($css);
        //$css = $this->fontFace($css);
        
        return $css;
    }

    function boxShadow($css)
    {
        if(preg_match_all('/[^\-]box\-shadow\:(.*?)\;/', $css, $matches))
        {
            foreach($matches[0] as $key => $match)
            {                
                $s = $match;
                $s .= "-moz-box-shadow:".$matches[1][$key].";";
                $s .= "-webkit-box-shadow:".$matches[1][$key].";";
                
                // Remove the border-radius:xpx;
                $css = str_replace($match,$s,$css);
            }
        }
        return $css;
    }

    
    function borderRadius($css)
    {
        if(preg_match_all('/[^\-]border\-radius\:(.*?)\;/', $css, $matches))
        {
            foreach($matches[0] as $key => $match)
            {                
                $s = $match;
                $s .= "-moz-border-radius:".$matches[1][$key].";";
                $s .= "-webkit-border-radius:".$matches[1][$key].";";
                
                // Remove the border-radius:xpx;
                $css = str_replace($match,$s,$css);
            }
        }
        
        if(preg_match_all('/[^\-]border\-top\-left\-radius\:(.*?)\;/', $css, $matches))
        {
            foreach($matches[0] as $key => $match)
            {                
                $s = $match;
                $s .= "-moz-border-radius-topleft:".$matches[1][$key].";";
                $s .= "-webkit-border-top-left-radius:".$matches[1][$key].";";
                
                // Remove the border-radius:xpx;
                $css = str_replace($match,$s,$css);
            }
        }
        
        if(preg_match_all('/[^\-]border\-top\-right\-radius\:(.*?)\;/', $css, $matches))
        {
            foreach($matches[0] as $key => $match)
            {                
                $s = $match;
                $s .= "-moz-border-radius-topright:".$matches[1][$key].";";
                $s .= "-webkit-border-top-right-radius:".$matches[1][$key].";";
                
                // Remove the border-radius:xpx;
                $css = str_replace($match,$s,$css);
            }
        }
        
        if(preg_match_all('/[^\-]border\-bottom\-left\-radius\:(.*?)\;/', $css, $matches))
        {
            foreach($matches[0] as $key => $match)
            {                
                $s = $match;
                $s .= "-moz-border-radius-bottomleft:".$matches[1][$key].";";
                $s .= "-webkit-border-bottom-left-radius:".$matches[1][$key].";";
                
                // Remove the border-radius:xpx;
                $css = str_replace($match,$s,$css);
            }
        }
        
        if(preg_match_all('/[^\-]border\-bottom\-right\-radius\:(.*?)\;/', $css, $matches))
        {
            foreach($matches[0] as $key => $match)
            {            
                $s = $match;
                $s .= "-moz-border-radius-bottomright:".$matches[1][$key].";";
                $s .= "-webkit-border-bottom-right-radius:".$matches[1][$key].";";
                
                // Remove the border-radius:xpx;
                $css = str_replace($match,$s,$css);
            }
        }

        return $css;
    }
    
    /**
     * getOpacity
     *
     * Finds all opacity properties, and converts them to filters for IE6/7
     *
     * @return $ie_string
     **/
    function getOpacity($css)
    {
        if(preg_match_all('/([\s.#a-z,-]*)\s*\{[^}]*opacity\:\s*(\d\.\d).*?\}/sx', $css, $matches))
        {
            foreach($matches[0] as $key => $match)
            {
                $selectors            = $matches[1][$key];
                $opacity_value     = $matches[2][$key];
                
                // Convert it for the filter 
                $opacity_value = $opacity_value * 100;
                
                // Get rid of excess whitespace
                $selectors = trim($selectors);
                
                $ie_string .= $selectors . "{filter:alpha(opacity='".$opacity_value."'); zoom:1;}";    
            }
        }        
        
        return $ie_string;
    }
    
    /**
     * fontFace
     *
     * Finds all fonts in the fonts folder, and creates @font-face properties
     *
     * @return $css
     **/
     /*
    function fontFace($css)
    {
    
        // Load up all the fonts into an array
        $fonts = read_dir(ASSETPATH . "/fonts");

        if($fonts != "")
        {
            // Loop through each of them
            foreach($fonts as $name => $path)
            {
                $ext = substr($path, -3, 3);
                $name = str_replace(".".$ext,'',$name);
    
                // Make sure its a font file
                if( $ext == 'otf' || $ext == 'ttf' || $ext == 'eot' )
                {     
                    // Add them as @font-face rules            
                    $css .= "@font-face { name:'".$name."';src:url('".$path."'); }";                                
                }
            }
        }

        return $css;
    }*/
    
}

