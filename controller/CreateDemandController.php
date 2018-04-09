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
            //$file = ...;
            if (isset($_POST['description']))
                $desc = $_POST['description'];
            else
                $desc = "";

            if (!$this->isDemandNew($label, $pages, $diff, $deadline)) {
                $this->addMessage("I'm old");
                return;
            }

            $this->addMessage("I'm new");

            $_SESSION['lastPost']['label'] = $label;
            $_SESSION['lastPost']['pages'] = $pages;
            $_SESSION['lastPost']['diff'] = $diff;
            $_SESSION['lastPost']['deadline'] = $deadline;
            $_SESSION['lastPost']['desc'] = $desc;

            $filePath = "File path on server";  //TODO

            // save to the database
            Work::createDemand($label, $pages, $diff, $desc, $filePath);
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
            //TODO
            $this->addMessage("Deadline");
            $isOk = false;
        }

        if (!isset($_POST['diff']) || !$_POST['diff']) {
            //TODO
            $this->addMessage("Diff");
            $isOk = false;
        }

        // file uploaded test
//        if () {
//
//        }

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