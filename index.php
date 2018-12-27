<?php
//Versija 1.01 . Taisyta LY2GN 2018-02-15  Hill-Fort-Hunter award lentelė automatiškai pasikeičia priklausomai nuo einamųjų metų */
//Versija 1.02 . Taisyta LY2GN 2018-02-20  Papildyta pirmo puslapio statistika: digi modos, PHONE moda  papildyta AM; pridėti bandai 6m, 70cm, 3cm; pridėta LastUpdate() funkcija */


$db = mysqli_connect("localhost", "qrzlt_logs-cli", "") or die("Bandykite veliau");

mysqli_select_db($db,"qrzlt_logs") or die(mysqli_error($db));

mysqli_query($db,"SET CHARACTER SET utf8");
mysqli_query($db,"SET NAMES 'utf8'");

if (!function_exists('html2slashes')) {
    function html2slashes($k) { return htmlspecialchars("$k", ENT_QUOTES); }
}

if (!function_exists('slashes')) {
    function slashes($k) { return (get_magic_quotes_gpc()) ? $k : addslashes($k); }
}

$servport  = ($_SERVER['SERVER_PORT'] == '443') ? 'https' : 'http';
$domenas   = $servport . "://" . $_SERVER['HTTP_HOST'] . "/";
$saukinysP = strtoupper(html2slashes($_POST['saukinys']));
$stats_limit = 50;
list($kas, $tmp) = explode("-", html2slashes($_GET['k']));

$kas  = strtoupper($kas);
$prog = html2slashes($_GET['p']);
if (empty($prog) && strlen($kas) < 5)
    $kas = str_replace(array(
        'WAL',
        'LHFA',
        'LYFF'
    ), array(
        'wal',
        'lhfa',
        'lyff'
    ), $kas);

$names['wal'][0]        = "skverai";
$names['wal'][1]        = "skveras<br>the square";
$names['lhfa'][0]       = "piliakalniai";
$names['lhfa'][1]       = "piliakalnis<br>hillfort";
$names['lyff'][0]       = "parkai";
$names['lyff'][1]       = "parkas, rezervatas<br>park,reserve";
for ($step=50;$step<=850;$step+=50) {
  $medzioja['lhfa'][$step] = "LHFA-$step";
}
/*
$medzioja['lhfa'][50]   = "LHFA–50";
$medzioja['lhfa'][100]  = "LHFA–100";
$medzioja['lhfa'][150]  = "LHFA–150";
$medzioja['lhfa'][200]  = "LHFA–200";
$medzioja['lhfa'][300]  = "LHFA–300";
$medzioja['lhfa'][400]  = "LHFA–400";
$medzioja['lhfa'][500]  = "LHFA–500";
$medzioja['lhfa'][600]  = "LHFA–600";
$medzioja['lhfa'][700]  = "LHFA–700";
$medzioja['lhfa'][800]  = "LHFA–800"; */
$aktyvuoja['lhfa'][25]  = "LHF–ACTIVATOR";
$aktyvuoja['lhfa'][50]  = "LHF–ACTIVATOR 50";
$aktyvuoja['lhfa'][75]  = "LHF–ACTIVATOR 75";
$aktyvuoja['lhfa'][100] = "LHF–ACTIVATOR 100";
$aktyvuoja['lhfa'][125] = "LHF–ACTIVATOR 125";
$aktyvuoja['lhfa'][150] = "LHF–ACTIVATOR 150";
$aktyvuoja['lhfa'][175] = "LHF–ACTIVATOR 175";
$medzioja['wal'][50]    = "WAL";
$medzioja['wal'][100]   = "WAL–100";
$medzioja['wal'][200]   = "WAL–200";
$medzioja['wal'][300]   = "WAL–300";
$medzioja['wal'][394]   = "WAL Trophy";
$aktyvuoja['wal'][999]  = "";
$medzioja['lyff'][5]    = "LYFF 3 pakopa";
$medzioja['lyff'][10]   = "LYFF 2 pakopa";
$medzioja['lyff'][15]   = "LYFF 1 pakopa";
$medzioja['lyff'][20]   = "LYFF Honor Roll";
$aktyvuoja['lyff'][5]   = "LYFF aktyvatoriaus 3 p.";
$aktyvuoja['lyff'][10]  = "LYFF aktyvatoriaus 2 p.";
$aktyvuoja['lyff'][15]  = "LYFF aktyvatoriaus 1 p.";
$aktyvuoja['lyff'][20]  = "LYFF aktyvatoriaus Honor Roll";

