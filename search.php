<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    search: <input type="text" name="search">
    <button type="submit">search</button>
</form>
<?php


$curl = curl_init();
$search_string = $_POST['search'];
$url = "https://www.amazon.eg/s?k=$search_string";

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($curl);
//echo $result;

preg_match_all("!https://m.media-amazon.com/images/I/[^/s]*?._AC_UL320_.jpg!", $result, $match);


$images = array_values(array_unique($match[0]));

for ($i = 0; $i < count($images); $i++) {


    echo  "<pre>" . "
    <table style='width:100'>
      <tr>
      <td><img src='$images[$i]' ></td>
      </tr>"  . "</pre>";
}


?>
