<?php

class CreateDemandController extends Controller {

    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'Create Demand';
        // Podnadpis strán
        $this->header['subtitle'] = 'Upload file to be corrected.';
        // Nastavení šablony
        $this->view = 'createDemand';

        $this->loggedOnly();
        $this->checkLogin();



        if ($_POST) {
            $this->processMain();
            $this->loggedOnly();

            // if form filled correctly
            if (!$this->checkPost()) {
                $this->addMessage("Filled wrong");
                return;
            }

//            $this->addMessage("Filled correctly");

            $label = $_POST['label'];
            $pages = $_POST['pages'];
            $diff = $_POST['diff'];
            $deadline = $_POST['deadline'];

            // FILE

            $fileName = $_FILES['fileUp']['name'];
            $fileType = $_FILES['fileUp']['type'];
            $fileSize = $_FILES['fileUp']['size'];
            $fileData = file_get_contents($_FILES['fileUp']['tmp_name']);
//            $fileHash = md5_file($_FILES['fileUp']);
            $fileHash = md5($_FILES['fileUp']['tmp_name']);

//            $this->addMessage(md5($_FILES['fileUp']['tmp_name']));

//            $uploadDir = "./uploads/";
            if (isset($_POST['description']) && $_POST['description']!="")
                $desc = $_POST['description'];
            else
                $desc = "No description";

//            if (!$this->isDemandNew($label, $pages, $diff, $deadline)) {
//                $this->addMessage("I'm old");
//                return;
//            }

                $_SESSION['lastPost']['label'] = $label;
                $_SESSION['lastPost']['pages'] = $pages;
                $_SESSION['lastPost']['diff'] = $diff;
                $_SESSION['lastPost']['deadline'] = $deadline;
                $_SESSION['lastPost']['desc'] = $desc;

                // save to the database
            $userId = $_SESSION['user_id'];
            $fileId = File::insertFile($fileName, $fileType, $fileSize, $fileData, $diff, $pages, $fileHash);
//                var_dump($fileId);

            if ($fileId != -1)
                Work::createDemand($userId, $label, $deadline, $desc, $fileId);
        }
    }

    function checkPost() {
        $isOk = true;

        if (!isset($_POST['label']) || !$_POST['label']) {
            $this->addMessage("Label");
            $isOk = false;
        }

        if (!isset($_POST['pages']) || !$_POST['pages']) {
            $this->addMessage("Pages");
            $isOk = false;
        }

        if (!isset($_POST['deadline']) || !$_POST['deadline']) {
            $this->addMessage("Deadline");
            $isOk = false;
        }

        if (!isset($_POST['diff']) || !$_POST['diff']) {
            $this->addMessage("Diff");
            $isOk = false;
        }

        if ($_FILES['fileUp']['size'] == 0) {
            $this->addMessage("Zero byte file");
            $isOk = false;
        } elseif ($_FILES['fileUp']['size'] > 16777216) {
            $this->addMessage("Too big file (max. 16MB)");
            $isOk = false;
        }

        return $isOk;
    }

    function isDemandNew($label, $pages, $diff, $deadline) {
        if (!isset($_SESSION['lastPost'])) {
            return true;
        }
        if ($_SESSION['lastPost']['label'] == $label &&
            $_SESSION['lastPost']['pages'] == $pages &&
            $_SESSION['lastPost']['diff'] = $diff &&
                $_SESSION['lastPost']['deadline'] = $deadline) {
            return false;
        }

        return true;
    }

    function clearController() {

    }
}