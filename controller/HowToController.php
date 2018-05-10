<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 07.05.2018
 * Time: 15:52
 */
class HowToController extends Controller
{
    function process($params)
    {
        // Hlavička stránky
        $this->header['title'] = 'Q&A';
        $this->header['subtitle'] = 'User Manual.';
        // Nastavení šablony
        $this->view = 'howTo';

        $_SESSION['description'] = "HowToController";

        $this->checkLogin();

        if ($_POST) {
            $this->processMain();
        }
    }


    function clearController() {

    }
}