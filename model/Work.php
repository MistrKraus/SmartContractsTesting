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
              u.username AS user FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=b.users_id
              WHERE R.client_id=:userId AND b.state=0", array(':userId'=>$userId));
    }

    public static function getOpenSentCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user, b.state FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=b.users_id
              WHERE R.client_id=:userId AND (b.state=2 OR b.state=3)", array(':userId'=>$userId));
    }

    public static function getClosedSentCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, b.corrected AS received, b.eth_demand AS eth,
              u.username AS user, b.review, b.review_text FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=b.users_id
              WHERE R.client_id=:userId AND b.state=4", array(':userId'=>$userId));
    }

    public static function getMyBinds($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=b.users_id
              WHERE b.users_id=:userId AND b.state=0", array(':userId'=>$userId));
    }

    public static function getOpenMyCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user, b.state FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=b.users_id
              WHERE b.users_id=:userId AND (b.state=2 OR b.state=3)", array(':userId'=>$userId));
    }

    public static function getClosedMyCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, b.corrected AS received, b.eth_demand AS eth,
              u.username AS user, b.review, b.review_text FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=b.users_id
              WHERE b.users_id=:userId AND b.state=4", array(':userId'=>$userId));
    }

    public static function createDemand($userId, $label, $pages, $diff, $deadline, $mess, $file, $hash) {
        Db::insert("request", array('client_id' => $userId, 'file' => $file, 'hash' => $hash, 'corrected_file' => "", 'created' => date("d.m.y"), 'deadline' => $deadline,
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
        Db::query("UPDATE binds SET accepted=0 WHERE request_id=:requestId", array(':requestId'=>$requestId));
        Db::query("UPDATE binds SET accepted=1 WHERE request_id=:requestId AND users_id=:correctorId",
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
        return Db::getAll("SELECT request.request_id, request.title, request.pages, request.diff, request.deadline, request.description, request.file
        FROM request WHERE request.request_id NOT IN (SELECT request_id FROM binds WHERE state >1) AND request.client_id!=:usersID AND :userID NOT IN(SELECT users_id FROM binds WHERE state<>1 AND request_id=request.request_id)", array('usersID'=>$userID, ':userID'=>$userID));
    }

    public static function cancelOrder($orderId) {

    }

    public static function uploadCorrected($filePath, $uploadID) {
        $request = db::getAll("SELECT request_id FROM binds WHERE binds_id=:bind_id", array(':bind_id'=>$uploadID));
        if(sizeof($request)>0) {
            $requestID = $request[0]['request_id'];
//            echo "---------------------------------------------------" . $requestID;
//            echo "UPDATE binds SET state=4, corrected=". date("y-m-d") ." WHERE binds_id=" . $uploadID;
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

    public static function checkUserID($userID)
    {
        $array = Db::getAll("SELECT binds_id FROM binds WHERE state!=1 AND users_id=:userID",array('userID'=>$userID));
        return sizeof($array)>0;
    }

    public static function listReviews($user_id)
    {
        return Db::getAll("SELECT u.username, r. title, b.corrected, b.review, b.review_text FROM binds AS b LEFT JOIN request AS r ON b.request_id=r.request_id LEFT JOIN users AS u ON r.client_id=u.users_id WHERE b.users_id=:user_id AND review>=0", array('user_id'=>$user_id));
    }

    public static function getSentRequests($userID)
    {
        return Db::getAll("SELECT request.request_id, request.title, request.pages, request.diff, request.deadline, request.description, request.created FROM request WHERE request.request_id NOT IN (SELECT request_id FROM binds WHERE state >1) AND request.client_id=:usersID", array('usersID'=>$userID));
    }

    public static function deleteRequest($id) {

    }

}