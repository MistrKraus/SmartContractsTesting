<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 22.03.2018
 * Time: 19:02
 */

class FindWorkController extends Controller {
    function process($params) {
        // Hlavička stránky
        $this->header['title'] = 'Find Work';
        // Podnadpis strán
        $this->header['subtitle'] = 'Choose, bind, correct!';
        // Nastavení šablony
        $this->view = 'findWork';

//        $this->checkLogin();

        $_SESSION['works'] = array(array());

        for ($i = 0; $i < 5; $i++) {
            $_SESSION['works'][$i]['label'] = "1234567890123456";
            $_SESSION['works'][$i]['pages'] = rand(0, 5);
            $_SESSION['works'][$i]['diff'] = rand(0, 5);
            $_SESSION['works'][$i]['deadline'] = rand(0, 5);
            $_SESSION['works'][$i]['message'] = "If you use it on a block element all its children will inherit the value. Vertical-align can also be used with HTML table cells. Values for table cells: top: aligns the cells contents with the top of the cell. middle: aligns the cells contents with the middle of the cell. bottom: aligns the cells contents with the bottom of";
        }

        if ($_POST) {
            $i = 0;
            for ($i; $i < sizeof($_SESSION['works']); $i++) {
                if (isset($_POST["eth".$i]) && $_POST["eth".$i] > 0) {
//                    Work::createBind(, , $_POST["eth".$i]);   //TODO
                }
            }
        }
    }

    function clearController() {
    }
}