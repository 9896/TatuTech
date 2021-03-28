<?php
$str = "lele porns";

echo substr($str, 0, 15)."hehe\n";
$arr = array();
$arr["fruit"] = "mango";

$ar = [1,2,3,4];
echo $ar[0]."\n";
echo $arr["fruit"]."\n";

function findEven($num){
    if($num%2 == 0){
        return $num;
    } 
}
/**
 * Note that each value from the array runs in utilizing the callback function, all that succeed the 
 * condition are returned
 */
$name = array("one" => "al","two" => "   ali","three" => "        alic");
//array_walk($name,function($value, $key){
    //if($num%2 == 0){
     //  $value = trim($value);
   // } 
//});
$trimmedValue = array();
foreach($name as $key=>$value){
    $trim = trim($value);
    $trimmedValue[$key] = $trim;
}
print_r($trimmedValue);

foreach($trimmedValue as $key=>$value){
    echo $value. ", Length:".strlen($value)."\n";
}

/**
 * filter_var is an equivalent of filter_input. Both help in filtering data
 * filter_var_array is an equivalent of filter_input_array.
 */
$filterArray = array(
    "email" => "a@gmail.com",
    "phone" => 12345,
    "name" => "   Musoba"
);

$options = array(
    "email" => FILTER_VALIDATE_EMAIL,
    "phone" => FILTER_VALIDATE_INT,
    "name" => FILTER_SANITIZE_STRING
);

$filterArrayResult = filter_var_array($filterArray, $options);
//print_r($filterArrayResult);
foreach($filterArrayResult as $key=>$value){
    if(empty($value)){
        echo "Please Enter a valid $key \n";
    }else{
     echo "$key = $value". " Value Length:". strlen(trim($value))." \n";
    }
}

$fluits = ['mango', 'apple', 'banana'];

echo $fluits[array_rand($fluits)];
?>