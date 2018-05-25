<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 23.03.2018
 * Time: 17:34
 */

class Work {

    public static function getSentBindsCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.request_id AS req_id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=b.users_id
              WHERE R.client_id=:userId AND b.state=0", array(':userId'=>$userId));
    }

    public static function getOpenSentCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user, b.state, b.smart_contract AS address FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=b.users_id
              WHERE R.client_id=:userId AND (b.state=2 OR b.state=3)", array(':userId'=>$userId));
    }

    public static function getClosedSentCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, f.files_id AS file_id, R.title AS label, b.corrected AS received, 
              b.eth_demand AS eth, u.username AS user, b.review, b.review_text, b.state FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=b.users_id
              JOIN files AS f ON f.files_id=R.corrected_file_id
              WHERE R.client_id=:userId AND b.state>3", array(':userId'=>$userId));
    }

    public static function getMyBinds($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=R.client_id
              WHERE b.users_id=:userId AND b.state=0", array(':userId'=>$userId));
    }

    public static function getOpenMyCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, R.file_id AS file_id, b.request_id AS request_id, R.title AS label, R.deadline AS deadline, b.eth_demand AS eth,
              u.username AS user, b.state, b.smart_contract AS address FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=R.client_id
              WHERE b.users_id=:userId AND (b.state=2 OR b.state=3)", array(':userId'=>$userId));
    }

    public static function getClosedMyCorrections($userId) {
        return Db::getAll("SELECT b.binds_id AS id, f.files_id AS file_id, R.title AS label, b.corrected AS received, b.eth_demand AS eth,
              u.username AS user, b.review, b.review_text FROM binds AS b
              JOIN request AS R ON R.request_id=b.request_id
              JOIN users AS u ON u.users_id=R.client_id
              JOIN files AS f ON f.files_id=R.file_id
              WHERE b.users_id=:userId AND b.state>3", array(':userId'=>$userId));
    }

//    public static function createDemand($userId, $label, $pages, $diff, $deadline, $mess, $file, $hash) {
//        Db::insert("request", array('client_id' => $userId, 'file' => $file, 'hash' => $hash, 'corrected_file' => "", 'created' => date("d.m.y"), 'deadline' => $deadline,
//            'title' => $label, 'diff' => $diff, 'pages' => $pages, 'description' => $mess));
//    }

    public static function createDemand($userId, $label, $deadline, $mess, $fileId) {
        Db::insert("request", array('client_id' => $userId, 'file_id' => $fileId, 'corrected_file_id'=>$fileId, 'created' => date("y-m-d"), 'deadline' => $deadline,
            'title' => $label, 'description' => $mess));
    }

    public static function createBind($requestId, $correctorId, $eth) {
        Db::insert("binds", array('request_id'=>$requestId, 'users_id'=>$correctorId, 'eth_demand'=>$eth,
            'created'=>date("y-m-d")));
    }

    public static function createCorrection($requestId, $correctorId, $eth, $smartContAddress) {
        //TODO smart contract
        Db::insert("Corrections", array('request_id'=>$requestId, 'corrector_id'=>$correctorId, 'payment'=>$eth,
            'assigned'=>date("y-m-d"), 'smartc_address'=>$smartContAddress));
        Db::query("UPDATE binds SET accepted=0 WHERE request_id=:requestId", array(':requestId'=>$requestId));
        Db::query("UPDATE binds SET accepted=1 WHERE request_id=:requestId AND users_id=:correctorId",
            array(':requestId'=>$requestId, ':correctorId'=>$correctorId));
    }

    public static function rejectBind($bindId) {
        return Db::query("UPDATE binds SET state=1 WHERE binds_id=:bind_id", array(':bind_id'=>$bindId));
    }

    public static function cancelBind($bindId) {
        return Db::query("UPDATE binds SET state=0 WHERE binds_id=:bind_id", array(':bind_id'=>$bindId));
    }

    public static function getBindById($bindId) {
        return Db::getFirstRow("SELECT r.title AS title, u.username AS user FROM binds AS b
              JOIN request AS r ON r.request_id=b.request_id
              JOIN users AS u ON u.users_id=b.users_id
              WHERE b.binds_id=:bindId", array(':bindId'=>$bindId));
    }

    /**
     * Zamitne vsechny nabidky a prijde vybranou
     *
     * @param $bindId id vybrane nabidky
     * @param $requestId id poptavky
     * @return mixed
     */
    public static function acceptBind($bindId, $requestId) {
        Db::query("UPDATE binds SET state=1 WHERE request_id=:request_id", array('request_id'=>$requestId));
        return Db::query("UPDATE binds SET state=2 WHERE binds_id=:bind_id", array(':bind_id'=>$bindId));
    }

    public static function lockBind($bindId){
        return Db::query("UPDATE binds SET state=3 WHERE binds_id=:bind_id", array(':bind_id'=>$bindId));
    }


    public static function listWorks($userID) {
        return Db::getAll("SELECT request.request_id AS request_id, request.title AS title, f.pages AS pages,
        f.diff AS diff, request.deadline AS deadline, request.description AS description FROM request
        JOIN files AS f ON f.files_id=request.file_id
        WHERE request.request_id NOT IN (SELECT request_id FROM binds WHERE state >1)
        AND request.client_id!=:usersID AND :userID NOT IN(SELECT users_id FROM binds WHERE state<>1 AND 
        request_id=request.request_id)", array('usersID'=>$userID, ':userID'=>$userID));
    }

//    public static function cancelOrder($orderId) {
//
//    }

//    public static function uploadCorrected($filePath, $uploadID) {
//        $request = db::getAll("SELECT request_id FROM binds WHERE binds_id=:bind_id", array(':bind_id'=>$uploadID));
//        if(sizeof($request)>0) {
//            $requestID = $request[0]['request_id'];
////            echo "---------------------------------------------------" . $requestID;
////            echo "UPDATE binds SET state=4, corrected=". date("y-m-d") ." WHERE binds_id=" . $uploadID;
//            Db::query("UPDATE request SET corrected_file=:file WHERE request_id=:request_id", array(':file' => $filePath, ':request_id' => $requestID));
//            Db::query("UPDATE binds SET state=4, corrected=:corrected_date WHERE binds_id=:bind_id", array(':bind_id'=>$uploadID, ':corrected_date' => date("y-m-d")));
//        }
//    }

    public static function fulfillDemand($bind, $request_id, $fileId) {
        Db::query("UPDATE request SET corrected_file_id=:file WHERE request_id=:request_id", array(':file'=>$fileId, ':request_id'=>$request_id));
        Db::query("UPDATE binds SET state=4, corrected=:corrected_date WHERE binds_id=:bind_id", array(':corrected_date'=> date("y-m-d"), ':bind_id'=>$bind));
    }

    public static function getFilenameToCorrect($id) {
        $request = db::getAll("SELECT request_id FROM binds WHERE binds_id=:bind_id", array(':bind_id'=>$id));
        if(sizeof($request)>0) {
            $requestID = $request[0]['request_id'];
            return db::getAll("SELECT file FROM request WHERE request_id=:request_id", array(':request_id' => $requestID));
        }
    }

    public static function getFilename($id) {
        $request = db::getAll("SELECT request_id AS id FROM binds WHERE binds_id=:bind_id", array(':bind_id'=>$id));
        if(sizeof($request)>0) {
            $requestID = $request[0]['request_id'];
            return db::getAll("SELECT corrected_file_id FROM request WHERE request_id=:request_id",
                array(':request_id' => $requestID));
        }
    }

    public static function sendReview($id, $stars, $desc) {
        Db::query("UPDATE binds SET review_text=:descr, review=:stars, state=5 WHERE binds_id=:bind_id", array(':descr'=>$desc, ':bind_id'=>$id,':stars'=>$stars));
    }

    public static function checkUserID($userID) {
        $array = Db::getAll("SELECT binds_id FROM binds WHERE state!=1 AND users_id=:userID",array(':userID'=>$userID));
        return sizeof($array)>0;
    }

    public static function listReviews($user_id) {
        return Db::getAll("SELECT u.username, r. title, b.corrected, b.review, b.review_text FROM binds AS b LEFT JOIN request AS r ON b.request_id=r.request_id LEFT JOIN users AS u ON r.client_id=u.users_id WHERE b.users_id=:user_id AND review>=0", array('user_id'=>$user_id));
    }

    public static function getSentRequests($userID) {
        return Db::getAll("SELECT r.request_id AS id, f.files_id AS files_id, r.title AS title, f.pages AS pages, f.diff AS diff
              , r.deadline AS deadline, r.description, r.created FROM request AS r 
            JOIN files AS f ON f.files_id=r.file_id 
            WHERE r.request_id NOT IN (SELECT request_id FROM binds WHERE state >1) AND r.client_id=:usersID",
            array(':usersID'=>$userID));
    }

    public static function deleteRequest($id) {
//        Db::query("DELETE FROM binds WHERE request_id=:id;", array(':id'=>$id));
//        Db::query("DELETE FROM request WHERE request_id=:id", array(':id'=>$id));
        Db::query("DELETE FROM files WHERE files_id=:id", array(':id'=>$id));
    }

    public static function getContractData($id) {
        return Db::getFirstRow("SELECT u.eth_wallet_address AS wallet, r.deadline, (b.eth_demand * 1) AS wei,
              f.hash FROM binds AS b
              JOIN request AS r ON r.request_id=b.request_id
              JOIN files AS f ON f.files_id=r.file_id
              JOIN users AS u ON u.users_id=b.users_id
              WHERE b.binds_id=:id", array(':id'=>$id));
    }

    public static function setContractAddress($contract_id, $contract_add) {
        Db::query("UPDATE binds SET smart_contract=:address WHERE binds_id=:id",
            array(':address'=>$contract_add, ':id'=>$contract_id));
    }

    public static function contractFailed($contract_id) {
//        Db::query("UPDATE binds SET state=1 WHERE request_id=:request_id", array('request_id'=>$requestId));
//        return Db::query("UPDATE binds SET state=2 WHERE binds_id=:bind_id", array(':bind_id'=>$bindId));

        Db::query("UPDATE binds SET state=0
            WHERE binds_id=:bind_id", array(':bind_id'=>$contract_id));
    }
}