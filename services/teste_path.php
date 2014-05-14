<?php
   echo getcwd() . "\n\n";
   if(file_exists('../../calendario/services/nosso_numero.count'))  {
             echo ' - achei!' ;
        }      else {
            echo ' - não achei!' ;
        }
?>
