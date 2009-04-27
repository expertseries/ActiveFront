<?php

$plugin_class = 'ConstantsPlugin';
class ConstantsPlugin extends CacheerPlugin
{
    function pre_process($css)
    {
        $constants = array();
        if (preg_match_all('#@constants\s*\{\s*([^\}]+)\s*\}\s*#i', $css, $matches))
        {
            foreach ($matches[0] as $i => $constant)
            {
                $css = str_replace($constant, '', $css);
                preg_match_all('#([_a-z0-9]+)\s*:\s*([^;]+);#i', $matches[1][$i], $vars);
                foreach ($vars[1] as $var => $name)
                {
                    if ( preg_match('/([A-Z_]+)/',$name) ) {
                        $constants["$name"] = $vars[2][$var];
                    } elseif ( preg_match('/\(|\)/',$name) ) {
                        $name = preg_replace('/.*\((.+)\).*/','$1',$name);
                        $constants["$name"] = $vars[2][$var];
                    } else {
                        $constants["const($name)"] = $vars[2][$var];
                    }
                }
            }
        }

        if (!empty($constants))
        {
            $css = str_replace(array_keys($constants), array_values($constants), $css);
        }
        
        return $css;
    }
}