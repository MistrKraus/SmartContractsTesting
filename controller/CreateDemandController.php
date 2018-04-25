<?php

class CreateDemandController extends Controller {

    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'Create Demand';
        // Podnadpis strán
        $this->header['subtitle'] = 'Upload file to be corrected.';
        // Nastavení šablony
        $this->view = 'createDemand';

//        $this->checkLogin();


        if ($_POST) {
            // if form filled correctly
            if (!$this->checkPost()) {
                $this->addMessage("Filled wrong");
                return;
            }

            $this->addMessage("Filled correctly");

            $label = $_POST['label'];
            $pages = $_POST['pages'];
            $diff = $_POST['diff'];
            $deadline = $_POST['deadline'];
            $uploadDir = "./uploads/";
            if (isset($_POST['description']))
                $desc = $_POST['description'];
            else
                $desc = "";

            if (!$this->isDemandNew($label, $pages, $diff, $deadline)) {
                $this->addMessage("I'm old");
                return;
            }

            //create user folder?
            move_uploaded_file($_FILES['fileUp']['tmp_name'], $uploadDir . $_FILES['fileUp']['name']) or die("Cannot copy uploaded file");

            $this->addMessage("I'm new");

            $_SESSION['lastPost']['label'] = $label;
            $_SESSION['lastPost']['pages'] = $pages;
            $_SESSION['lastPost']['diff'] = $diff;
            $_SESSION['lastPost']['deadline'] = $deadline;
            $_SESSION['lastPost']['desc'] = $desc;

            $filePath = $uploadDir . $_FILES['fileUp']['name'];  //TODO

            // save to the database
            // user logged in?
            //Work::createDemand($userId, $label, $pages, $diff, $deadline, $desc, $filePath);
        }
    }

    function checkPost() {
        $isOk = true;

        if (!isset($_POST['label']) || !$_POST['label']) {
            //TODO
            $this->addMessage("Label");
            $isOk = false;
        }

        if (!isset($_POST['pages']) || !$_POST['pages']) {
            //TODO
            $this->addMessage("Pages");
            $isOk = false;
        }

        if (!isset($_POST['deadline']) || !$_POST['deadline']) {
            $this->addMessage("Deadline");
            $isOk = false;
        }

        if (!isset($_POST['diff']) || !$_POST['diff']) {
            //TODO
            $this->addMessage("Diff");
            $isOk = false;
        }

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