<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CoreBase {

    const NULL_VALUE = "null";

    function isEmpty($param) {
        if (($param == "") or ( $param == " ") or ( $param == null))
            return TRUE;
        else
            return FALSE;
    }

    function isNotEmpty($param) {
        if (($param != "") and ( $param != " ") and ( $param != null))
            return TRUE;
        else
            return FALSE;
    }

    function quotation($param) {
        return "'" . $param . "'";
    }

}

?>