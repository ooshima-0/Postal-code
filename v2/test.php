<?php

/*
データベースに接続↓
フォームから変数に格納↓
ifで確認↓
パスワードをハッシュ化↓
データベースに書き込み
*/


// 変数
$name = "Administrator";
$password_text = 'Fubuki.1208';
$res = null;
$sql = null;
$db = null;

$arg = $argv[1];

// 接続
// $db = new SQLite3("database.sqlite3");
$dbi = "mysql:dbname=postal_code;host=localhost;charset=utf8mb4";
$user = "root";
$password = "Orga3596";

try {
	$db = new PDO($dbi, $user, $password);
	$path = glob("./address/$arg*.CSV");
	try {
		$f = fopen($path[0], "r");
	} catch (Exception $e) {
		echo $e->getMessage();
		exit();
	}

	// echo $path[0];

	while ($csv = fgetcsv($f)) {
		mb_convert_variables("UTF-8", "SJIS-win", $csv);
		$lim = count($csv);

		$postal_code = $csv[2];
		$prefectures_kana = $csv[3];
		$cities_kana = $csv[4];
		$addresses_kana = $csv[5];
		$prefectures = $csv[6];
		$cities = $csv[7];
		$addresses = $csv[8];
		// 書き込み
		$sql = "INSERT INTO postal_code VALUES (null, '$postal_code', '$prefectures_kana', '$cities_kana', '$addresses_kana', '$prefectures', '$cities', '$addresses')";
		$db->query($sql);
	}

	fclose($f);
} catch (PDOException $e) {
	echo "接続失敗: " . $e->getMessage() . "\n";
	header("Content-Type: text/plain; charset=UTF-8", true, 500);
	exit($e->getMessage());;
}

$db = null;
