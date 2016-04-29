<?php
    function swap($a,$b){
        print nl2br("Before swapping\n");
        print nl2br("a = $a and b = $b \n\n\n");
        $a = $a + $b; // 30 = 10 + 20
        $b = $a - $b;
        $a = $a - $b;
        print nl2br("After swapping\n");
        print nl2br("a = $a and b = $b \n");
    }
    swap(10,20);
   
?>
