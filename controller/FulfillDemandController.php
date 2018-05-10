<?php

class FulfillDemandController extends Controller {

    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'Fulfill Demand';
        // Podnadpis strán
        $this->header['subtitle'] = 'Upload corrected file.';
        // Nastavení šablony
        $this->view = 'fulfillDemand';

        $this->loggedOnly();
        $this->checkLogin();

        $this->addMessage("ID: " . $_SESSION['uploadID']);

        if ($_POST) {
            $this->processMain();
            $this->loggedOnly();

            // if form filled correctly
            if (!$this->checkPost()) {
                $this->addMessage("Upload failed");
                return;
            }


            $uploadDir = "./corrected/";

            //create user folder?
            move_uploaded_file($_FILES['fileUp']['tmp_name'], $uploadDir . $_FILES['fileUp']['name']) or die("Cannot copy uploaded file");

            $this->addMessage("Uploaded correctly");
            $filePath = $uploadDir . $_FILES['fileUp']['name'];  //TODO
            $uploadID=$_SESSION['uploadID'];
            $_SESSION['uploadID']="";
            $this->addMessage("sent correctly");
            Work::uploadCorrected($filePath, $uploadID);
            $this->addMessage("db correctly");

            // save to the database
            // user logged in?
            //Work::createDemand($userId, $label, $pages, $diff, $deadline, $desc, $filePath);
        }
    }

    function checkPost() {
        $isOk = true;

        if ($_FILES['fileUp']['size'] == 0) {
            $this->addMessage("Zero byte file");
            $isOk = false;
        } elseif ($_FILES['fileUp']['size'] > $_POST['MAX_SIZE']) {
            $this->addMessage("Too big file (max. 8MB)");
            $isOk = false;
        } elseif (!is_uploaded_file($_FILES['fileUp']['tmp_name'])){
            $this->addMessage("File injected, not uploaded");
            $isOk = false;
        }

        return $isOk;
    }

    function clearController() {

    }
}