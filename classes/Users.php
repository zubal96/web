<?php

//id=5944364
//protected_key=4vuYXcrRX1ZqxUvlplxK
//service_key_access=5a5028e75a5028e75aec3a2e685a0a9ccb55a5
class Users {

    var $id;
    var $first_name;
    var $last_name;
    var $sex = 0;
    var $bdate;
    var $country = [
    "id" => 0,
    "title" => ""];
    var $city = [
    "id" => "",
    "title" => ""];
    var $mobile_phone = "";
    var $home_phone = "";
    var $home_town = '';
    var $online = 0;
    var $relation = 0;
    var $relatives = array();
    var $groups = array();
    var $photo_200='';
    var $photo_50='';
    
    static $rel = array(
        0 => 'не указано',
        1 => 'не женат/не замужем',
        2 => 'есть друг/есть подруга',
        3 => 'помолвлен/помолвлена',
        4 => 'женат/замужем',
        5 => 'всё сложно',
        6 => 'в активном поиске',
        7 => 'влюблён/влюблена'
        );

    function __construct(/* $response */) {
    }

}

