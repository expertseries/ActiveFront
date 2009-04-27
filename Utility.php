<? 
class UTILITY
{
    public static function is($var) 
    { 
        if (!isset($var)) return false; 
        if ($var!==false) return true; 
        return false; 
    }    

    public static function readDirectory($path) {
        if ($handle = opendir( $path )) { 
           $dir_array = array(); 
            while (false !== ($file = readdir($handle))) {
                $test = "/^a$|^[\.\_]/";
                if(!preg_match($test,$file)){ 
                    $dir_array[] = $file; 
                } 
            } 
            closedir($handle); 
            return $dir_array;
        } else {
            return array();
        }
    }

}

