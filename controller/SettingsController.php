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

        $this->checkLogin();
    }

    function clearController() {

    }
}