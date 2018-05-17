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
        $this->header['subtitle'] = $_SESSION['username'];
        // Nastavení šablony
        $this->view = 'accountReviews';

        $this->checkLogin();

        $this->data['reviews'] = Work::listReviews($_SESSION['user_id']);

        if ($_POST) {
            $this->processMain();
            $this->loggedOnly();
        }
    }

    function clearController() {
    }
}