$texts['ly']['back']    = "į pirmą puslapį";
$texts['en']['back']    = "back to homepage";
$texts['ly']['achievements']    = "pasiekimai";
$texts['en']['achievements']    = "achievements";
$texts['ly']['hunted']    = "sumedžioti";
$texts['en']['hunted']    = "hunted";
$texts['ly']['activated']    = "aktyvuoti";
$texts['en']['activated']    = "activated";
$texts['ly']['total']    = "viso";
$texts['en']['total']    = "total";
$texts['ly']['objects']    = "objektai";
$texts['en']['objects']    = "objects";
$texts['ly']['year']    = "metai";
$texts['en']['year']    = "year";
$texts['ly']['diplomas']    = "diplomai";
$texts['en']['diplomas']    = "diplomas";
$texts['ly']['program']    = "programa";
$texts['en']['program']    = "program";

$google_analytics = "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;
i[r]=i[r]||function(){		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();
a=s.createElement(o),		  m=s.getElementsByTagName(o)[0];
a.async=1; a.src=g; m.parentNode.insertBefore(a,m)		  })
(window,document,'script','//www.google-analytics.com/analytics.js','ga');
 ga('create', 'UA-9264286-3', 'qrz.lt'); ga('send', 'pageview');  </script>";

function pirmoPslStatistika($vardas, $kas, $kas2, $kuris)
{
    global $db;
    $priedas = (strlen($kas2) >= 2) ? (" group by " . $kas . "," . $kas2) : '';

    return "<tr><td class='" . $kuris . "'>" . $vardas . "</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `mode` in('SSB','LSB','USB','FM','AM') $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `mode` in('CW') $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `mode` in('DIGI','PSK31','PSK63','RTTY','SSTV','MFSK','FT8') $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '160m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '80m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '40m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '30m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '20m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '17m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '15m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '12m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '10m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '6m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '2m' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '70cm' $priedas"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `band` = '3cm' $priedas"))."</td> 
</tr>";
}

function pirmoPslAktyvuotojuStatistika($vardas, $kas, $kuris)
{
    global $db;
    $priedas = (strlen($kas2) >= 2) ? (" group by " . $kas . "," . $kas2) : '';

    return "<tr>
<td class='$kuris'>$vardas</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' GROUP BY `caller`"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `call` LIKE 'LY%' GROUP BY `call`"))."</td>
<td class='$kuris'>".mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE `$kas`!='' and `call` not LIKE 'LY%' GROUP BY `call`"))."</td>
</tr>";
}

function LastUpdate(){
    global $db;
    $info = mysqli_query($db,"SELECT concat(count(`id`),' QSO records. Last update ', max(`datetimenow`),'.') from `qso`")->fetch_array[0];
    return $info;
}


