<?php

class FulfillDemandController extends Controller {

    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'Fulfill Demand';
        // Podnadpis strán
        $this->header['subtitle'] = 'Upload corrected file.';
        // Nastavení šablony
        $this->view = 'fulfillDemand';

//        $this->checkLogin();


        if ($_POST) {
            // if form filled correctly
            if (!$this->checkPost()) {
                $this->addMessage("Upload failed");
                return;
            }

            $this->addMessage("Uploaded correctly");

            $uploadDir = "./corrected/";

            //create user folder?
            move_uploaded_file($_FILES['fileUp']['tmp_name'], $uploadDir . $_FILES['fileUp']['name']) or die("Cannot copy uploaded file");

            $filePath = $uploadDir . $_FILES['fileUp']['name'];  //TODO

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