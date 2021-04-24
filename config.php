<?php
    /* Database credentials.  */
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_NAME', 'ims');
          
    if (isset($_SERVER['HTTP_HOST']))
    {
        $base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
        $base_url .= '://'. $_SERVER['HTTP_HOST'];
        //$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
    }    
    else
    {
        $base_url = 'http://localhost';
    } 
    date_default_timezone_set("Europe/Berlin");
     // define global constants   
     define("BASE_URL", $base_url.'/ims/' ); //defines the base url to start from in the server   
   // define("BASE_URL", "http://localhost/ims/"); //defines the base url to start from in the server 
    define("LOGIN_URL", BASE_URL."index.php"); //defines the base url to start from in the server  
    define("DASHBOARD_URL", BASE_URL."dashboard.php");
    define("SERVER_URL", BASE_URL."server/");    
    define("ASSETS_URL", BASE_URL."");
    define("CSS_URL", ASSETS_URL."css/");
    define("JS_URL", ASSETS_URL."js/");
    define("IMAGES_URL", ASSETS_URL."userimages/");   
    define("FONTS_URL", ASSETS_URL."fonts/");  

    define ("ROOT_PATH", realpath(dirname(__FILE__))); //starts the path from where the config.php file is located
    define ("BASE_PATH",ROOT_PATH."/");
    define("ERROR_LOG",BASE_PATH."errorlog/error.log");    
    define("INC", BASE_PATH."inc/"); 
    define("CLASS_DIR", BASE_PATH."class/"); 
    define("SERVER", BASE_PATH."server/"); 
    define("ASSETS_DIR", BASE_PATH."");
    define("CSS_DIR", ASSETS_DIR."css/");
    define("JS_DIR", ASSETS_DIR."jscripts/");
    define("IMAGES_DIR", ASSETS_DIR."userimages/");
    define("FONTS_DIR", ASSETS_DIR."fonts/");	

    define("ALLOWED_IMAGES", array("jpg", "jpeg", "png"));    

    define("SITE_NAME", "IMS");
   
    $page = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);


?>