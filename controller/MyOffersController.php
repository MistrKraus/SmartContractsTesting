<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 22.03.2018
 * Time: 19:03
 */

class MyOffersController extends Controller {
    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'My Contracts';
        // Podnadpis strán
        $this->header['subtitle'] = 'Manage your contracts.';
        // Nastavení šablony
        $this->view = 'myOffers';

        $this->checkLogin();

        for ($i = 0; $i < 5; $i++) {
            $this->data['sentOpen'][$i]['label'] = "Name";
            $this->data['sentOpen'][$i]['deadline'] = rand(0, 20);
            $this->data['sentOpen'][$i]['eth'] = rand(0.0, 1.0);
            $this->data['sentOpen'][$i]['user'] = "Name";
        }

        for ($i = 0; $i < 5; $i++) {
            $this->data['sentClosed'][$i]['label'] = "Name";
            $this->data['sentClosed'][$i]['received'] = rand(0, 20);
            $this->data['sentClosed'][$i]['eth'] = rand(0.0, 1.0);
            $this->data['sentClosed'][$i]['user'] = "Name";
        }

        for ($i = 0; $i < 5; $i++) {
            $this->data['correctingOpen'][$i]['label'] = "Name";
            $this->data['correctingOpen'][$i]['deadline'] = rand(0, 20);
            $this->data['correctingOpen'][$i]['eth'] = rand(0.0, 1.0);
            $this->data['correctingOpen'][$i]['user'] = "Name";
        }

        for ($i = 0; $i < 5; $i++) {
            $this->data['correctingClosed'][$i]['label'] = "Name";
            $this->data['correctingClosed'][$i]['received'] = rand(0, 20);
            $this->data['correctingClosed'][$i]['eth'] = rand(0.0, 1.0);
            $this->data['correctingClosed'][$i]['user'] = "Name";
        }
    }

    function clearController() {
    }
}