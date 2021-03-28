<?php
$ds = DIRECTORY_SEPARATOR;
$css = $_SERVER["DOCUMENT_ROOT"].$ds."tatutech".$ds."public".$ds."css";

/*This config file will be updated numerously drawing important information such as 
#database configurations
#external urls
#commonly utilized paths

*/
$config = array(
    //Commonly utilized paths
    "paths" => array(
        "css" => $_SERVER["DOCUMENT_ROOT"].$ds."tatutech".$ds."public".$ds."css".$ds,
        "js" => $_SERVER["DOCUMENT_ROOT"].$ds."tatutech".$ds."public".$ds."js".$ds
    )
)
?>