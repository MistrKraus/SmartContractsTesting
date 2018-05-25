<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 24.05.2018
 * Time: 20:47
 */

class MyOffersAjaxController extends Controller {

    function process($params)
    {

        $output = "-----";

        if ($_POST) {
//            $_SESSION['test'] = "----";
            $output = "0";
            // Ziskani dat pro vytvoreni smart kontraktu
            if (isset($_POST['bindId']) && isset($_POST['reqId']) && $_POST) {
//                $_SESSION['test'] = ">>>>>";
                $id = $_POST['bindId'];
                Work::acceptBind($id, $_POST['reqId']);

                $data = Work::GetContractData($id);

                $output = $data['wallet'] . ';' . $data['deadline'] . ';' . $data['wei'] . ';' . $data['hash'] . ';' . $id;

//                $_SESSION['test'] = $output;
                echo $output;
//                return $output;
            }

            // Ulozeni adresy kontraktu do databaze
            if (isset($_POST['contract_add']) && isset($_POST['contract_id'])) {
                $address = $_POST['contract_add'];
                if ($address != "-1") {
//                    $_SESSION['test'] = "!!!!!";
                    Work::setContractAddress($_POST['contract_id'], $address);
                    echo "Sprave";
                } else {
                    Work::contractFailed($_POST['contract_id']);
                    echo "Spatne";
                }
                //echo "funguje";
                //return;
            }

            // Zruseni kontraktu
            if (isset($_POST['contract_add']) && isset($_POST['cancel_bind_id'])) {
                Work::contractFailed($_POST['cancel_bind_id']);
            }
        }

        echo $output;

//            if ($_SESSION['sentBinds'] != "" && $_SESSION['sentBinds'] != 1) {
//
//                // odmitani nabidky
//                foreach ($_SESSION['sentBinds'] as $bind) {
////                    echo "->".$bind['id'];
////                    return;
//                    if (isset($_POST['acceptBind' . $bind['id']])) {
//                        $id = $bind['id'];
////                        echo "id je " . $id;
////                        return;
//
//                        Work::acceptBind($id, $_SESSION['sentBinds']['req_id']);
//
//                        //TODO odkomentovat
////                        if (Work::acceptBind($bind['id'], $_SESSION['sentBinds']['req_id']) == -1) {
////                            $this->addMessage("Chyba");
////                        } else {
//
//                            $data = Work::GetContractData($id);
//
//                            $output = $data['wallet'].';'.$data['deadline'].';'.$data['wei'].';'.$data['hash'];
//
//                            $_SESSION['test'] = $output;
////                            echo $output;
////                            return;
////                        }
//                    }
//                }
//            }
//        }
    }

    function clearController() {
        // TODO: Implement clearController() method.
    }
}