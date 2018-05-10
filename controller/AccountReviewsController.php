<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 25.03.2018
 * Time: 11:24
 */

class AccountReviewsController extends Controller {
    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'Account Reviews';
        // Podnadpis strán
        $this->header['subtitle'] = 'insert name';     // TODO: Add correctors name
        // Nastavení šablony
        $this->view = 'accountReviews';

        $this->loggedOnly();
        $this->checkLogin();

        $reviews = array(array());

        for ($i = 0; $i < 5; $i++) {
            $reviews[$i]['reviewer'] = "";
        }
    }

    function clearController() {
    }
}