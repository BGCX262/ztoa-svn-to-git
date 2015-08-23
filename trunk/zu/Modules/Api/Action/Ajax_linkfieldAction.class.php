<?php

/**
 *  linkfield.php 异步获取数据
 * api.php?op=Ajax_linkfield&act=search_ajax&value=nn&table_name=think_category&select_title=catid,catname&like_title=catname&set_title=catname
 */
class Ajax_linkfieldAction extends AdminbaseAction {

    function _initialize() {
        parent::_initialize();
        $key = authcode($this->_get("key"), "DECODE", C("AUTHCODE"));
        if ($key != "true") {
            exit;
        }
    }
    
    public function index() {
        $this->public_index();
    }

    public function public_index() {
        switch ($_GET['act']) {
            case 'search_ajax':
                $this->search_ajax();
                break;
            case 'check_search':
                $this->check_search();
                break;
            case 'search_data':
                $this->search_data();
                break;
        }
    }

    protected function search_ajax() {
        $db = M("");
        $data = array();
        if ($_GET['value']) {
            $value = trim($_GET['value']);

            $table_name = trim($_GET['table_name']);
            $columns = $db->query("SHOW COLUMNS FROM $table_name");

            foreach ($columns as $r) {
                if ($r['Key'] == 'PRI')
                    break;
            }

            $order_id = $r['Field'];
            $select_title = $_GET['select_title'] ? trim($_GET['select_title']) : '*';
            $like_title = $_GET['like_title'] ? trim($_GET['like_title']) : 'title';
            $set_title = trim($_GET['set_title']);
            $limit = $_GET['limit'] ? trim($_GET['limit']) : '20';
            $sql = "SELECT $select_title from $table_name where $like_title LIKE('%$value%') order by $order_id desc limit 0,$limit";

            $data = $db->query($sql);

            echo $_GET['callback'] . '(', json_encode($data), ')';
        }
    }

    protected function check_search() {
        $get_db = M("");

        if ($_GET['value']) {

            $value = trim($_GET['value']);

            $table_name = trim($_GET['table_name']);
            $set_type = trim($_GET['set_type']);
            $set_id = trim($_GET['set_id']);
            $set_title = trim($_GET['set_title']);
            $limit = $_GET['limit'] ? trim($_GET['limit']) : '20';
            $columns = $get_db->query("SHOW COLUMNS FROM $table_name");
            foreach ($columns as $r) {
                if ($r['Key'] == 'PRI')
                    break;
            }
            $order_id = $rs['Field'];

            if ($set_type == 'id') {
                $sqls = $set_id . ' = ' . $value;
            } elseif ($set_type == 'title') {
                $sqls = $set_title . ' = ' . $value;
            } elseif ($set_type == 'title_id') {
                $value = explode('_', $value);
                $sqls = $set_title . ' = \'' . $value[0] . '\' AND ' . $set_id . ' = ' . $value[1];
            }

            $dataArr = array();
            $sqld = "SELECT $set_title from $table_name where $sqls order by $order_id desc limit 0,1";

            $dataArr = $get_db->query($sqld);

            echo $_GET['callback'] . '(', json_encode($dataArr), ')';
        }
    }

    /**
     * 获取相应表字段列表 
     */
    protected function search_data() {

        //获取表名
        $tables = $_POST['tables'] ? $_POST['tables'] : trim($_GET['tables']);

        if ($tables) {
            $Common = D("Common");
            $tables = str_replace(C("DB_PREFIX"), "", $tables);
            $data = $Common->get_fields($tables);
            if ($data) {
                echo $_GET['callback'] . '(', json_encode($data), ')';
            } else {
                exit(0);
            }
        }
    }

}

?>