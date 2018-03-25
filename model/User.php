<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 12.12.2017
 * Time: 21:43
 */

class User {

    // zapíše nového uživatele do databáze
    public static function registerUser($userName, $passWord) {
        Db::insert("user", array('nickname'=>$userName, 'password'=>$passWord, 'position_id'=>2));
    }

    // vrátí data o uživateli podle jména
    public static function logIn($userName) {
        return Db::getFirstRow("SELECT * FROM user WHERE nickname=:nickname", array(':nickname'=>$userName));
    }

    // vrátí heslo uživatele
    public static function getUserPassword($userName) {
        return Db::getFirstRow("SELECT password FROM user WHERE nickname=:nickname", array(':nickname'=>$userName));
    }

    // vrátí data o uživateli podle id
    public static function getUser($user_id) {
        return Db::getAll("SELECT * FROM user WHERE id_user=:id", array(':id'=>$user_id));
    }
}