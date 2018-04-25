<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 23.03.2018
 * Time: 17:34
 */

class Work {

    public static function getSentBindsCorrections($userId) {
        return Db::getAll("SELECT corrections_id AS id, R.title AS label, R.deadline AS deadline, payment AS eth,
              u.username AS user FROM Binds AS b
              JOIN Users AS u ON u.user_id=corrector_id
              WHERE R.client_id=:userId AND closed=0", array(':userId'=>$userId));
    }

    public static function getOpenSentCorrections($userId) {
        return Db::getAll("SELECT corrections_id AS id, R.title AS label, R.deadline AS deadline, payment AS eth,
              u.username AS user FROM Corrections AS os
              JOIN Request AS R ON R.request_id=os.request_id
              JOIN Users AS u ON u.user_id=corrector_id
              WHERE R.client_id=:userId AND closed=0", array(':userId'=>$userId));
    }

    public static function getClosedSentCorrections($userId) {
        return Db::getAll("SELECT corrections_id AS id, R.title AS label, R.deadline AS deadline, payment AS eth,
              u.username AS user FROM Corrections AS cs
              JOIN Request AS R ON R.request_id=cs.request_id
              JOIN Users AS u ON u.user_id=corrector_id
              WHERE R.client_id=:userId AND closed=1", array(':userId'=>$userId));
    }

    public static function getMyBinds($userId) {
        return Db::getAll("SELECT  Binds AS b");
    }

    public static function getOpenMyCorrections($userId) {
        return Db::getAll("SELECT corrections_id AS id, R.title AS label, R.deadline AS deadline, payment AS eth,
              u.username AS user FROM Corrections AS om
              JOIN Request AS r ON r.request_id = om.request_id
              JOIN Users AS u ON u.users_id=client_id 
              WHERE R.corrector_id=:userId AND closed=0", array(':userId'=>$userId));
    }

    public static function getClosedMyCorrections($userId) {
        return Db::getAll("SELECT corrections_id AS id, R.title AS label, R.deadline AS deadline, payment AS eth,
              u.username AS user FROM Corrections AS om
              JOIN Request AS r ON r.request_id = om.request_id
              JOIN Users AS u ON u.users_id=client_id
              WHERE R.corrector_id=:userId AND closed=1", array(':userId'=>$userId));
    }

    public static function createDemand($userId, $label, $pages, $diff, $deadline, $mess, $file) {
        Db::insert("Request", array('client'=>$userId, 'title'=>$label, 'pages'=>$pages,
            'file'=>$file, 'diff'=>$diff, 'created'=>date("d.m.y"), 'deadline'=>$deadline));
    }

    public static function createBind($requestId, $correctorId, $eth) {
        Db::insert("Binds", array('requestId'=>$requestId, 'user_id'=>$correctorId, 'eth_demand'=>$eth,
            'created'=>date("d.m.y")));
    }

    public static function createCorrection($requestId, $correctorId, $eth, $smartContAddress) {
        //TODO smart contract
        Db::insert("Corrections", array('request_id'=>$requestId, 'corrector_id'=>$correctorId, 'payment'=>$eth,
            'assigned'=>date("d.m.y"), 'smartc_address'=>$smartContAddress));
        Db::query("UPDATE * FROM Binds SET accepted=0 WHERE request_id=:requestId", array(':requestId'=>$requestId));
        Db::query("UPDATE FROM Binds SET accepted=1 WHERE request_id=:requestId AND users_id=:correctorId",
            array(':requestId'=>$requestId, ':correctorId'=>$correctorId));
    }

    public static function rejectBind($bindId) {
        Db::query("UPDATE FROM Binds SET accepted=0 WHERE binds_id=:bind_id", array(':bind_id'=>$bindId));
    }

    public static function cancelOrder($orderId) {

    }
}