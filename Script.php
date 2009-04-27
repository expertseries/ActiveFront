<? 

switch ( $filext ) {
    case 'js' : 
    default   : $ct = 'text/javascript';
}
header("Content-type: $ct");
readfile(CONFIG::get('DOCUMENT'));
exit(0);

/*
    
    function init() {
    	init.initAll();
    }
    init.add = funtion(fnc) {
    	if (!init.stack) {
    		init.stack = [];
    	}
    	init.stack.push(fnc);
    }
    init.initAll = function(){
    	for (var i in init.stack) {
    		init.stack[i]();
    	}
    }


    // class.TabPanel.js
    init.add(function() { @done
    	$(.stuff).live(...)
    }

*/