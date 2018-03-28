<?php
/*
 *   https://gist.github.com/tediscript/1390628
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Widget {

    static $a = array();

    function Widget() {
        $this->_assign_libraries();
    }

    function run($name, $data = array()) {
        self::$a = $data;
        $args = func_get_args();

        require_once APPPATH . 'widgets/' . $name . EXT;
        $arrname = explode('/', $name);
        $name = $arrname[sizeof($arrname) - 1];
        $name = ucfirst($name);

        $widget = new $name();
        return call_user_func_array(array(&$widget, 'run'), array_slice($args, 1));
    }

    function render($view, $widget_data = array()) {
        include APPPATH . 'views/widgets/' . $view . EXT;
    }

    function view($view, $data = array(), $ret = false) {
        $ci = & get_instance();
        return $ci->load->view($view, $data, $ret);
    }

    function load($object) {
        $this->$object = & load_class(ucfirst($object));
    }

    function load_model($model) {
        $ci = & get_instance();
        $ci->load->model($model, 'tmpmodel');

        return $ci->tmpmodel;
    }

    function _assign_libraries() {
        $ci = & get_instance();
        foreach (get_object_vars($ci) as $key => $object) {
            $this->$key = & $ci->$key;
        }
    }

}