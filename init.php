<?php
// 数据库配置信息
$db = [
	'username' => filter_input(INPUT_POST,'user',FILTER_SANITIZE_STRING),
	'password' => filter_input(INPUT_POST,'pwd',FILTER_SANITIZE_STRING),
	'dsn' => 'mysql:host='.filter_input(INPUT_POST,'host',FILTER_VALIDATE_IP).';dbname=mysql;charset=utf8'
];

// 数据库连接
try {
	// ,array(PDO::ATTR_PERSISTENT=>true)
	$pdo = new PDO($db['dsn'],$db['username'],$db['password']);	
} catch (Exception $e) {
	showMessage($code=500,$data=[],'数据库连接错误，请检查配置');
}	

// 业务逻辑处理
if (filter_input(INPUT_POST,'action',FILTER_SANITIZE_STRING) == 'init') {
	$stmt = $pdo->query('show variables where Variable_name=\'general_log\'');
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($row['Value'] == 'OFF') {
		$stmt = $pdo->query('set global general_log=\'ON\'');
		$ret = $stmt->execute();
	}

	$stmt = $pdo->query("show variables where Variable_name='log_output'");
	$row2 = $stmt->fetch(PDO::FETCH_ASSOC);

	if ($row2['Value'] == 'FILE') {
		$stmt = $pdo->query('set global log_output=\'table\'');
		$ret = $stmt->execute();
	}
	showMessage($code=200,['cul_time'=>date('Y-m-d H:i:s')],'');
} else if (filter_input(INPUT_POST,'action',FILTER_SANITIZE_STRING) == 'pull') {
	$sql = "select event_time,argument from mysql.general_log where command_type='Query' and argument not like 'set global general_log=on;SET GLOBAL log_output%' and argument not like 'select event_time,argument from%' and argument not like 'SHOW%' and argument not like 'SET NAMES gbk;SET character_set_results=NULL%' and event_time> :event_time";
	error_log($sql.PHP_EOL,3,'/tmp/amu.txt');
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':event_time', $_POST['init_time']);
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	showMessage($code=200,$rows,[]);
} else if (filter_input(INPUT_POST,'action',FILTER_SANITIZE_STRING) == 'restore') {
	$stmt = $pdo->query('set global general_log=\'OFF\'');
	$ret = $stmt->execute();
	$stmt = $pdo->query('set global log_output=\'FILE\'');
	$ret = $stmt->execute();
	showMessage(200,[],'还原成功');
} else {
	showMessage(404,[],'bad gay');
}

/**
 * [showMessage 通用数据格式化输出]
 * @param  integer $code [返回状态码]
 * @param  array   $data [查询的结果集]
 * @param  string  $msg  [提示信息]
 * @return string        [返回数据]
 */
function showMessage($code=200,$data=[],$msg=''){
	$arr = [];
	$arr['code'] = $code;
	$arr['data'] = $data;
	$arr['msg'] = $msg;
	echo json_encode($arr,JSON_UNESCAPED_UNICODE);
	$pdo = null; // 释放数据库连接
	die();
}
