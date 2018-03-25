<?php

class CreateDemandController extends Controller {
    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'Create Demand';
        // Podnadpis strán
        $this->header['subtitle'] = 'Upload file to be corrected.';
        // Nastavení šablony
        $this->view = 'createDemand';

        $this->checkLogin();

        if ($_POST) {
            // form not filled correctly
            if (!$this->checkPost()) {
                return;
            }

            $label = $_POST['label'];
            $pages = $_POST['pages'];
            $deadline = $_POST['deadline'];
            //$file = ...;

            // save to the database
        }
    }

    function checkPost() {
        $isOk = true;

        if (!isset($_POST['label']) || !$_POST['label']) {
            $isOk = false;
        }

        if (!isset($_POST['pages']) || !$_POST['pages']) {
            $isOk = false;
        }

        if (!isset($_POST['deadline']) || !$_POST['deadline']) {
            $isOk = false;
        }

        // file uploaded test
//        if () {
//
//        }

        return $isOk;
    }

    function clearController() {
    }
}