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

        $this->checkLogin();

        //TODO
//        $_SESSION['sentBinds'] = Work::getSentBindsCorrections();
//        $_SESSION['sentOpen'] = Work::getOpenSentCorrections();
//        $_SESSION['sentClosed'] = Work::getClosedSentCorrections();
//        $_SESSION['correctingBinds'] = Work::getMyBinds();
//        $_SESSION['correctingOpen'] = Work::getOpenMyCorrections();
//        $_SESSION['correctingClosed'] = Work::getClosedMyCorrections();

        for ($i = 0; $i < 5; $i++) {
            $_SESSION['sentBinds'][$i]['label'] = "Name";
            $_SESSION['sentBinds'][$i]['deadline'] = rand(0, 20);
            $_SESSION['sentBinds'][$i]['eth'] = rand(0, 1000) / 1000;
            $_SESSION['sentBinds'][$i]['user'] = "Name";
        }

        for ($i = 0; $i < 5; $i++) {
            $_SESSION['sentOpen'][$i]['label'] = "Name";
            $_SESSION['sentOpen'][$i]['deadline'] = rand(0, 20);
            $_SESSION['sentOpen'][$i]['eth'] = rand(0, 1000) / 1000;
            $_SESSION['sentOpen'][$i]['user'] = "Name";
        }

        for ($i = 0; $i < 5; $i++) {
            $_SESSION['sentClosed'][$i]['label'] = "Name";
            $_SESSION['sentClosed'][$i]['received'] = rand(0, 20);
            $_SESSION['sentClosed'][$i]['eth'] = rand(0, 1000) / 1000;
            $_SESSION['sentClosed'][$i]['user'] = "Name";
        }

        for ($i = 0; $i < 5; $i++) {
            $_SESSION['myBinds'][$i]['label'] = "Name";
            $_SESSION['myBinds'][$i]['deadline'] = rand(0, 20);
            $_SESSION['myBinds'][$i]['eth'] = rand(0, 1000) / 1000;
            $_SESSION['myBinds'][$i]['user'] = "Name";
        }

        for ($i = 0; $i < 5; $i++) {
            $_SESSION['correctingOpen'][$i]['label'] = "Name";
            $_SESSION['correctingOpen'][$i]['deadline'] = rand(0, 20);
            $_SESSION['correctingOpen'][$i]['eth'] = rand(0, 1000) / 1000;
            $_SESSION['correctingOpen'][$i]['user'] = "Name";
        }

        for ($i = 0; $i < 5; $i++) {
            $_SESSION['correctingClosed'][$i]['label'] = "Name";
            $_SESSION['correctingClosed'][$i]['received'] = rand(0, 20);
            $_SESSION['correctingClosed'][$i]['eth'] = rand(0, 1000) / 1000;
            $_SESSION['correctingClosed'][$i]['user'] = "Name";
        }

        if ($_POST) {
            if(isset($_POST['UploadFile'])) {
                $this->redirect("fulfillDemand");
            }
            elseif (isset($_POST['DownloadFile'])) {
                if (basename($_POST['fileDwn']) == $_POST['fileDwn']){
                    $filename = $_POST['fileDwn'];
                } else {
                    $filename = NULL;
                }

                if (!$filename) {
                    $this->addMessage("Requested file is unavailable");
                } else {
                    $path = 'uploads/'.$filename;
                    if (file_exists($path) && is_readable($path)){
                        $size = filesize($path);
                        header('Content-Type: application/octet-stream');
                        header('Content-Length: '.$size);
                        header('Content-Disposition: attachment; filename='.$filename);
                        header('Content-Transfer-Encoding: binary');
                        $file = @ fopen($path, 'rb');
                        if ($file){
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
    }
}