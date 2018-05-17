<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 22.03.2018
 * Time: 19:03
 */

class MyOffersController extends Controller {

    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'My Contracts';
        // Podnadpis strán
        $this->header['subtitle'] = 'Manage your contracts.';
        // Nastavení šablony
        $this->view = 'myOffers';

        $this->loggedOnly();
        $this->checkLogin();
//        $this->addMessage($_SESSION['user_id'] . " " . $_SESSION['username']);

//        $this->addMessage("");
//        unset($_SESSION['sentBinds']);
//        unset($_SESSION['sentOpen']);
//        unset($_SESSION['sentClosed']);
//        unset($_SESSION['correctingBinds']);
//        unset($_SESSION['correctingOpen']);
//        unset($_SESSION['correctingClosed']);
//        unset($_SESSION['uploadID']);
//
        $_SESSION['sentBinds'] = array();
        $_SESSION['sentOpen'] = array();
        $_SESSION['sentClosed'] = array();
        $_SESSION['correctingBinds'] = array();
        $_SESSION['correctingOpen'] = array();
        $_SESSION['correctingClosed'] = array();
        $_SESSION['uploadID'] = "";
        $_SESSION['userReview'] = "";
        $_SESSION['fileReview'] = "";
        $_SESSION['bindReview'] = "";

        if(isset($_SESSION['user_id']) && $_SESSION['user_id']!=null){
            $userID = $_SESSION['user_id'];
    //        $userID = 1;
//            $this->addMessage($userID);
//            if(Work::checkUserID($userID)) {
                $_SESSION['sentBinds'] = Work::getSentBindsCorrections($userID);
                $_SESSION['sentOpen'] = Work::getOpenSentCorrections($userID);
                $_SESSION['sentClosed'] = Work::getClosedSentCorrections($userID);
//
//
//                //        $userID = 2;
                $_SESSION['correctingBinds'] = Work::getMyBinds($userID);
                $_SESSION['correctingOpen'] = Work::getOpenMyCorrections($userID);
                $_SESSION['correctingClosed'] = Work::getClosedMyCorrections($userID);
//            }
        }

