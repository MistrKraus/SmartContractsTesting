<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 24.05.2018
 * Time: 20:47
 */

class MyOffersAjaxController extends Controller {

    function process($params) {

        $output = "";

        if ($_POST) {
            if ($_SESSION['sentBinds'] != "" && $_SESSION['sentBinds'] != 1) {
                $this->addMessage("neco...");
                // odmitani nabidky
                foreach ($_SESSION['sentBinds'] as $bind) {
                    if (isset($_POST['acceptBind' . $bind['id']])) {

                        Work::acceptBind($bind['id'], $_SESSION['sentBinds']['req_id']);

                        //TODO odkomentovat
//                        if (Work::acceptBind($bind['id'], $_SESSION['sentBinds']['req_id']) == -1) {
//                            $this->addMessage("Chyba");
//                        } else {
                            $id = $bind['id'];
                            $data = Work::GetContractData($id);

                            $output = $data['wallet'].';'.$data['deadline'].';'.$data['wei'].';'.$data['hash'];

                            $_SESSION['test'] = $output;
//                            echo $output;
//                            return;
//                        }
                    }
                }
            }


        }

        echo $output;
    }

    function clearController() {
        // TODO: Implement clearController() method.
    }
}