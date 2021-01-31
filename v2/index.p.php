<?php

$data = null;
$prefectures = null;
$postal_code = null;
$prefecture = null;
$city = null;
$address = null;
$postal_code = $_POST["postal_code"];
$prefecture = $_POST["prefecture"];
$city = "%" . $_POST["city"] . "%";
$address = "%" . $_POST["address"] . "%";

// 接続
// $db = new SQLite3("database.sqlite3");
$dbi = "mysql:dbname=postal_code;host=localhost;charset=utf8mb4";
$user = "root";
$password = "Orga3596";

try {
	$db = new PDO($dbi, $user, $password);
	$sql = "SELECT * FROM prefectures";
	$stmt = $db->query($sql);
	$prefectures = $stmt->fetchAll(PDO::FETCH_ASSOC);
	/* var_dump($prefectures);
	foreach($prefectures as $data) {
		echo $data["prefecture"];
	} */

	if(!empty($postal_code)) {
		if (mb_strlen($postal_code) != 7) {
			$postal_code = "0" . $postal_code;
		}
		$sql = "SELECT * FROM postal_code WHERE postal_code = ?";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(1, $postal_code, PDO::PARAM_STR);
		$stmt->execute();
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
	} else {
		if (!empty($prefecture) && !empty($city) && !empty($address)) {
			$sql = "SELECT * FROM postal_code WHERE prefectures LIKE ? AND cities LIKE ? AND addresses LIKE ?";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(1, $prefecture, PDO::PARAM_STR);
			$stmt->bindParam(2, $city, PDO::PARAM_STR);
			$stmt->bindParam(3, $address, PDO::PARAM_STR);
			$stmt->execute();
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} else {
		}
	}
} catch (PDOException $e) {
	echo "接続失敗: " . $e->getMessage() . "\n";
	header("Content-Type: text/plain; charset=UTF-8", true, 500);
	exit($e->getMessage());;
}

$db = null;
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF8">
		<title></title>
	</head>
	<body>
		<h1>郵便番号検索</h1>
		<h3>番号から検索</h3>
		<form action="" method="POST">
			<p>郵便番号：<input type="tel" name="postal_code" id="postal_code" maxlength="7">　<input type="submit" value="検索"></p>
		</form>
		<h3>住所から検索</h3>
		<form action="" method="POST">
			<p>都道府県：
			<select name="prefecture">
				<?php foreach($prefectures as $value) { ?>
				<?php echo "<option value='" . $value["prefecture"] . "'>" . $value["prefecture"] . "</option>"; ?>
				<?php } ?>
			</select>
			</p>
			<!-- <p>都道府県：<input type="text" name="prefecture"></p> -->
			<p>市区町村：<input type="text" name="city"></p>
			<p>町名：<input type="text" name="address">　<input type="submit" value="検索"></p>
		</form>
		<h2>検索結果</h2>
		<table border="1">
			<tr>
				<th>郵便番号</th>
				<th>都道府県(かな)</th>
				<th>市区町村(かな)</th>
				<th>町名(かな)</th>
				<th>都道府県</th>
				<th>市区町村</th>
				<th>町名</th>
			</tr>
			<?php for($i = 0; $i < count($data); $i++) { ?>
				<tr>
					<td><?php echo $data[$i]["postal_code"] ?></td>
					<td><?php echo $data[$i]["prefectures_kana"] ?></td>
					<td><?php echo $data[$i]["cities_kana"] ?></td>
					<td><?php echo $data[$i]["addresses_kana"] ?></td>
					<td><?php echo $data[$i]["prefectures"] ?></td>
					<td><?php echo $data[$i]["cities"] ?></td>
					<td><?php echo $data[$i]["addresses"] ?></td>
				</tr>
			<?php } ?>
		</table>
		<script type="text/javascript">
		$("#postal_code").on("keypress", function(event) {
			return onlyNumber(event);
		});
		function onlyNumber(e){
			var st = String.fromCharCode(e.which);
			if ("0123456789".indexOf(st, 0) < 0) {
				return false;
			}
			return true;
		}
		</script>
	</body>
</html>
