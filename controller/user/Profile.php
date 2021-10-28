<?php

    class Profile extends Database{

        public function __construct(){
            $user = new User();
            var_dump($user);
        }
    }