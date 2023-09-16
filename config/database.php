<?php

    function conectarDB(): mysqli {
        $db = mysqli_connect('localhost','root','091159Eliza*','pqrs230831');
        if(!$db){
            echo ">Error: No se pudo conectar";
            exit;
        }
        return $db;
    }

?>