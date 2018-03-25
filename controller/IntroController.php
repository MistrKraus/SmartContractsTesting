<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 05.11.2017
 * Time: 15:45
 */

class IntroController extends Controller {

    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'Hire expert correctors';
        $this->header['subtitle'] = 'Pay with Ethereum!';
        // Nastavení šablony
        $this->view = 'intro';

        $_SESSION['fromUrl'] = 'intro';

        $this->checkLogin();

        if ($_POST) {

        }
    }

    function clearController() {

    }
}