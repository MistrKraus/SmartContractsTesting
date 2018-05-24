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
//
        $userID = $_SESSION['user_id'];

        $_SESSION['sentRequests'] = Work::getSentRequests($userID);
        $_SESSION['sentBinds'] = Work::getSentBindsCorrections($userID);
        $_SESSION['sentOpen'] = Work::getOpenSentCorrections($userID);
        $_SESSION['sentClosed'] = Work::getClosedSentCorrections($userID);

        $_SESSION['correctingBinds'] = Work::getMyBinds($userID);
        $_SESSION['correctingOpen'] = Work::getOpenMyCorrections($userID);
        $_SESSION['correctingClosed'] = Work::getClosedMyCorrections($userID);

        $_SESSION['uploadID'] = "";
        $_SESSION['userReview'] = "";
        $_SESSION['fileReview'] = "";
        $_SESSION['bindReview'] = "";

        if ($_POST) {
            $this->processMain();
            $this->loggedOnly();

//            $this->addMessage(print_r($_SESSION['sentBinds']));
            // ruseni poptavky
            if($_SESSION['sentRequests']!="" && $_SESSION['sentRequests']!=1) {
//                $this->addMessage($_POST['cancelOrderSR2']['id']);
                foreach ($_SESSION['sentRequests'] as $bind) {
                    if (isset($_POST['cancelOrderSR' . $bind['id']])) {
                        if (Work::deleteRequest($bind['id']) == -1) {
                            $this->addMessage("Chyba");
                        }
                        $this->redirect('myOffers');
                    }
                }
            }

            if($_SESSION['sentBinds']!="" && $_SESSION['sentBinds']!=1) {
                // odmitani nabidky
                foreach ($_SESSION['sentBinds'] as $bind) {
                    if (isset($_POST['rejectBind' . $bind['id']])) {
                        if (Work::rejectBind($bind['id']) == -1) {
                            $this->addMessage("Chyba");
                        }
                        $this->redirect('myOffers');
                    // prijimani nabidky
                    } elseif (isset($_POST['acceptBind'.$bind['id']])) {
                        if(Work::acceptBind($bind['id'], $_SESSION['sentBinds']['req_id'])==-1){
                            $this->addMessage("Chyba");
                        }
                        $this->redirect('myOffers');
                    }
                }

//                // prijimani nabidky
//                foreach ($_SESSION['sentBinds'] as $bind) {
//                    if (isset($_POST['acceptBind'.$bind['id']])) {
//                        if(Work::acceptBind($bind['id'], $_SESSION['sentBinds']['req_id'])==-1){
//                            $this->addMessage("Chyba");
//                        }
//
//                        $this->redirect('myOffers');
//                    }
//                }
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
                        $this->downloadFile($_SESSION['sentClosed']['file_id']);

//                        if (basename($_POST['fileDwn']) == $_POST['fileDwn']) {
//                            $filename = $_POST['fileDwn'];
//                        } else {
//                            $filename = NULL;
//                        }

//                        $req = Work::getFilename($bind['id']);
//                        if(sizeof($req)>0) {
//                            $path = $req[0]['corrected_file'];
//                            $pathXploded = explode("/", $path);
//                            $filename = $pathXploded[2];
//                            if (!$filename || $filename == 0) {
//                                $this->addMessage("Requested file is unavailable");
//                            } else {
////                                $path = 'uploads/' . $filename;
//                                if (file_exists($path) && is_readable($path)) {
//                                    $size = filesize($path);
//                                    header('Content-Type: application/octet-stream');
//                                    header('Content-Length: ' . $size);
//                                    header('Content-Disposition: attachment; filename=' . $filename);
//                                    header('Content-Transfer-Encoding: binary');
//                                    $file = @ fopen($path, 'rb');
//                                    if ($file) {
//                                        fpassthru($file);
//                                        exit;
//                                    } else {
//                                        $this->addMessage("Can't open requested file");
//                                    }
//                                } else {
//                                    $this->addMessage("Requested file is unaccessible");
//                                }
//                            }
//                        }
                    } elseif(isset($_POST['SendReview' . $bind['id']])){
//                        $_SESSION['userReview'] = $bind['user'];
//                        $req = Work::getFilename($bind['id']);
//                        $path = $req[0]['corrected_file'];
//                        $pathXploded = explode("/", $path);
//                        $filename = $pathXploded[2];
//                        $_SESSION['fileReview'] = $filename;

                        $_SESSION['bindReviewId'] = $bind['id'];
                        $this->redirect("SendReview");
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

            if($_SESSION['correctingOpen']!="") {
                foreach ($_SESSION['correctingOpen'] as $bind) {
                    if (isset($_POST['cancelMyCorrection' . $bind['id']])) {
                        if (Work::rejectBind($bind['id']) == -1) {
                            $this->addMessage("Chyba");
                        }
                    } elseif (isset($_POST['UploadFile' . $bind['id']]) && isset($_FILES['fileUp'])) {
                        $id = $bind['id'];
                        $fileName = $_FILES['fileUp'.$id]['name'];
                        $fileType = $_FILES['fileUp'.$id]['type'];
                        $fileSize = $_FILES['fileUp'.$id]['size'];
                        $fileData = file_get_contents($_FILES['fileUp'.$id]['tmp_name']);
                        $fileHash = md5($_FILES['fileUp'.$id]['tmp_name']);

                        $fileId = File::insertCorrectedFile($fileName, $fileType, $fileSize, $fileData, $fileHash);

                        Work::fulfillDemand($bind['id'], $bind['request_id'], $fileId);
//                        $this->redirect("fulfillDemand");
                    }
                }
            }

            if ($_SESSION['correctingClosed']!=0) {
                foreach ($_SESSION['correctingClosed'] as $bind) {
                    if(isset($_POST['DownloadToCorrect' . $bind['id']])) {
                        $this->downloadFile($bind['file_id']);
//                        $req = Work::getFilenameToCorrect($bind['id']);
//                        if(sizeof($req)>0) {
//                            $path = $req[0]['file'];
//                            $pathXploded = explode("/", $path);
//                            $filename = $pathXploded[2];
//                            if (!$filename) {
//                                $this->addMessage("Requested file " . $filename . " is unavailable");
//                            } else {
////                                $path = 'uploads/' . $filename;
//                                if (file_exists($path) && is_readable($path)) {
//                                    $size = filesize($path);
//                                    header('Content-Type: application/octet-stream');
//                                    header('Content-Length: ' . $size);
//                                    header('Content-Disposition: attachment; filename=' . $filename);
//                                    header('Content-Transfer-Encoding: binary');
//                                    $file = @ fopen($path, 'rb');
//                                    if ($file) {
//                                        Work::lockBind($bind['id']);
//                                        fpassthru($file);
//                                        $this->redirect("MyOffers");
//                                        exit;
//                                    } else {
//                                        $this->addMessage("Can't open requested file");
//                                    }
//                                } else {
//                                    $this->addMessage("Requested file " . $path . " is unaccessible");
//                                }
//                            }
//                        }
                    }
                }
            }
        }
    }

    function downloadFile($id) {
        $file = File::getFile($id);

        if (empty($file['name']) && $file['size'] <= 0) {
            return;
        }

//        base64_decode($file['data']);
//
//        header('Content-Type: ' . $file['type']);
//        header("Content-length:" . $file['size']);
//        header('Content-Disposition: attachment; filename=' . $file['name'] . '');
//        header('Content-Transfer-Encoding: binary');
//
//        echo $file['data'];
//        ob_clean();
//        flush();

        base64_decode($file['data']);

        header('Content-Type: ' . $file['type']);
        header("Content-length:" . $file['size']);
        header('Content-Disposition: attachment; filename=' . $file['name'] . '');
        header('Content-Transfer-Encoding: binary');

        echo $file['data'];
        ob_clean();
        flush();
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