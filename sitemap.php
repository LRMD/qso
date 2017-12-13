<?php
/*
    Pakeitimai: isvalyti passwordai, pataisytos zymos is <? i <?php

*/
	header ("Content-Type:text/xml");
	echo'<?xml version="1.0" encoding="UTF-8"?>';
	
?><urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<url><loc>http://qso.qrz.lt/</loc><changefreq>weekly</changefreq><priority>1.00</priority></url>
<url><loc>http://qso.qrz.lt/lhfa</loc><changefreq>weekly</changefreq><priority>0.80</priority></url>
<url><loc>http://qso.qrz.lt/wal</loc><changefreq>weekly</changefreq><priority>0.80</priority></url>
<url><loc>http://qso.qrz.lt/lyff</loc><changefreq>weekly</changefreq><priority>0.80</priority></url>
<?php
	
	mysql_connect("localhost", "username", "password") or die("Bandykite veliau");
	mysql_select_db("qrzlt_logs") or die(mysql_error());
	
	if(version_compare(@mysql_get_server_info(), '4.1'))
	{
		mysql_query("SET CHARACTER SET utf8");
		mysql_query("SET NAMES 'utf8'");
	}
	
	$visi = array("lhfa","wal","lyff","lhfa-aktyvuoti","wal-aktyvuoti","lyff-aktyvuoti");
	$handle = @fopen("sitemap.cache", "r");
	list($cDate, $contents) = @explode("###", @fread($handle, @filesize("sitemap.cache")));
	@fclose($handle);
	$timestamp = mysql_result(mysql_query("SELECT UNIX_TIMESTAMP(datetimenow) as datetime FROM qso ORDER by datetime DESC LIMIT 1"), 0, "datetime");
	
	if($cDate < $timestamp) {
			
		ob_start();
			
		$result = mysql_query("SELECT DISTINCT(if(
									LENGTH(SUBSTRING_INDEX(`call`, '/', 1)) >
									LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(`call`, '/', 2), '/', -1)),
									SUBSTRING_INDEX(`call`, '/', 1),
									SUBSTRING_INDEX(SUBSTRING_INDEX(`call`, '/', 2), '/', -1)
								)) as `call` FROM `qso` WHERE `call` LIKE 'LY%' ORDER by `call`");
		while($row=@mysql_fetch_array($result)) { 
			foreach($visi as $value) {
				echo"<url><loc>http://qso.qrz.lt/".strtolower($row[call])."/$value/</loc><changefreq>weekly</changefreq><priority>0.64</priority></url>\r\n";
			}	
		}
		
		echo $content = ob_get_clean();
			
		$fp = fopen("sitemap.cache", 'w');
		fwrite($fp, $timestamp."###".$content);
		fclose($fp);
	
	}
	else {
		echo $contents;
	}
	
?>
</urlset>
