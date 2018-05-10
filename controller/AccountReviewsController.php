<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 25.03.2018
 * Time: 11:24
 */

class AccountReviewsController extends Controller {
    function process($params) {
        $this->loggedOnly();

        // Hlavička stránky
        $this->header['title'] = 'Account Reviews';
        // Podnadpis strán
        $this->header['subtitle'] = $_SESSION['username'];     // TODO: Add correctors name
        // Nastavení šablony
        $this->view = 'accountReviews';

        $this->checkLogin();

        $reviews = array(array());

        for ($i = 0; $i < 5; $i++) {
            $reviews[$i]['reviewer'] = "";
        }

        if ($_POST) {
            $this->processMain();
            $this->loggedOnly();
        }
    }

    function clearController() {
    }
}