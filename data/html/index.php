<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
      <title>Enable HTTP/2</title>
      <link rel="stylesheet" href="/main.css">
      </head>
      <body>
      <div class="container">
      <h1 class="title">JOYSOUND 全国採点GP</h1>

<?php
$user=$_POST['user'];
if ($user=='') {
    $user = $_REQUEST['user'];
    if ($user=='') {
        $first_line = file('/userlist')[0];
        $user=explode(" ", $first_line)[0];
    }
}
echo "<h2> 現在のユーザ: {$user} </h2>"
?>

    <form method="post">
    <select name="user">
<?php
    $lines = file('/userlist');
    foreach ($lines as $line) {
        $user_tmp = explode(" ", $line)[0];
        echo "<option value='$user_tmp'>$user_tmp</option>";
    }
?>
    </select>
    <input type="submit" name="submit" value="切り替え">
    </form>

    <table border='1' cellspacing='0'>
<?php
      $mysql = new mysqli($_ENV['DATABASE_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DATABASE']);

if (!$mysql) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}

// todo: 三項演算子で書き直す
$sort_kind=$_REQUEST['sort_kind'];
if ($sort_kind=='') {
    $sort_kind="month";
} else if ($sort_kind=='portion') {
    $sort_kind="(rank/population)";
} 
$sort_order=$_REQUEST['sort_order'];
if ($sort_order=='') {
    $sort_order="desc";
}
$sql = "SELECT * FROM gp_score WHERE user='$user' ORDER BY $sort_kind  $sort_order";

$stmt = $mysql->query( $sql );
        
echo "<tr>";
echo_th_with_sort($user, $sort_kind=="title", $sort_order, "title", "曲名", True);
echo_th_with_sort($user, $sort_kind=="score", $sort_order, "score", "点数", True);
echo_th_with_sort($user, $sort_kind=="rank", $sort_order, "rank", "順位", False, "colspan='3'");
echo_th_with_sort($user, $sort_kind=="(rank/population)", $sort_order, "portion", "上位%", False);
echo_th_with_sort($user, $sort_kind=="artist", $sort_order, "artist", "アーティスト", True);
echo_th_with_sort($user, $sort_kind=="month", $sort_order, "month", "日付", True);
//echo_th_with_sort($user, $sort_kind=="user", $sort_order, "user", "ユーザ", True);
echo "</tr>";


while( $result = $stmt->fetch_array(MYSQLI_ASSOC) ){
    $portion = sprintf("%.2f",$result['rank'] / $result['population'] * 100);
    $score = sprintf("%2.3f", $result['score']);
    echo "<tr>";
    echo "<td width='400'>{$result['title']}</td>";
    echo "<td>{$score}</td>";
    echo "<td align='right' style='border-right-style: hidden;'>{$result['rank']}</td>";
    echo "<td style='border-left-style: hidden; border-right-style: hidden' >/</td>";
    echo "<td align='right' style='border-left-style: hidden;'>{$result['population']}</td>";
    echo "<td align='right'>{$portion}</td>";
    echo "<td>{$result['artist']}</td>";
    echo "<td>{$result['month']}</td>";
    //echo "<td>{$result['user']}</td>";
    echo "</tr>";
}
echo "</table>\n";

mysqli_close($mysql);
?>
</div>
<script src="/main.js"></script>
    </body>
    </html>

<?php
    function echo_th_with_sort($user, $is_selected, $current_sort_order, $sort_kind, $str, $is_in_order, $option="")
    {
        if ($is_selected) {
            if ($current_sort_order=="desc") {
                echo "<th bgcolor='#9999FF' {$option}><a href='index.php?sort_kind={$sort_kind}&sort_order=asc&user={$user}'>{$str}</a></th>";
            } else {
                echo "<th bgcolor='#FF9999' {$option}><a href='index.php?sort_kind={$sort_kind}&sort_order=desc&user={$user}'>{$str}</a></th>";
            }
        } else {
            if ($is_in_order) {
                echo "<th {$option}><a href='index.php?sort_kind={$sort_kind}&sort_order=desc&user={$user}'>{$str}</a></th>";
            } else {
                echo "<th {$option}><a href='index.php?sort_kind={$sort_kind}&sort_order=asc&user={$user}'>{$str}</a></th>";
            }
        }
    }
?>
