<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 23.03.2018
 * Time: 17:34
 */

class Work {

    public static function getSentBindsCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user FROM Binds AS b
              JOIN Request AS R ON R.request_id=b.request_id
              JOIN Users AS u ON u.users_id=b.users_id
              WHERE R.client_id=:userId AND b.state=0", array(':userId'=>$userId));
    }

    public static function getOpenSentCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user, b.state FROM Binds AS b
              JOIN Request AS R ON R.request_id=b.request_id
              JOIN Users AS u ON u.users_id=b.users_id
              WHERE R.client_id=:userId AND (b.state=2 OR b.state=3)", array(':userId'=>$userId));
    }

    public static function getClosedSentCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, b.corrected AS received, b.eth_demand AS eth,
              u.username AS user, b.review FROM Binds AS b
              JOIN Request AS R ON R.request_id=b.request_id
              JOIN Users AS u ON u.users_id=b.users_id
              WHERE R.client_id=:userId AND b.state=4", array(':userId'=>$userId));
    }

    public static function getMyBinds($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user FROM Binds AS b
              JOIN Request AS R ON R.request_id=b.request_id
              JOIN Users AS u ON u.users_id=b.users_id
              WHERE b.users_id=:userId AND b.state=0", array(':userId'=>$userId));
    }

    public static function getOpenMyCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user, b.state FROM Binds AS b
              JOIN Request AS R ON R.request_id=b.request_id
              JOIN Users AS u ON u.users_id=b.users_id
              WHERE b.users_id=:userId AND (b.state=2 OR b.state=3)", array(':userId'=>$userId));
    }

    public static function getClosedMyCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, b.corrected AS received, b.eth_demand AS eth,
              u.username AS user, b.review, b.review_text FROM Binds AS b
              JOIN Request AS R ON R.request_id=b.request_id
              JOIN Users AS u ON u.users_id=b.users_id
              WHERE b.users_id=:userId AND b.state=4", array(':userId'=>$userId));
    }

    public static function createDemand($userId, $label, $pages, $diff, $deadline, $mess, $file) {
        Db::insert("request", array('client_id' => $userId, 'file' => $file, 'corrected_file' => "", 'created' => date("d.m.y"), 'deadline' => $deadline,
            'title' => $label, 'diff' => $diff, 'pages' => $pages, 'description' => $mess));
    }

    public static function createBind($requestId, $correctorId, $eth) {
        Db::insert("binds", array('request_id'=>$requestId, 'users_id'=>$correctorId, 'eth_demand'=>$eth,
            'created'=>date("y-m-d")));
    }

    public static function createCorrection($requestId, $correctorId, $eth, $smartContAddress) {
        //TODO smart contract
        Db::insert("Corrections", array('request_id'=>$requestId, 'corrector_id'=>$correctorId, 'payment'=>$eth,
            'assigned'=>date("d.m.y"), 'smartc_address'=>$smartContAddress));
        Db::query("UPDATE Binds SET accepted=0 WHERE request_id=:requestId", array(':requestId'=>$requestId));
        Db::query("UPDATE Binds SET accepted=1 WHERE request_id=:requestId AND users_id=:correctorId",
            array(':requestId'=>$requestId, ':correctorId'=>$correctorId));
    }

    public static function rejectBind($bindId) {
        return Db::query("UPDATE binds SET state=1 WHERE binds_id=:bind_id", array(':bind_id'=>$bindId));
    }

    public static function acceptBind($bindId) {
        return Db::query("UPDATE binds SET state=2 WHERE binds_id=:bind_id", array(':bind_id'=>$bindId));
    }

    public static function lockBind($bindId){
        return Db::query("UPDATE binds SET state=3 WHERE binds_id=:bind_id", array(':bind_id'=>$bindId));
    }


    public static function listWorks($userID) {
        return Db::getAll("SELECT request.request_id, request.title, request.pages, request.diff, request.deadline, request.description
        FROM request LEFT JOIN corrections ON request.request_id = corrections.request_id WHERE corrections.closed NOT IN (SELECT closed FROM corrections) AND :userID NOT IN(SELECT users_id FROM binds WHERE state<>1 AND request_id=request.request_id)", array(':userID'=>$userID));
    }

    public static function cancelOrder($orderId) {

    }

    public static function uploadCorrected($filePath, $uploadID) {
        $request = db::getAll("SELECT request_id FROM binds WHERE binds_id=:bind_id", array(':bind_id'=>$uploadID));
        if(sizeof($request)>0) {
            $requestID = $request[0]['request_id'];
            echo "---------------------------------------------------" . $requestID;
            echo "UPDATE binds SET state=4, corrected=". date("y-m-d") ." WHERE binds_id=" . $uploadID;
            Db::query("UPDATE request SET corrected_file=:file WHERE request_id=:request_id", array(':file' => $filePath, ':request_id' => $requestID));
            Db::query("UPDATE binds SET state=4, corrected=:corrected_date WHERE binds_id=:bind_id", array(':bind_id'=>$uploadID, ':corrected_date' => date("y-m-d")));
        }
    }

    public static function getFilenameToCorrect($id)
    {
        $request = db::getAll("SELECT request_id FROM binds WHERE binds_id=:bind_id", array(':bind_id'=>$id));
        if(sizeof($request)>0) {
            $requestID = $request[0]['request_id'];
            return db::getAll("SELECT file FROM request WHERE request_id=:request_id", array(':request_id' => $requestID));
        }
    }

    public static function getFilename($id)
    {
        $request = db::getAll("SELECT request_id FROM binds WHERE binds_id=:bind_id", array(':bind_id'=>$id));
        if(sizeof($request)>0) {
            $requestID = $request[0]['request_id'];
            return db::getAll("SELECT corrected_file FROM request WHERE request_id=:request_id", array(':request_id' => $requestID));
        }
    }

    public static function sendReview($id, $stars, $desc){
        Db::query("UPDATE binds SET review_text=:descr, review=:stars WHERE binds_id=:bind_id", array(':bind_id'=>$id,':stars'=>$stars, ':descr'=>$desc));
    }

}