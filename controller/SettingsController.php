<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 25.03.2018
 * Time: 9:49
 */

class SettingsController extends Controller {
    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'My Account';
        // Podnadpis strán
        $this->header['subtitle'] = 'Account overview.';
        // Nastavení šablony
        $this->view = 'settings';

        $this->loggedOnly();
        $this->checkLogin();

        if ($_POST) {
            $this->processMain();
            $this->loggedOnly();
//            User::setEmail();
            User::setAsCorrector($_SESSION['user_id'], $_POST['corrector']);
        }
    }

    function clearController() {

    }
}