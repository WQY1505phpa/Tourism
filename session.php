<?php
header("Content-type: text/html; charset=utf-8");

function connect(){
    $dsn=[
        'localhost'=> '127.0.0.1',
        'username' => 'root',
        'password' => 'root',
        'charset'  => 'utf-8',
        'dbname'   => "blog",
    ];
    $localhost=$dsn['localhost'];
    $username=$dsn['username'];
    $password=$dsn['password'];
    $dbname=$dsn['dbname'];
    $mysqli = new mysqli("$localhost","$username","$password","$dbname");
    if ($mysqli->connect_error) {
        die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
    }
    $charset=$dsn['charset'];
    $mysqli->query("set names $charset");//设置字符集
    return $mysqli;
}

function sess_open(){
    //echo __FUNCTION__."\r\n";
    connect();
}

function sess_close(){
    //echo __FUNCTION__."\r\n";
}

function sess_read($sess_id){
    // echo $sess_id;exit;
    // echo __FUNCTION__."\r\n";
    $mysql=connect();
    $sql = "select sess_data from `session` where sess_id = '$sess_id'";
    // echo $sql;exit;
    $result = $mysql->query($sql);  // $link 可以自己找到，或可以声明为全局变量
    if($rows = mysqli_fetch_assoc($result)){
        return $rows['sess_data'];
    }else{
        return '';
    }
}

function sess_write($sess_id, $sess_data){
    //echo __FUNCTION__."\r\n";
    $mysql=connect();
    //当前 session 存在则更新 sess_data
    //获得时间戳，mysql函数：unix_timestamp();
    //获得时间戳，php函数：time();
    $sql = "replace into `session` values('$sess_id', '$sess_data', now())";
    //on duplicate key update sess_data = '$sess_data'，times = now()";  //这是为了gc()
    return $mysql->query($sql);
}

function sess_destroy($sess_id){
    //echo __FUNCTION__."\r\n";
    $mysql=connect();
    $sql = "delete from `session` where sess_id = '$sess_id'";
    return $mysql->query($sql);
}
function sess_gc(){
    // echo __FUNCTION__."\r\n";
}

session_set_save_handler(
    'sess_open',
    'sess_close',
    'sess_read',
    'sess_write',
    'sess_destroy',
    'sess_gc'
);

//ibraries/session.inc.php at line 79  // session.save_handler
// ini_set('session.save_handler', 'files');
// session_save_path("D:\web\session");
session_start();
if (!empty($path)) {
    session_save_path($path);
}
$_SESSION['user_id']='123';
$_SESSION['name']='雷政资';
var_dump($_SESSION);