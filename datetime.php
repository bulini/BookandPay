<?php
 function itdate_php_to_mysql($date)
       {
               $day = substr($date,0,2);
               $month = substr($date,3,2);
               $year = substr($date,6,4);
               return $year.'-'.$month.'-'.$day;
       }

 function itdatetime_php_to_mysql($date)
       {
               $day = substr($date,0,2);
               $month = substr($date,3,2);
               $year = substr($date,6,4);
               $time = substr($date,11,8);
               return $year.'-'.$month.'-'.$day.' '.$time;
       }

//echo date("d/m/Y H:i:s",time());
?> 