        if ($_POST) {
            $this->processMain();
            $this->loggedOnly();

//            $this->addMessage(print_r($_SESSION['sentBinds']));
            if($_SESSION['sentBinds']!="" && $_SESSION['sentBinds']!=1) {
                foreach ($_SESSION['sentBinds'] as $bind) {
                    if (isset($_POST['rejectBind' . $bind['id']])) {
                        if (Work::rejectBind($bind['id']) == -1) {
                            $this->addMessage("Chyba");
                        }
                        $this->redirect('myOffers');
                    }
                }

                foreach ($_SESSION['sentBinds'] as $bind) {
                    if (isset($_POST['acceptBind'.$bind['id']])) {
                        if(Work::acceptBind($bind['id'])==-1){
                            $this->addMessage("Chyba");
                        }
                        $this->redirect('myOffers');
                    }
                }
            }

            if($_SESSION['correctingBinds']!="") {
                foreach ($_SESSION['correctingBinds'] as $bind) {
                    if (isset($_POST['cancelBind' . $bind['id']])) {
                        if (Work::rejectBind($bind['id']) == -1) {
                            $this->addMessage("Chyba");
                        }
                        $this->redirect('myOffers');
                    }
                }
            }

            if($_SESSION['sentOpen']!="") {
                foreach ($_SESSION['sentOpen'] as $bind) {
                    if (isset($_POST['cancelOrder' . $bind['id']])) { //pripadne TODO reg. vyrazy
                        if (Work::rejectBind($bind['id']) == -1) {
                            $this->addMessage("Chyba");
                        }
                        $this->redirect('myOffers');
                    }
                }
            }
            if($_SESSION['sentClosed']!="") {
                foreach ($_SESSION['sentClosed'] as $bind) {
                    if (isset($_POST['DownloadCorrected' . $bind['id']])) {
//                        if (basename($_POST['fileDwn']) == $_POST['fileDwn']) {
//                            $filename = $_POST['fileDwn'];
//                        } else {
//                            $filename = NULL;
//                        }

                        $req = Work::getFilename($bind['id']);
                        if(sizeof($req)>0) {
                            $path = $req[0]['corrected_file'];
                            $pathXploded = explode("/", $path);
                            $filename = $pathXploded[2];
                            if (!$filename || $filename == 0) {
                                $this->addMessage("Requested file is unavailable");
                            } else {
//                                $path = 'uploads/' . $filename;
                                if (file_exists($path) && is_readable($path)) {
                                    $size = filesize($path);
                                    header('Content-Type: application/octet-stream');
                                    header('Content-Length: ' . $size);
                                    header('Content-Disposition: attachment; filename=' . $filename);
                                    header('Content-Transfer-Encoding: binary');
                                    $file = @ fopen($path, 'rb');
                                    if ($file) {
                                        fpassthru($file);
                                        exit;
                                    } else {
                                        $this->addMessage("Can't open requested file");
                                    }
                                } else {
                                    $this->addMessage("Requested file is unaccessible");
                                }
                            }
                        }
                    } elseif(isset($_POST['SendReview' . $bind['id']])){
                        $_SESSION['userReview'] = $bind['user'];
                        $req = Work::getFilename($bind['id']);
                        $path = $req[0]['corrected_file'];
                        $pathXploded = explode("/", $path);
                        $filename = $pathXploded[2];
                        $_SESSION['fileReview'] = $filename;
                        $_SESSION['bindReview'] = $bind['id'];
                        $this->redirect("SendReview");
                    }
                }
            }
            if($_SESSION['correctingOpen']!="") {
                foreach ($_SESSION['correctingOpen'] as $bind) {
                    if (isset($_POST['cancelMyCorrection' . $bind['id']])) {
                        if (Work::rejectBind($bind['id']) == -1) {
                            $this->addMessage("Chyba");
                        }
                        $this->redirect('myOffers');
                    }
                    elseif(isset($_POST['UploadFile'. $bind['id']])) {
                        $_SESSION['uploadID'] = $bind['id'];
                        $this->redirect("fulfillDemand");
                    }
                    elseif(isset($_POST['DownloadToCorrect' . $bind['id']])) {
                        $req = Work::getFilenameToCorrect($bind['id']);
                        if(sizeof($req)>0) {
                            $path = $req[0]['file'];
                            $pathXploded = explode("/", $path);
                            $filename = $pathXploded[2];
                            if (!$filename) {
                                $this->addMessage("Requested file " . $filename . " is unavailable");
                            } else {
//                                $path = 'uploads/' . $filename;
                                if (file_exists($path) && is_readable($path)) {
                                    $size = filesize($path);
                                    header('Content-Type: application/octet-stream');
                                    header('Content-Length: ' . $size);
                                    header('Content-Disposition: attachment; filename=' . $filename);
                                    header('Content-Transfer-Encoding: binary');
                                    $file = @ fopen($path, 'rb');
                                    if ($file) {
                                        Work::lockBind($bind['id']);
                                        fpassthru($file);
                                        $this->redirect("MyOffers");
                                        exit;
                                    } else {
                                        $this->addMessage("Can't open requested file");
                                    }
                                } else {
                                    $this->addMessage("Requested file " . $path . " is unaccessible");
                                }
                            }
                        }
                    }
                }
            }

            /*foreach ($_SESSION['sentBinds']['id'] as $id) {
                if ($_POST['rejectBind'.$id]) {
                    Work::rejectBind($id);
                }
            }

            foreach ($_SESSION['sentOpen']['id'] as $id) {
                if ($_POST['cancelOrder'.$id]) {
                    Work::cancelOrder($id);
                }
            }*/
        }
    }

    function clearController() {
        unset($_SESSION['sentBinds']);
        unset($_SESSION['sentOpen']);
        unset($_SESSION['sentClosed']);
        unset($_SESSION['correctingBinds']);
        foreach ($_SESSION['correctingOpen'] as $fuckmylife){
            unset($fuckmylife);
            $this->addMessage("aaaa");
        }
        unset($_SESSION['correctingOpen']);
        $_SESSION['correctingOpen'] = "";
        unset($_SESSION['correctingClosed']);
        unset($_SESSION['uploadID']);
        unset($_SESSION['userReview']);
        unset($_SESSION['fileReview']);
        unset($_SESSION['bindReview']);
    }
}