<?php
/**
 * Created by PhpStorm.
 * User: kraus
 * Date: 22.05.2018
 * Time: 17:32
 */

class File {

    public static function getFile($id) {
        $id = 12;
        return Db::getFirstRow("SELECT * FROM files WHERE id=:id", array(':id'=>$id));
    }

    public static function getAllFiles() {
        return Db::getAll("SELECT * FROM files", null);
    }

    public static function insertFile($name, $type, $size, $data, $diff, $pages, $hash) {
        if (Db::insert("files", array('name'=>$name, 'type'=>$type, 'size'=>$size, 'data'=>$data,
            'hash'=>$hash, 'diff' => $diff, 'pages' => $pages)) == -1) {
            return -1;
        } else {
            return Db::getLastId();
        }
    }

    public static function insertCorrectedFile($name, $type, $size, $data, $hash) {
        if (Db::insert("files", array('name'=>$name, 'type'=>$type, 'size'=>$size, 'data'=>$data,
                'hash'=>$hash) == -1)) {
            return -1;
        } else {
            return Db::getLastId();
        }
    }
}