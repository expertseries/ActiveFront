<?php
header('Content-type: text/css');


class ScriptManager {

    private static $output   = null;
    private static $filelist = array();
    private static $debug    = 1;

    public static function getFileList($path,$desired_ext) {

        $rootPath = dirname(__FILE__);
        
        
        if ( $handle = opendir($path) ) {
            
                $rel_path = preg_replace('#'.$_SERVER['DOCUMENT_ROOT'].'#e','',$path);
            
            while (false !== ($file = readdir($handle))) {
                
                $file_ext = substr($file, strrpos($file, '.') + 1);
                if (
                    ($file_ext == $desired_ext)
                     && (substr($file, 0, 1) != '.')
                     && (substr($file, 0, 1) != '-')
                    ) {
//                    self::$filelist[] = $rootPath . '/' . $path . $file;
                    $last_modified = filemtime($path . $file);
//                    self::$filelist[] = '' . $path . $file . '?' . $last_modified;
                    self::$filelist[] =  $rel_path . $file . '?recache=1';

                }
            }
            closedir($handle);
        }
    }

    public static function includeFiles() {
    
        if (self::$debug == 1) {
            foreach (self::$filelist as $file) {
                 self::$output .= "@import url(\"$file\");\n";
            }
        
        }
        return self::$output;
    
    }


/*
    
    function includeFiles($path,$ext) {
    
        $rootPath = dirname(__FILE__);
    
        if ( $handle = opendir($path) ) {
            while (false !== ($file = readdir($handle))) {
                
                $fext = substr($file, strrpos($file, '.') + 1);
                if ($fext == $ext) {
                    include_once($rootPath . '/' . $path . $file);
                }
            }
        closedir($handle);
        }
        
    }


    function includeFiles() {
    
        ob_start();
        require_once($filename);
        $return_str = ob_get_contents();
        ob_end_clean();
    
    
    }
    
*/


}



function compress($buffer) {
    // remove comments
//    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    // remove tabs, spaces, newlines, etc.
//    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    return $buffer;
}



function includeFiles($path,$ext) {

    $rootPath = dirname(__FILE__);

    if ( $handle = opendir($path) ) {
        while (false !== ($file = readdir($handle))) {
            
            $fext = substr($file, strrpos($file, '.') + 1);
            if ($fext == $ext) {
                include_once($rootPath . '/' . $path . $file);
            }
        }
    closedir($handle);
    }
    
}

/*
ob_start("compress");

includeFiles('scripts/','js');

ob_end_flush();
*/


ob_start("compress");

ScriptManager::getFileList(CONFIG::get('DOCUMENT'),'css');
echo ScriptManager::includeFiles();

ob_end_flush();