function saukinioStatistika($vardas, $saukinys, $kas, $kuris)
{
    global $medzioja, $aktyvuoja, $db;

    $medziotu = mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE (`call` LIKE '$saukinys' or `call` LIKE '$saukinys"."/%') and `$kas"."1` !='' group by `$kas"."1`, `$kas"."2`"));
    $aktyvuotu = mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE (`caller` LIKE '$saukinys' or `caller` LIKE '$saukinys"."/%' or `operator` LIKE '$saukinys' or `operator` LIKE '$saukinys"."/%') and `$kas"."1` !='' group by `$kas"."1`, `$kas"."2`"));
    $viso = mysqli_num_rows(mysqli_query($db,"SELECT `id` FROM `qso` WHERE (`call` LIKE '$saukinys' or `call` LIKE '$saukinys"."/%' or `caller` LIKE '$saukinys' or `caller` LIKE '$saukinys"."/%' or `operator` LIKE '$saukinys' or `operator` LIKE '$saukinys"."/%') and `$kas"."1` !='' group by `$kas"."1`, `$kas"."2`"));

    // nuimtas kai pridejau $viso, bet gali buti klaidingas, nes neaisku ar visi diplomai uzskaito aktyvuotus objektus ir kiek ju reikia aktyvuoti

    // foreach($medzioja[$kas] as $key => $value) { if($key <= $medziotu) { $diplomas[] = $value; 		} }

    foreach ($medzioja[$kas] as $key => $value) {
        if ($key <= $viso) {
            $diplomas[] = $value;
        }
    }
    foreach ($aktyvuoja[$kas] as $key => $value) {
        if ($key <= $aktyvuotu) {
            $diplomas[] = $value;
        }
    }
    return "<tr><td class='$kuris'>$vardas</td><td class='$kuris'>$medziotu</td><td class='$kuris'>$aktyvuotu</td><td class='$kuris'>$viso</td><td class='$kuris'>".@implode($diplomas, ",<br/>")."</td></tr>";
}
function isspreskObjekta($kas)
{
    global $masyvas, $koordinates, $db;

    $result = mysqli_query($db,"SELECT * FROM " . $kas . "");

    while ($row = @mysqli_fetch_array($result)) {
        if ($kas == 'lhfa') {
            $masyvas[$row['state']][$row['nr']] = $row['name'];
            $koordinates[$row['state']][$row['nr']] = $row['coordsN'] . "," . $row['coordsE'];
        } elseif ($kas == 'wal') {
            $masyvas[$row['row']][$row['column']] = $row['name'];
        } elseif ($kas == 'lyff') {
            $masyvas[$row['type']][$row['nr']] = $row['name'];
        }
    }
}
function parodykObjekta($kas, $tdClass)
{
    global $kvadratas, $masyvas, $koordinates, $row;
    $kvadratas = (strlen($row[$kas . "1"]) > 0 ? $row[$kas . "1"] . "-" . ((strlen($row[$kas . "2"]) < 2 ? "0" . $row[$kas . "2"] : $row[$kas . "2"])) : '');
    if ($kas == 'lhfa') {
        return "<td class='$tdClass'><a href='http://qrz.lt/qth?" . $koordinates[$row['lhfa1']][$row['lhfa2']] . "' target='_blank'>" . $kvadratas . "</a></td>" . "<td class='$tdClass'><a href='http://qrz.lt/qth?" . $koordinates[$row['lhfa1']][$row['lhfa2']] . "' target='_blank'>" . $masyvas[$row['lhfa1']][$row['lhfa2']] . "</a></td>";
    } elseif ($kas == 'wal') {
        $kvadratas = str_replace("-", "", $kvadratas);
        return "<td class='$tdClass'><a href='http://qrz.lt/qth?" . $kvadratas . "' target='_blank'>" . $kvadratas . "</a></td>" . "<td class='$tdClass'><a href='http://qrz.lt/qth?" . $kvadratas . "' target='_blank'>" . $masyvas[$row['wal1']][$row['wal2']] . "</a></td>";
    } elseif ($kas == 'lyff') {
        $kvadratas = $row[$kas . "1"] . "-" . str_pad($row[$kas . "2"], 4, "0", STR_PAD_LEFT);
        return "<td class='$tdClass'>" . $kvadratas . "</td>" . "<td class='$tdClass'>" . $masyvas[$row['lyff1']][$row['lyff2']] . "</td>";
    }
}
if ($tmp == 'stats') {
	echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
	echo "<title>".strtoupper($kas)." $tmp</title>";
	echo $google_analytics;
	echo "<link href='".$domenas."style.css' rel='stylesheet' type='text/css' media='screen' /></head><body><div id='container'>";
    echo "<p><a class='b' href='$domenas'>atgal - back</a></p>";
    echo "<font style='font-size: 2.5em'>".strtoupper($kas)." statistics</font>";
	if ($kas == 'lhfa') {
		echo "<p>&nbsp;</p>";
		
		/* LY HUNTER einamųjų metų statistika */
		echo "<h2>LY HUNTER ".date('Y')." TOP $stats_limit</h2>\n";
		 $result = mysqli_query($db,"SELECT `call`, count(distinct concat(`lhfa1`,'-',`lhfa2`)) AS hunter
		 FROM `qso` WHERE `lhfa1` != '' AND `datetime` LIKE '".date('Y')."%' GROUP BY SUBSTRING_INDEX(`call`,'/',1) order by hunter desc LIMIT $stats_limit");

		echo "<table style='width: 100%'>";
		echo "<tr><td class='nuRow'>Pos.</td><td class='nuRow'>" . strtoupper($kas) . " participant</td><td class='nuRow'>Hillforts</td>
			</tr>";

			
		while ($row = @mysqli_fetch_array($result)) {
			$tdClass = ($iTmp % 2) ? "fiRow" : "seRow";
			echo "<tr>\n";
			echo "<td class='$tdClass'>" . ++$iTmp . "</td>\n";
			echo "<td class='$tdClass'><a href='/".$row['call']."/$kas'>" . $row['call'] . "</a></td>\n";
			echo "<td class='$tdClass'>" . $row['hunter'] . "</td>\n";
			echo "</tr>\r\n";

		}
		echo "</table>";
		$iTmp = 0;
		echo "<p>&nbsp;</p>";

		/* LY HUNTER einamųjų metų statistika */

		echo "<h2>ALL TIME DX TOP $stats_limit</h2>\n";
		$result = mysqli_query($db,"SELECT `call`, count(distinct concat(lhfa1,'-',lhfa2)) as hunter
		FROM `qso` WHERE qso.call NOT LIKE 'LY%' AND lhfa1 != '' group by `call` order by hunter desc LIMIT $stats_limit");
				echo "<table style='width: 100%'>";
		echo "<tr><td class='nuRow'>Pos.</td><td class='nuRow'>" . strtoupper($kas) . " participant</td><td class='nuRow'>Hillforts</td>
			</tr>";

		while ($row = @mysqli_fetch_array($result)) {
			$tdClass = ($iTmp % 2) ? "fiRow" : "seRow";
			echo "<tr>\n";
			echo "<td class='$tdClass'>" . ++$iTmp . "</td>\n";
			echo "<td class='$tdClass'><a href='/".$row['call']."/$kas'>" . $row['call'] . "</a></td>\n";
			echo "<td class='$tdClass'>" . $row['hunter'] . "</td>\n";
			echo "</tr>\r\n";
		}
		echo "</table>";
	}
}
else
if (array_key_exists($kas, $names)) {
    echo $headText = "<html>
						<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
						<title>Aktyvuoti ".strtoupper($kas)." ".$names[$kas][0]."</title>".$google_analytics."
						<link href='".$domenas."style.css' rel='stylesheet' type='text/css' media='screen' />
						</head>
						<body>
						<div id='container'>";

    isspreskObjekta($kas);
    echo "<p><a class='b' href='$domenas'>atgal - back</a></p>";
    echo "<h2>Aktyvuoti " . strtoupper($kas) . " " . $names[$kas][0] . "</h2>";
    if ($kas == 'wal'){echo "<iframe src='/wal-mapserver/wal.htm'></iframe>";}
    if ($kas == 'lhfa'){echo "<iframe src='/lhfa-mapserver/lhfa.htm'></iframe>";}
    echo "<table style='width: 100%'>";
    echo "<tr><th>".strtoupper($kas)."</th><th>".$names[$kas][1]."</th><th>QSO</th><th>aktivuota<br/>activated</th><th>paskutinis aktyvavo<br/>last activator</th><th>vėl. data<br/>last date</th></tr>";

    /*$result = mysqli_query($db,"SELECT *, count(*) as sk, ".$kas."1, ".$kas."2, caller, DATE_FORMAT(datetime, '%Y-%m-%d') as datetime
    FROM (SELECT * FROM `qso` WHERE ".$kas."1 != '' ORDER BY datetime DESC ) AS sub GROUP BY ".$kas."1, ".$kas."2");

    */

    $result = mysqli_query($db,"SELECT count(*) as kiek, sum(sk2) as sk, " . $kas . "1, " . $kas . "2, caller, DATE_FORMAT(datetime, '%Y-%m-%d') as datetime FROM (
	SELECT *, count(*) as sk2 FROM (
		SELECT *, IF(POSITION('/' in `caller`) > 0, SUBSTRING(`caller` FROM 1 FOR POSITION('/' in `caller`)-1 ), `caller`) AS caller_ FROM `qso` WHERE `$kas"."1` != '' ORDER BY `datetime` DESC
	) AS sub GROUP BY `$kas"."1`,`$kas"."2`, caller_ ORDER BY `datetime` DESC ) as sub2 GROUP BY `$kas"."1`, `$kas"."2`");


    while ($row = @mysqli_fetch_array($result)) {
        $tdClass = ($iTmp % 2) ? "fiRow" : "seRow";
        echo "<tr>";
        echo parodykObjekta($kas, $tdClass);
        echo "<td class='$tdClass'>" . $row['sk'] . "</td>";
        echo "<td class='$tdClass'>" . $row['kiek'] . "</td>";
        echo "<td class='$tdClass'>" . $row['caller'] . "</td>";
        echo "<td class='$tdClass'>" . $row['datetime'] . "</td>";
        echo "</tr>\r\n";
        $iTmp++;
    }

    echo "</table>";
    echo "<p>&nbsp;</p><p><a class='b' href='$domenas'>atgal - back</a></p>";
} else if (strlen($kas) > 3) {
    echo $headText = "<html>
						<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
						<title>".strtoupper($kas)." aktyvuoti ".strtoupper($prog)." ".$names[$prog][0]."</title>
						<link href='".$domenas."style.css' rel='stylesheet' type='text/css' media='screen' />
						<!-- link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' integrity='sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u' crossorigin='anonymous' -->		
						<script>
							(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;
								i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();
								a=s.createElement(o),m=s.getElementsByTagName(o)[0];
								a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})
							(window,document,'script','//www.google-analytics.com/analytics.js','ga');
							ga('create', 'UA-9264286-3', 'qrz.lt');
							ga('send', 'pageview');
						</script>
						</head>
						<body>
						<div id='container'>";

    $lang = strtolower(substr($kas,0,2));
    $lang = ($lang=='ly'?'ly':'en');

    echo "<p><a class='b' href='$domenas'>atgal - back</a></p>";
    echo "<div style='float: right; width: 140px'>";
		
    echo "<h2>".$texts[$lang]['hunted']." ".$texts[$lang]['objects']."</h2>";
    echo "<p><a class='b' href='$domenas$kas/lhfa'>LHFA</a> &nbsp; <a class='b' href='$domenas$kas/wal'>WAL</a> &nbsp; <a class='b' href='$domenas$kas/lyff'>LYFF</a></p>";
    echo "<h2>".$texts[$lang]['activated']." ".$texts[$lang]['objects']."</h2>";
    echo "<p><a class='b' href='$domenas$kas/lhfa-aktyvuoti'>LHFA</a> &nbsp; <a class='b' href='$domenas$kas/wal-aktyvuoti'>WAL</a> &nbsp; <a class='b' href='$domenas$kas/lyff-aktyvuoti'>LYFF</a></p></div>";

 // Asmeniniai Pasiekimai
 
    echo "<h2>".strtoupper($kas)." ". $texts[$lang]['achievements']."</h2>";

    echo "<table>";
    echo "<tr><th>".$texts[$lang]['program']."</th><th>".$texts[$lang]['hunted']."</th><th>".$texts[$lang]['activated']."</th><th>".$texts[$lang]['total']."</th><th>".$texts[$lang]['diplomas']."</th></tr>";

    echo saukinioStatistika("LHFA", $kas, "lhfa", "fiRow");
    echo saukinioStatistika("WAL", $kas, "wal", "seRow");
    echo saukinioStatistika("LYFF", $kas, "lyff", "fiRow");

    echo "</table>";
	
	/***** taisyta LY2GN 2018-02-15 */

	echo "<h2>Hill-Fort-Hunter Award ".$texts[$lang]['achievements'] ."</h2>";
	echo "<table>";
	echo "<tr><th>".$texts[$lang]['year']."</th><th>".$texts[$lang]['hunted']."</th><th>".$texts[$lang]['activated']."</th><th>".$texts[$lang]['total']."</th></tr>";
	for ($metai=2017; $metai<=date('Y');$metai++){
    $result = mysqli_query($db,
      "SELECT count(distinct concat(`lhfa1`,'-',`lhfa2`)) as count
      FROM `qso`
      WHERE `lhfa1` != '' AND `datetime` LIKE '".$metai."%'
      AND (`call`='".$kas."' OR `call`='".$kas."/P')");
    if ($result)
    {
      $h=$result->fetch_assoc()['count'];
    }
    else
    {
      $h=-1;
    }
    $result = mysqli_query($db,
      "SELECT count(distinct concat(`lhfa1`,'-',`lhfa2`))
      FROM `qso` WHERE `lhfa1` != '' AND `datetime` LIKE '".$metai."%'
      AND (`caller`='".$kas."' or `caller`='".$kas."/P' or
      `operator`='".$kas."' or `operator`='".$kas."/P')");
    if ($result)
    {
      $a=$result->fetch_assoc()['count'];
    }
    else
    {
      $a=-1;
    }
    $s=$h+$a;
		echo "<tr><td class='nuRow'>$metai</td><td>$h</td><td>$a</td><td>$s</td></tr>";
	}
	echo "</table>";
	/***** taisyta LY2GN 2018-02-15  pabaiga*/
	
    list($prog, $pType) = explode("-", $prog);

    if (array_key_exists($prog, $names) && $pType == 'aktyvuoti') {
        echo "<h2>" . strtoupper($kas) . " aktyvuoti " . strtoupper($prog) . " " . $names[$prog][0] . "</h2>";
        if ($prog == 'wal'){echo "<iframe src='/wal-mapserver/wal.htm?$kas,caller'></iframe>";}
        if ($prog == 'lhfa'){echo "<iframe src='/lhfa-mapserver/lhfa.htm?$kas,caller'></iframe>";}
		isspreskObjekta($prog);
		echo "<table style='width: 100%'>";
        echo "<tr><th>".strtoupper($prog)."</th><th>".$names[$prog][1]."</th><th>Ryšių skaičius - QSO</th><th>Data</th><th>Šaukinys - Calsign</th></tr>";
        $result = mysqli_query($db,"SELECT *, COUNT(*) as sk, DATE_FORMAT(datetime, '%Y-%m-%d') as datetime, caller
				FROM (SELECT * FROM `qso` WHERE (`caller` LIKE '$kas' or `caller` LIKE '$kas"."/%' or `operator` LIKE '$kas' or `operator` LIKE '$kas"."/%') and `$prog"."1` != '' ORDER BY `datetime` DESC ) AS sub GROUP BY `$prog"."1`,`$prog"."2`, `caller`, DATE_FORMAT(`datetime`, '%Y-%m-%d')");
       while ($row = mysqli_fetch_array($result)) {
           $tdClass = ($iTmp % 2) ? "fiRow" : "seRow";
            echo "<tr>";
            echo parodykObjekta($prog, $tdClass);
            echo "<td class='$tdClass'>" . $row['sk'] . "</td>";
            echo "<td class='$tdClass'>" . $row['datetime'] . "</td>";
            echo "<td class='$tdClass'>" . $row['caller'] . "</td>";
            echo "</tr>\r\n";
            $iTmp++;
        }
        echo "</table>";
    } elseif (array_key_exists($prog, $names)) {
        echo "<h2>" . strtoupper($kas) . " sumedžioti " . strtoupper($prog) . " " . $names[$prog][0] . "</h2>";
        if ($prog == 'wal'){echo "<iframe src='/wal-mapserver/wal.htm?$kas'></iframe>";}
        if ($prog == 'lhfa'){echo "<iframe src='/lhfa-mapserver/lhfa.htm?$kas'></iframe>";}

        isspreskObjekta($prog);

        echo "<table style='width: 100%'>";
        echo "<tr><th>".strtoupper($prog)."</th><th>".$names[$prog][1]."</th><th>Data</th><th>Aktyvavo</th><th>Diapaz.</th><th>Mod.</th></tr>";

        $result = mysqli_query($db,"SELECT *, DATE_FORMAT(datetime, '%Y-%m-%d') as datetime
FROM (SELECT * FROM `qso` WHERE (`call` LIKE '$kas' or `call` LIKE '$kas" . "/" . "%') and " . $prog . "1 != '' ORDER BY datetime DESC ) AS sub GROUP BY " . $prog . "1, " . $prog . "2");

        while ($row = mysqli_fetch_array($result)) {

            $tdClass = ($iTmp % 2) ? "fiRow" : "seRow";

            echo "<tr>";
            echo parodykObjekta($prog, $tdClass);
            echo "<td class='$tdClass'>" . $row['datetime'] . "</td>";
            echo "<td class='$tdClass'>" . $row['caller'] . "</td>";
            echo "<td class='$tdClass'>" . $row['band'] . "</td>";
            echo "<td class='$tdClass'>" . $row['mode'] . "</td>";

            echo "</tr>\r\n";

            $iTmp++;
        }

        echo "</table>";

    }
} else {
    if (strlen($saukinysP) > 3) {
        header("Location: " . $domenas . str_replace("/", "-", $saukinysP));

    }
    $handle = @fopen("index.cache", "r");
    list($cDate, $contents) = @explode("###", @fread($handle, @filesize("index.cache")));
    @fclose($handle);
    $timestamp = mysqli_query($db,"SELECT UNIX_TIMESTAMP(datetimenow) as datetime FROM qso ORDER by datetime DESC LIMIT 1")->fetch_assoc["datetime"];
 	
	if ($cDate < $timestamp) {
        ob_start();
        echo $headText = "<html>
							<head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
							<title>LHFA, WAL, LYFF aktyvavimų sąrašai</title>
							<link href='".$domenas."style.css' rel='stylesheet' type='text/css' media='screen' />
							<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;
									i[r]=i[r]||function(){		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();
									a=s.createElement(o),		  m=s.getElementsByTagName(o)[0];
									a.async=1;a.src=g; m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
									ga('create', 'UA-9264286-3', 'qrz.lt');
									ga('send', 'pageview');
							</script>
							</head>
							<body>
							<div id='container'>";

        echo "<form action='' method='post'>";
        echo "<h2>Registruotų ryšių peržiūra - View Your registered QSO</h2>";
        echo "<p>Šaukinys - Calsign: <input name='saukinys' value='". $_POST['tDateFrom'] ."' style='width: 146px; margin: 0 6px 0 2px'>";
		echo "<input type='submit' value='Rodyk - Show '></p>";
		echo "</form>";
		echo "<h2>Aktyvuoti objektai - Activated objects</h2>";
		echo "<p>Parodyti visus - Show all: &nbsp;
			 <a class='b' href='" . $domenas . "lhfa'>LHFA</a> &nbsp;
			 <a class='b' href='" . $domenas . "wal'>WAL</a> &nbsp;
			 <a class='b' href='" . $domenas . "lyff'>LYFF</a></p>";
        
		echo "<h2>Dirbusių šaukinių skaičius  - Number of worked Calsigns</h2>";
        echo "<table>";
		echo "<tr><th>Program</th><th>Activators</th><th>LY hunters</th><th>DX hunters</th></tr>";
		echo pirmoPslAktyvuotojuStatistika("LHFA","lhfa1","fiRow");
		echo pirmoPslAktyvuotojuStatistika("WAL","wal1","seRow");
		echo pirmoPslAktyvuotojuStatistika("LYFF","lyff1","fiRow");
        echo "</table>";
		
        echo "<h2>Aktyvuoti objektai - Activated objects</h2>";
        echo "<table style='width: 100%'>";
        echo "<tr><th>Program</th><th>PHONE</th><th>CW</th><th>DIGI</th><th>160m</th><th>80m</th><th>40m</th><th>30m</th><th>20m</th><th>17m</th><th>15m</th><th>12m</td><th>10m</th><th>6m</th><th>2m</th><th>70cm</th><th>3cm</th></tr>";
        echo pirmoPslStatistika("LHFA", "lhfa1", "lhfa2", "fiRow");
        echo pirmoPslStatistika("WAL", "wal1", "wal2", "seRow");
        echo pirmoPslStatistika("LYFF", "lyff1", "lyff2", "fiRow");
        echo "</table>";
 
		echo "<h2>Registruoti ryšiai - Registrated QSO</h2>";
        echo "<table style='width: 100%'>";
 		echo "<tr><th>Program</th><th>PHONE</th><th>CW</th><th>DIGI</th><th>160m</th><th>80m</th><th>40m</th><th>30m</th><th>20m</th><th>17m</th><th>15m</th><th>12m</td><th>10m</th><th>6m</th><th>2m</th><th>70cm</th><th>3cm</th></tr>";
        echo pirmoPslStatistika("LHFA", "lhfa1", "", "fiRow");
        echo pirmoPslStatistika("WAL", "wal1", "", "seRow");
        echo pirmoPslStatistika("LYFF", "lyff1", "", "fiRow");
        echo "</table>";
		echo "<h3>".LastUpdate()."</h3>";
        echo $content = ob_get_clean();
        $fp = fopen("index.cache", 'w');
        fwrite($fp, $timestamp . "###" . $content);
        fclose($fp);
    } else {
       echo $contents;
    }
}
echo $endText = "</div></body></html>";
?>
