<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 12.12.2017
 * Time: 21:43
 */

class User {

    // zapíše nového uživatele do databáze
    public static function registerUser($userName, $email, $metaMaskId) {
        Db::insert("users", array('username'=>$userName, 'email'=>$email, 'eth_wallet_address'=>$metaMaskId));
    }

    // vrátí data o uživateli podle jména
    public static function logIn($metamask) {
        return Db::getFirstRow("SELECT * FROM users WHERE eth_wallet_address=:eth_wallet_address", array(':eth_wallet_address'=>$metamask));
    }

    // vrátí heslo uživatele
    public static function getUserPassword($userName) {
        return Db::getFirstRow("SELECT password FROM users WHERE nickname=:nickname", array(':nickname'=>$userName));
    }

    // vrátí data o uživateli podle id
    public static function getUser($user_id) {
        return Db::getAll("SELECT * FROM users WHERE users_id=:users_id", array(':users_id'=>$user_id));
    }

    public static function setAsCorrector($user_id, $corrector) {
        return Db::update("Users", array('role'=>$corrector), "WHERE users_id=:user_id", array('users_id'=>$user_id));
    }
}