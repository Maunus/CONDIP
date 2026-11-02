<?PHP ob_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="es">
<head>
<title>CoNDiP</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta name="author" content="Michael Thomsen. Una Canger.">
<meta name="revised" content="MT20071001; MT20080416; ">
<meta name="description" content="CoNDiP">
<meta name="generator" content="M-sys Edit">
<meta name="keywords" content="CoNDiP, Nahuatl, Nahua, Language, Translate, English, Spanish, Aztec, Culture, Dictionary, Michael Thomsen, Una Canger, Book, Codex, Word, Root, Text">
<link rel="stylesheet" href="condip.css" type="text/css" media="all">
</head>
<body>

<script type="text/javascript">
function hand() {
  document.body.style.cursor = 'pointer';
}
function norm() {
  document.body.style.cursor = 'default';
}
function cliRod(i) {
  eval("document.selRod2.aktRod").value = i;
  eval("document.selRod2.knap").click();
}
function cliOrd(i) {
  eval("document.selOrd2.aktOrd").value = i;
  eval("document.selOrd2.knap").click();
}
function cliSen(i, j) {
  eval("document.selSen2.aktSen").value = i;
  eval("document.selSen2.aktWno").value = j;
  eval("document.selSen2.knap").click();
}
function cliFindRod() {
  eval("document.findRod2.sk").value = document.getElementById("sk1").value;
  eval("document.findRod2.knap").click();
}
function cliFindOrd() {
  eval("document.findOrd2.sk").value = document.getElementById("sk1").value;
  eval("document.findOrd2.knap").click();
}
function cliFindTxt() {
  eval("document.findTxt2.sk").value = document.getElementById("sk1").value;
  eval("document.findTxt2.knap").click();
}
function pre(e, c) {
var keynum;
var r;
  r = ' ';
  if ( window.event ) { keynum = e.keyCode } // IE
  else if ( e.which ) { keynum = e.which } // Netscape/Firefox/Opera
  if ( keynum == 13 ) {
    eval("document.find2.sk").value = document.getElementById("sk1").value;
    eval("document.find2.knap").click();
  }
      else if ( (c == 'T') && (keynum == 37) ) { r = 'v'; }
      else if ( (c == 'T') && (keynum == 38) ) { r = 'o'; }
      else if ( (c == 'T') && (keynum == 39) ) { r = 'h'; }
      else if ( (c == 'T') && (keynum == 40) ) { r = 'n'; }
  if ( r != ' ' ) {
    eval("document.selSen3.aktSen").value = r;
    eval("document.selSen3.knap").click();
  }
}
function rez() {
  if ( (typeof(document.body.offsetWidth) != 'undefined') && (document.body.offsetWidth < 802) ) {
    window.resizeBy(805-document.body.offsetWidth, 0);
  }
  if ( (typeof(window.innerWidth) != 'undefined') && (window.innerWidth < 802) ) {
    window.resizeBy(805-document.body.offsetWidth, 0);
  }
}
function cliSpan(i) {
  var del = document.getElementById("del" + i);
  var inf = document.getElementById("inf" + i);
//  alert(del.innerHTML);
//  alert(document.all.del1.innerHTML.substr(0,1));
  if ( del.innerHTML.substr(0,1) == "+" ) {
    inf.style.display = "";
    del.innerHTML = "&#8211;" + del.innerHTML.substr(1);
  } else {
    inf.style.display = "none";
    del.innerHTML = "+" + del.innerHTML.substr(1);
  }
}
function maildk(u, d) {
  document.writeln('<a href="mailto:'+u+'@'+d+'.dk">'+u+'@'+d+'.dk<\/a>');
}
</script>

<?PHP
session_start();
require_once("utils.php");
require_once("condip_i.php");

//$DbOpen = 0;
//$link = false;
$db;

$tety = array("tra" => "Transcription", "pal" => "Paleography", "eng" => "English translation");
$bogt = array("BANC" => "Bancroft Dialogues", "CAUB" => "Codex Aubin", "CF02" => "Codex Florentino 2. book", "CF11" => "Codex Florentino 11. book", "CM8B" => "Códice Matritense", "LD21" => "Land document #21", "MOLSA" => "Molia");
$clas = array('noun', 'verb', 'pronoun', 'numeral', 'rel. noun', 'adverb', 'adjective', 'see', 'a', 'all');
$gram = array('', 'toponym', 'proper name', 'ethnonym', 'zool.', 'bot.', 'min.', 'astr.', 'G8', 'obl. poss.', 'intransitive', 'tr 1', 'tr 2', 'tr 3', 'tr 4', 'imp.agent', 'imp.ag. 1', 'imp.ag. 2', 'imp.ag. 3', 'reflexive');
$plur = array('', '+me´', '+tin', '+´', '=ke+´', 'R', 'R +me´', 'R +tin', 'R +´', 'R =ke+´', '*', '+me´ *', '+tin *', '+´ *', '=ke+´ *', 'R *', 'R +me´ *', 'R +tin *', 'R +´ *');
$traf = array('<',    '>',    '´',      'ä',      'ë',      'ï',      'ö',      '£',      '²',      '¹',       '³',      '¸',       'á',      'ç',      'é',      'í',      'ñ',      'ó',      'ú',      'ý',      '»');
for ( $i = 0; $i < count($traf); $i++ ) { $traf[$i] = utf8_decode($traf[$i]); }
$trat = array('&lt;', '&gt;', '&#704;', '&#257;', '&#275;', '&#299;', '&#333;', '&#955;', '&#162;', 'k&#695;', '&#269;', '&#8776;', '&#225;', '&#231;', '&#233;', '&#237;', '&#241;', '&#243;', '&#250;', '&#253;', '&#187;');
$palf = array('<',    '>',    'á',      'ç',      'é',      'í',      'ñ',      'ó',      'ú',      '¶',      '¨',      '¸',       'Ç',      'à',      'ä',      'è',      'ë',      'ì',      'ï',      'ò',      'ö',      'ü',      'ý',      '´',     '»');
for ( $i = 0; $i < count($traf); $i++ ) { $palf[$i] = utf8_decode($palf[$i]); }
$palt = array('&lt;', '&gt;', '&#225;', '&#231;', '&#233;', '&#237;', '&#241;', '&#243;', '&#250;', '&#182;', '&#168;', '&#8776;', '&#199;', '&#224;', '&#228;', '&#232;', '&#235;', '&#236;', '&#239;', '&#242;', '&#246;', '&#252;', '&#253;', '&#39;', '&#187;');

function openDb() {
  global $db;
//  global $DbOpen, $link;
//  if ( $DbOpen == 0 ) {
//  if ( !$link ) {
//    mysql_select_db("compromi_eu_db") or die(mysql_error());
//    if ( !$link ) { die('Could not connect: ' . mysql_error()); }
//    mysql_select_db("Troldma_condip") or die(mysql_error());
//    mysql_select_db("compromi_eu_db") or die(mysql_error());
//    mysql_select_db("quinden_michael") or die(mysql_error());
//    $DbOpen = 1;
//  }
  require_once("config.php");
  $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if ($db->connect_errno) {
    exit("Connect failed: %s\n" . $db->connect_error);
  }
}

function closeDb() {
  global $db;
  $db->close();
//  if ( $DbOpen == 1 ) {    $link = !mysql_close($link);  }
}

function tra($si) {
  global $traf, $trat;
  return utf8_encode(str_replace($traf, $trat, $si));
}

function pal($si) {
  global $palf, $palt;
  return utf8_encode(str_replace($palf, $palt, $si));
}

function startNo($sen) {
  global $db;
  $quS = mysqli_query($db, "SELECT t.tra, t.pal FROM test17e t, test17e d WHERE d.id = " . $sen .
    " AND t.bid = d.bid AND t.fol = d.fol AND t.sno = d.sno AND t.fot = '+' ")
  or die(mysqli_error($db));
  $poS = mysqli_fetch_assoc($quS);
  if ( $poS['tra'] != '' ) { $ar = explode(' ', $poS['tra']); }
  else { $ar = explode(' ', $poS['pal']); }
  return count($ar);
}

function VisOrd($l, $n, $fof, $sen) {
  if ( $fof == '+' ) { $n = $n - startNo($sen); }
  $st = $l . ' ';
  $i = 0;
  $a = true;
  $sr = '';
  do {
    $p = strpos($st, ' ');
    $o = substr($st, 0, $p + 1);
    if ( $o == '&lt; ' ) { $a = true; }
    elseif ( $o == '&gt; ' ) { $a = true; }
    elseif ( $o == '{ ' ) { $a = false; }
    elseif ( $o == '} ' ) { $a = true; }
    else { $i++; }
    if ( $a && ($i == $n) ) { $sr .= '<b>' . $o . '</b>'; }
	else { $sr .= $o; }
    $st = substr($st, $p + 1);
  } while ( ($i <= $n) && ($st != '') );
  $sr .= $st;
  return trim($sr);
}

function visRod1() {
  global $db;
//  $quRod = mysql_query($db, "SELECT * FROM test17b WHERE id = " . $_SESSION['aktRod']) or die(mysql_error());
  $quRod = mysqli_query($db, "SELECT * FROM test17b WHERE id = " . $_SESSION['aktRod']) or die(200);
  $poRod = mysqli_fetch_assoc($quRod);
  echo '<tr><td class="tdRod">';
  echo '<table id="tbrod1" border="0" width="660px" align="center" cellspacing="0px" style="border: 2px solid #AAAAAA">';
  echo '<thead><tr>';
  echo '<td class="head_blaa2" width="656px" colspan="5">Root information</td>';
  echo '</tr><tr>';
  echo '<td class="head_graa" width="128px" align="left">Transcription</td>';
  echo '<td class="head_graa" width="128px" align="left">Normalized</td>';
  echo '<td class="head_graa" width="128px" align="left">Orthography</td>';
  echo '<td class="head_graa" width="126px" align="left">English gloss</td>';
  echo '<td class="head_graa" width="126px" align="left">Spanish gloss</td>';
  echo '</tr></thead>';
  echo '<tbody><tr>';
  echo '<td class="post_pasiv" width="128px" align="left" style="padding-left: 4px; border: 2px solid #AAAAAA">' . tra($poRod["tra"]) . '</td>';
  echo '<td class="post_pasiv" width="128px" align="left" style="padding-left: 4px; border: 2px solid #AAAAAA">' . tra($poRod["nor"]) . '</td>';
  echo '<td class="post_pasiv" width="128px" align="left" style="padding-left: 4px; border: 2px solid #AAAAAA">' . $poRod["ort"] . '</td>';
  echo '<td class="post_pasiv" width="126px" align="left" style="padding-left: 4px; border: 2px solid #AAAAAA">' . $poRod["eng"] . '</td>';
  echo '<td class="post_pasiv" width="126px" align="left" style="padding-left: 4px; border: 2px solid #AAAAAA">' . $poRod["spa"] . '</td></tr>';
  echo '<tr><td width="652px" colspan="5" align="left" style="padding-left: 2px; border: 2px solid #AAAAAA">' . nl2br($poRod["com"]) . '</td></tr>';
  echo '</tbody>';
  echo '</table>';
  echo '</td></tr>';
  $quOrd = mysqli_query($db, "SELECT w.id, w.ort, w.eng, w.spa FROM test17c w, test17cb wr WHERE w.id = wr.wid and wr.rid = " . $_SESSION['aktRod'] . " ORDER BY w.ort") or die(mysqli_error($db));
  echo '<tr><td class="tdRod">';
  echo '<table id="tbrw2" border="0" width="660px" align="center" cellspacing="0px" style="border: 2px solid #AAAAAA">';
  echo '<thead><tr>';
  echo '<td class="head_blaa2" width="656px" colspan="3">Word list</td>';
  echo '</tr><tr>';
  echo '<td class="head_graa" width="128px" align="left">Word</td>';
  echo '<td class="head_graa" width="256px" align="left">English gloss</td>';
  echo '<td class="head_graa" width="256px" align="left">Spanish gloss</td>';
  echo '</tr></thead>';
//  echo '<tfoot>';
//  echo '</tfoot>';
  echo '<tbody onmouseover="hand()" onmouseout="norm()">';
  while ( $poOrd = mysqli_fetch_assoc($quOrd) ) {
    if ( !isset($_SESSION['aktOrd'])  ) { $_SESSION['aktOrd'] = $poOrd["id"]; }
    echo '<tr onclick="cliOrd(' . $poOrd["id"] . ')">';
    if ( $_SESSION['aktOrd'] == $poOrd["id"] ) {
	  echo '<td class="post_aktiv" width="128px" align="left">' . $poOrd["ort"] . '</td>';
      echo '<td class="post_aktiv" width="256px" align="left">' . $poOrd["eng"] . '</td>';
      echo '<td class="post_aktiv" width="256px" align="left">' . $poOrd["spa"] . '</td></tr>';
    } else {
	  echo '<td class="post_pasiv" width="128px" align="left">' . $poOrd["ort"] . '</td>';
      echo '<td class="post_pasiv" width="256px" align="left">' . $poOrd["eng"] . '</td>';
      echo '<td class="post_pasiv" width="256px" align="left">' . $poOrd["spa"] . '</td></tr>';
    }
  }
  echo '</tbody>';
  echo '</table>';
  echo '</td></tr>';
}

function visRod2() {
  global $db;
  $quRod = mysqli_query($db, "SELECT id, ort, eng, spa FROM test17b WHERE " .
//  $quRod = $db->query("SELECT id, ort, eng, spa FROM test17b WHERE " .
         "tra = '" . $_SESSION['sk'] . "' OR " .
  		 "nor = '" . $_SESSION['sk'] . "' OR " .
  		 "ort = '" . $_SESSION['sk'] . "' OR " .
  		 "sea = '" . $_SESSION['sk'] . "'" .
		 " ORDER BY sea, tra") or die(mysqli_error($db));
  echo '<tr><td class="tdRod">';
  echo '<table id="tbrod2" border="0" width="660px" align="center" cellspacing="0px" style="border: 2px solid #AAAAAA">';
  echo '<thead><tr>';
  echo '<td class="head_blaa1" width="656px" colspan="3">Root dictonary</td>';
  echo '</tr><tr>';
  echo '<td class="head_graa" width="128px" align="left">Root</td>';
  echo '<td class="head_graa" width="256px" align="left">English gloss</td>';
  echo '<td class="head_graa" width="256px" align="left">Spanish gloss</td>';
  echo '</tr></thead>';
//  echo '<tfoot>';
//  echo '</tfoot>';
  echo '<tbody onmouseover="hand()" onmouseout="norm()">';
  while ( $poRod = mysqli_fetch_assoc($quRod) ) {
//  while ( $poRod = $quRod->fetch_assoc() ) { // $poRod[] = $poRodo; 
    if ( !isset($_SESSION['aktRod']) ) { $_SESSION['aktRod'] = $poRod["id"]; }
    echo '<tr onclick="cliRod(' . $poRod["id"] . ')">';
    if ( $_SESSION['aktRod'] == $poRod["id"] ) {
	  echo '<td class="post_aktiv" width="128px" align="left">' . $poRod["ort"] . '</td>';
      echo '<td class="post_aktiv" width="256px" align="left">' . $poRod["eng"] . '</td>';
      echo '<td class="post_aktiv" width="256px" align="left">' . $poRod["spa"] . '</td></tr>';
    } else {
	  echo '<td class="post_pasiv" width="128px" align="left">' . $poRod["ort"] . '</td>';
      echo '<td class="post_pasiv" width="256px" align="left">' . $poRod["eng"] . '</td>';
      echo '<td class="post_pasiv" width="256px" align="left">' . $poRod["spa"] . '</td></tr>';
    }
  }
  if ( !isset($_SESSION['aktRod'])  ) { $_SESSION['aktRod'] = 0; }
  echo '</tbody>';
  echo '</table>';
  echo '</td></tr>';
  visRod1();
}

function visOrd1() {
  global $db, $clas, $gram, $plur;
  $quOrd = mysqli_query($db, "SELECT * FROM test17c WHERE id = " . $_SESSION['aktOrd']) or die(mysqli_error($db));
  $poOrd = mysqli_fetch_assoc($quOrd);
  echo '<tr><td class="tdOrd">';
  echo '<table id="tbord1" border="0" width="660px" align="center" cellspacing="0px" style="border: 2px solid #AAAAAA">';
  echo '<thead><tr>';
  echo '<td class="head_blaa2" width="656px" colspan="7">Word information</td>';
  echo '</tr><tr>';
  echo '<td class="head_graa" width="96px" align="left">Transcription</td>';
  echo '<td class="head_graa" width="96px" align="left">Normalized</td>';
  echo '<td class="head_graa" width="96px" align="left">Orthography</td>';
  echo '<td class="head_graa" width="85px" align="left">Grammer</td>';
  echo '<td class="head_graa" width="85px" align="left">Class</td>';
  if ( $poOrd["cla"] == 0 ) {
    echo '<td class="head_graa" width="85px" align="left">Plural</td>';
    echo '<td class="head_graa" width="85px" align="left">Possessed</td>';
  } elseif ( $poOrd["cla"] == 1 ) {
    echo '<td class="head_graa" width="85px" align="left">-</td>';
    echo '<td class="head_graa" width="85px" align="left">Perfect</td>';
  } else {
    echo '<td class="head_graa" width="85px" align="left">-</td>';
    echo '<td class="head_graa" width="85px" align="left">-</td>';
  }
  echo '</tr></thead>';
  echo '<tbody><tr>';
  echo '<td class="post_pasiv" width="116px" align="left">' . tra($poOrd["tra"]) . '</td>';
  echo '<td class="post_pasiv" width="96px" align="left">' . tra($poOrd["nor"]) . '</td>';
  echo '<td class="post_pasiv" width="96px" align="left">' . $poOrd["ort"] . '</td>';
  echo '<td class="post_pasiv" width="80px" align="left">' . $gram[$poOrd["gra"]] . '</td>';
  echo '<td class="post_pasiv" width="80px" align="left">' . $clas[$poOrd["cla"]] . '</td>';
  if ( $poOrd["cla"] == 0 ) {
    echo '<td width="80px" align="left" style="padding-left: 4px; border: 2px solid #AAAAAA">' . $plur[$poOrd["plu"]] . '</td>';
  } else {
    echo '<td width="80px" align="left" style="padding-left: 4px; border: 2px solid #AAAAAA">' . $plur[0] . '</td>';
  }
  echo '<td width="80px" align="left" style="padding-left: 4px; border: 2px solid #AAAAAA">' . tra($poOrd["per"]) . '</td></tr>';
  if ( $poOrd["cla"] == 7 ) {
    $quOrd2 = mysqli_query($db, "SELECT * FROM test17c WHERE id = " . $poOrd["sew"]);
    $poOrd2 = mysqli_fetch_assoc($quOrd2);
    echo '<tr onclick="cliOrd(' . $poOrd["sew"] . ')" onmouseover="hand()" onmouseout="norm()">';
    echo '<td width="96px" align="left" style="padding-left: 4px; border: 2px solid #75A900">' . tra($poOrd2["tra"]) . '</td>';
    echo '<td width="96px" align="left" style="padding-left: 4px; border: 2px solid #75A900">' . tra($poOrd2["nor"]) . '</td>';
    echo '<td width="96px" align="left" style="padding-left: 4px; border: 2px solid #75A900">' . $poOrd2["ort"] . '</td>';
    echo '<td width="85px" align="left" style="padding-left: 4px; border: 2px solid #75A900">' . $gram[$poOrd2["gra"]] . '</td>';
    echo '<td width="85px" align="left" style="padding-left: 4px; border: 2px solid #75A900">' . $clas[$poOrd2["cla"]] . '</td>';
    if ( $poOrd2["cla"] == 0 ) {
      echo '<td width="85px" align="left" style="padding-left: 4px; border: 2px solid #75A900">' . $plur[$poOrd2["plu"]] . '</td>';
    } else {
      echo '<td width="85px" align="left" style="padding-left: 4px; border: 2px solid #75A900">' . $plur[0] . '</td>';
    }
    echo '<td width="85px" align="left" style="padding-left: 4px; border: 2px solid #75A900">' . tra($poOrd2["per"]) . '</td></tr>';
  }
  echo '<tr><td width="652px" colspan="7" align="left" style="padding-left: 2px; border: 2px solid #AAAAAA">' . nl2br($poOrd["com"]) . '</td></tr>';
  echo '</tbody>';
  echo '</table>';
  echo '</td></tr>';
  $quRod = mysqli_query($db, "SELECT r.id, r.ort, r.eng, r.spa FROM test17b r, test17cb wr WHERE r.id = wr.rid and wr.wid = " . $_SESSION['aktOrd'] . " ORDER BY r.ort") or die(mysqli_error($db));
  echo '<tr><td class="tdOrd">';
  echo '<table id="tbrw2" border="0" width="660px" align="center" cellspacing="0px" style="border: 2px solid #AAAAAA">';
  echo '<thead><tr>';
  echo '<td class="head_blaa2" width="656px" colspan="3">Root list</td>';
  echo '</tr><tr>';
  echo '<td class="head_graa" width="128px" align="left">Root</td>';
  echo '<td class="head_graa" width="256px" align="left">English gloss</td>';
  echo '<td class="head_graa" width="256px" align="left">Spanish gloss</td>';
  echo '</tr></thead>';
  echo '<tbody onmouseover="hand()" onmouseout="norm()">';
  while ( $poRod = mysqli_fetch_assoc($quRod) ) {
    if ( !isset($_SESSION['aktRod'])  ) { $_SESSION['aktRod'] = $poRod["id"]; }
    echo '<tr onclick="cliRod(' . $poRod["id"] . ')">';
    if ( $_SESSION['aktRod'] == $poRod["id"] ) {
	  echo '<td class="post_aktiv" width="128px" align="left">' . $poRod["ort"] . '</td>';
      echo '<td class="post_aktiv" width="256px" align="left">' . $poRod["eng"] . '</td>';
      echo '<td class="post_aktiv" width="256px" align="left">' . $poRod["spa"] . '</td></tr>';
    } else {
	  echo '<td class="post_pasiv" width="128px" align="left">' . $poRod["ort"] . '</td>';
      echo '<td class="post_pasiv" width="256px" align="left">' . $poRod["eng"] . '</td>';
      echo '<td class="post_pasiv" width="256px" align="left">' . $poRod["spa"] . '</td></tr>';
    }
  }
  echo '</tbody>';
  echo '</table>';
  echo '</td></tr>';
  echo '<tr><td class="tdOrd">';
  echo '<table id="tbws2" border="0" width="660px" align="center" cellspacing="0px" style="border: 2px solid #AAAAAA">';
  echo '<thead><tr>';
  echo '<td class="head_blaa2" width="656px" colspan="2">Sentence list</td>';
  echo '</tr><tr>';
  echo '<td class="head_graa" width="64px" align="left">Book</td>';
  echo '<td class="head_graa" width="576px" align="left">Text</td>';
  echo '</tr></thead>';
  echo '<tbody onmouseover="hand()" onmouseout="norm()">';
  $quSen = mysqli_query($db, "SELECT t.id, b.bog, t.fol, t.sno, k.wno, t.fof, t.tra AS lin FROM test17cde k, test17e t, test17d b WHERE k.wid = " . $_SESSION['aktOrd'] . " AND t.tra <> '' AND k.tid = t.id AND k.bid = b.id " .
                 "UNION SELECT t.id, b.bog, t.fol, t.sno, k.wno, t.fof, t.pal AS lin FROM test17cde k, test17e t, test17d b WHERE k.wid = " . $_SESSION['aktOrd'] . " AND t.tra = ''  AND k.tid = t.id AND k.bid = b.id ") or die(mysqli_error($db));
  while ( $poSen = mysqli_fetch_assoc($quSen) ) {
    if ( !isset($_SESSION['aktSen'])  ) { $_SESSION['aktSen'] = $poSen["id"]; }
    echo '<tr onclick="cliSen(' . $poSen["id"] . ', ' . $poSen["wno"] . ')">';
    if ( $poSen["bog"] == 'MOLSA' ) { $li = pal($poSen["lin"]); } else { $li = tra($poSen["lin"]); }
    if ( $_SESSION['aktSen'] == $poSen["id"] ) {
	  echo '<td class="post_aktiv" width="64px" align="left">' . $poSen["bog"] . '</td>';
      echo '<td class="post_aktiv_sub" width="576px" align="left">' . VisOrd($li, $poSen["wno"], $poSen["fof"], $poSen["id"]) . '</td></tr>';
    } else {
	  echo '<td class="post_pasiv" width="64px" align="left">' . $poSen["bog"] . '</td>';
      echo '<td class="post_pasiv" width="576px" align="left">' . VisOrd($li, $poSen["wno"], $poSen["fof"], $poSen["id"]) . '</td></tr>';
    }
  }
  echo '</tbody>';
  echo '</table>';
  echo '</td></tr>';
}

function visOrd2() {
  global $db;
  $quRod = mysqli_query($db, "SELECT id, ort, eng, spa FROM test17c WHERE " .
  		 "tra = '" . $_SESSION['sk'] . "' OR " .
  		 "nor = '" . $_SESSION['sk'] . "' OR " .
  		 "ort = '" . $_SESSION['sk'] . "' OR " .
  		 "sea = '" . $_SESSION['sk'] . "'" .
		 " ORDER BY sea, tra") or die(mysqli_error($db));
  echo '<tr><td class="tdOrd">';
  echo '<table id="tbord2" border="0" width="660px" align="center" cellspacing="0px" style="border: 2px solid #AAAAAA">';
  echo '<thead><tr>';
  echo '<td class="head_blaa1" width="656px" colspan="3">Word dictonary</td>';
  echo '</tr><tr>';
  echo '<td class="head_graa" width="128px" align="left">Word</td>';
  echo '<td class="head_graa" width="256px" align="left">English gloss</td>';
  echo '<td class="head_graa" width="256px" align="left">Spanish gloss</td>';
  echo '</tr></thead>';
  echo '<tbody onmouseover="hand()" onmouseout="norm()">';
  while ( $poRod = mysqli_fetch_assoc($quRod) ) {
    if ( !isset($_SESSION['aktOrd'])  ) { $_SESSION['aktOrd'] = $poRod["id"]; }
    echo '<tr onclick="cliOrd(' . $poRod["id"] . ')">';
    if ( $_SESSION['aktOrd'] == $poRod["id"] ) {
	  echo '<td class="post_aktiv" width="128px" align="left">' . $poRod["ort"] . '</td>';
      echo '<td class="post_aktiv" width="256px" align="left">' . $poRod["eng"] . '</td>';
      echo '<td class="post_aktiv" width="256px" align="left">' . $poRod["spa"] . '</td></tr>';
    } else {
	  echo '<td class="post_pasiv" width="128px" align="left">' . $poRod["ort"] . '</td>';
      echo '<td class="post_pasiv" width="256px" align="left">' . $poRod["eng"] . '</td>';
      echo '<td class="post_pasiv" width="256px" align="left">' . $poRod["spa"] . '</td></tr>';
    }
  }
  if ( !isset($_SESSION['aktOrd'])  ) { $_SESSION['aktOrd'] = 0; }
  echo '</tbody>';
  echo '</table>';
  echo '</td></tr>';
  visOrd1();
}

function visTxt1($x, $a) {
  global $db, $tety, $bogt;
  echo '<tr><td class="tdTxt">';
  echo '<table id="tbrw2' . $x . '" border="0" width="660px" align="center" cellspacing="0px" bgcolor="#BFBFBF" style="border: 2px solid #AAAAAA">';
  echo '<thead><tr>';
  echo '<td class="head_blaa2" width="656px" colspan="2">' . $bogt[$_SESSION["aktBog"]] . '; folio ' . $_SESSION["aktFol"] . ' in ' . $tety[$x] . '</td>';
  echo '</tr><tr>';
  echo '<td class="head_graa" width="64px" align="left">No.</td>';
  echo '<td class="head_graa" width="576px" align="left">Line</td>';
  echo '</tr></thead>';
  echo '<tbody>';

  $quSen = mysqli_query($db, "SELECT t.id, b.bog, t.fol, t.sno, t.fof, t." . $x . " AS lin FROM test17e t, test17d b WHERE t.id >= " . ($_SESSION['aktSen'] - $a) . " AND t.id <= " . ($_SESSION['aktSen'] + $a) . " AND t.bid = b.id ORDER BY t.id") or die(mysqli_error($db));
  while ( $poSen = mysqli_fetch_assoc($quSen) ) {
  if ( $x == 'tra' ) { $lin = tra($poSen['lin']); } else { $lin = pal($poSen['lin']); }
      if ( ($_SESSION['aktSno'] == $poSen['sno']) && ($_SESSION['aktFol'] == $poSen['fol']) ) {
        echo '<tr onclick="cliSen(' . $_SESSION["aktSen"] . ')" onmouseover="hand()" onmouseout="norm()">';
	    echo '<td class="post_aktiv" width="64px" align="left">' . $poSen['sno'] . '</td>';
        if ( $x == 'tra' ) {
          echo '<td class="post_aktiv_sub" width="576px" align="left">' . VisOrd($lin, $_SESSION["aktWno"], $poSen["fof"], $poSen["id"]) . '</td></tr>';
        } elseif ( $x == 'pal' ) {
          echo '<td class="post_aktiv_sub" width="576px" align="left">' .  VisOrd($lin, $_SESSION["aktWno"], $poSen["fof"], $poSen["id"]) . '</td></tr>';
        } else {
          echo '<td class="post_aktiv_sub" width="576px" align="left">' .$lin . '</td></tr>';
        }
      } else {
        echo '<tr>';
	    echo '<td class="post_pasiv" width="64px" align="left">' . $poSen['sno'] . '</td>';
        echo '<td class="post_pasiv" width="576px" align="left">' . $lin . '</td></tr>';
      }
  }
  echo '</tbody>';
  echo '</table>';
  echo '</td></tr>';
}

function visTxt2() {
  if ( ($_SESSION["aktBog"] == 'MOLSA') ) {
    visTxt1('pal', 6);
  } else {
    visTxt1('tra', 2);
    visTxt1('pal', 2);
    visTxt1('eng', 2);
  }
}

function visTop() {
  echo '<table id="tbsuper" border="0" width="802px" align="center" cellpadding="0px" cellspacing="0px" style="border: 0px solid #FF0000">';
  echo '<tr><td width="802px" height="19px" colspan="2" align="center" style="padding-left: 3px;">';
//  visTopRek();
  echo '</td></tr>';
  echo '<tr><td width="674px" align="center" valign="top" style="border: 0px solid #DDDDAA">';
  echo '<table id="tball" border="0" width="674px" align="center" cellpadding="0px" cellspacing="3px" style="border: 0px solid #FF0000">';
//  echo '<tr><td class="tdTop" width="668px" align="center" style="border: 4px solid #CCCCCC">';
  echo '<tr><td class="tdTop">';
  echo '<table id="tbtop" border="0" width="660px" align="center" cellspacing="0px" style="border: 0px solid #AAAAAA">';
  echo '<thead><tr>';
//  echo '<td class="head_blaa" width="656px" height="23px" align="center" style="border: 0px solid #AAAAAA;"><font face="Times New Roman" color="#FFFFFF" size="4">CoNDiP</font></td>';
  echo '<td class="head_blaa1" width="656px" >CoNDiP</td>';
  echo '</tr></thead>';
  echo '<tfoot><tr>';
  echo '<td class="knaplinie" width="656px">';
  echo '<table id="tbtop_f" border="0" width="656px" cellspacing="0px"><tr>';
  echo '<td class="liniefyld" width="30%"></td>';
  echo '<td class="linieknap" onclick="cliFindRod()" onmouseover="hand()" onmouseout="norm()">find Root</td>';
  echo '<td class="liniefyld" width="12px"></td>';
  echo '<td class="linieknap" onclick="cliFindOrd()" onmouseover="hand()" onmouseout="norm()">find Word</td>';
  echo '<td class="liniefyld" width="12px"></td>';
  echo '<td class="linieknap" onclick="cliFindTxt()" onmouseover="hand()" onmouseout="norm()">find Text</td>';
  echo '<td class="liniefyld" width="30%"></td>';
  echo '</tr></table></td>';
  echo '</tr></tfoot>';
  echo '<tbody><tr><td width="652px" height="17px" align="center" bgcolor="#BBBBBB" style="border: 2px solid #AAAAAA">';
  echo '<font color="#000000"><input type="text" id="sk1" name="sk1" maxlength="35" style="width: 128px;" onkeydown="return pre(event, ' . "'" . $_SESSION['dict'] . "'" . ')" value=""></font>';
  echo '</td></tr></tbody></table>';
  echo '</td></tr>';
}

function visBund() {
  echo '</table>';
  echo '</td>';
  echo '<td width="128px" height="600px" align="center" valign="top" style="padding-top: 3px;">';
//  echo '<img src="side.bmp" width="120px" height="600px" align="top" alt="Reklame" style="border: 4px solid #CCCCCC;">';
//  visSidRek();
  echo '</td></tr>';
  echo '</table>';
  echo '<div id="javaForms" style="display: none;">';
  echo '<form id="selRod2" name="selRod2" action="condip.php" method="post"><input type="submit" id="knapSR2" name="knap" style="width: 0px; height: 0px; background: gray;" value="selRod"><input type="hidden" id="aktRod" name="aktRod" value="0"></form>';
  echo '<form id="selOrd2" name="selOrd2" action="condip.php" method="post"><input type="submit" id="knapSO2" name="knap" style="width: 0px; height: 0px; background: gray;" value="selOrd"><input type="hidden" id="aktOrd" name="aktOrd" value="0"></form>';
  echo '<form id="selSen2" name="selSen2" action="condip.php" method="post"><input type="submit" id="knapSS2" name="knap" style="width: 0px; height: 0px; background: gray;" value="selSen"><input type="hidden" id="aktSen2" name="aktSen" value="0"><input type="hidden" id="aktWno" name="aktWno" value="0"></form>';
  echo '<form id="selSen3" name="selSen3" action="condip.php" method="post"><input type="submit" id="knapSS3" name="knap" style="width: 0px; height: 0px; background: gray;" value="selSe3"><input type="hidden" id="aktSen3" name="aktSen" value="0"></form>';
  echo '<form id="findRod2" name="findRod2" action="condip.php" method="post"><input type="submit" id="knapFR2" name="knap" accesskey="R" style="width: 0px; height: 0px; background: gray;" value="find Root"><input type="hidden" id="skR" name="sk" value=""></form>';
  echo '<form id="findOrd2" name="findOrd2" action="condip.php" method="post"><input type="submit" id="knapFO2" name="knap" accesskey="W" style="width: 0px; height: 0px; background: gray;" value="find Word"><input type="hidden" id="skW" name="sk" value=""></form>';
  echo '<form id="findTxt2" name="findTxt2" action="condip.php" method="post"><input type="submit" id="knapFT2" name="knap" accesskey="T" style="width: 0px; height: 0px; background: gray;" value="find Text"><input type="hidden" id="skT" name="sk" value=""></form>';
  echo '<form id="find2" name="find2" action="condip.php" method="post"><input type="submit" id="knapF2" name="knap" accesskey="F" style="width: 0px; height: 0px; background: gray;" value="find"><input type="hidden" id="skF" name="sk" value=""></form>';
  echo '<input type="button" accesskey="R" style="width: 0px; height: 0px" value="R" onclick="cliFindRod()"> ';
  echo '<input type="button" accesskey="W" style="width: 0px; height: 0px;" value="W" onclick="cliFindOrd()"> ';
  echo '</div>';
  echo '<script type="text/javascript"> ';
  echo 'document.getElementById("sk1").focus(); ';
  echo '</script>';
}

function visInfo() {
  global $infNahuatl, $infTextLis, $infDictFor, $infProjDes;
  echo '<tr><td class="tdInf">';
  echo '<h5>Copenhagen Nahuatl Dictionary Project.</h5>';

  echo "<div class='Beskriv'>";
  echo "This site presents the Nahuatl dictionary information gathered in CoNDiP.<br>";
  echo "The system contains three components, Root dictionary, Word dictionary and Texts.<br>";
  echo "Click on the items below to view contents.<br>";
  echo "<br><span id='del1' class='delHead' onclick='cliSpan(1)' onmouseover='hand()' onmouseout='norm()'>+ The nahuatl language.</span><br>";
  echo "<div id='inf1' style='display: none;'>";
  visInfTab($infNahuatl);
  echo "<a target='_blank' href='http://en.wikipedia.org/wiki/Nahuatl'>Further information on Nahuatl in Wikipedia.</a><br>";
  echo "<a target='_blank' href='http://en.wikipedia.org/wiki/Aztec'>Further information on the Aztec Empire in Wikipedia.</a><br>";
  echo "Note: Not all Nahua speaking people shared the Aztec view on war and exploitation. Sic.<br>";
  echo "</div>";
  echo "<br><span id='del2' class='delHead' onclick='cliSpan(2)' onmouseover='hand()' onmouseout='norm()'>+ Project description.</span><br>";
  echo "<div id='inf2' style='display: none;'>";
  echo "Citations from: <a target='_blank' href='dema.htm'>Description and manual 1992 by Una Canger.</a><br>";
  visInfTab($infProjDes);
  echo "</div>";

  echo "<br><span id='del3' class='delHead' onclick='cliSpan(3)' onmouseover='hand()' onmouseout='norm()'>+ Dictionary abbreviations.</span><br>";
  echo "<div id='inf3' style='display: none;'><br>";
  visInfTab($infDictFor);
  echo "</div>";
  echo "<br><span id='del4' class='delHead' onclick='cliSpan(4)' onmouseover='hand()' onmouseout='norm()'>+ Texts.</span><br>";
  echo "<div id='inf4' style='display: none;'><br>";
  visInfTab($infTextLis);
  echo "</div>";


  echo "<hr>Many thanks to <a target='_blank' href='http://en.wikipedia.org/wiki/Una_Canger'>Una Canger; University of Copenhagen; Denmark.</a>";
  echo "<hr>This version (April 2008) lacks some adjustments in the search function.<br>";
  echo "<br>Michael Thomsen";
  echo "<br>";
//  echo "<br>Ideas and comments to: ";
//  echo "<script type='text/javascript'>";
//  echo "maildk('&#119;&#101;&#098;&#109;&#097;&#115;&#116;&#101;&#114;', '&#099;&#111;&#110;&#100;&#105;&#112;');";
//  echo "</script>";
  echo "</div>";
  echo '</td></tr>';
}

function vis() {
  visTop();
  if ( $_SESSION['dict'] == 'R' ) { visRod2(); }
  elseif ( $_SESSION['dict'] == 'W' ) { visOrd2(); }
  elseif ( $_SESSION['dict'] == 'T' ) { visTxt2(); }
  elseif ( $_SESSION['dict'] == '-' ) { visInfo(); }
  visBund();
}

function findRod() {
  $_SESSION['dict'] = 'R';
  if ( !isset($_POST['sk']) ) { $_SESSION['sk'] = ''; }
  else { $_SESSION['sk'] = $_POST['sk']; }
  unset($_SESSION['aktRod']);
  unset($_SESSION['aktOrd']);
  unset($_SESSION['aktSen']);
  vis();
}

function findOrd() {
  $_SESSION['dict'] = 'W';
  if ( !isset($_POST['sk']) ) { $_SESSION['sk'] = ''; }
  else { $_SESSION['sk'] = $_POST['sk']; }
  unset($_SESSION['aktRod']);
  unset($_SESSION['aktOrd']);
  unset($_SESSION['aktSen']);
  vis();
}

function findTxt() {
  global $db;
  if ( isset($_POST['sk']) && ($_POST['sk'] != '') ) {
    $_SESSION['dict'] = 'T';
    $bf = explode('+', $_POST['sk']);
    unset($_SESSION['aktRod']);
    unset($_SESSION['aktOrd']);
    $bf[0] = strtoupper($bf[0]);
//  print_r($bf); echo '<br>';
    openDb();
    if ( isset($bf[2]) ) {
      $quSen = mysqli_query($db, "SELECT t.id FROM test17e t, test17d b WHERE b.bog = '" . $bf[0] . "' AND t.fol = " . $bf[1] . " AND t.sno = " . $bf[2] . " AND t.bid = b.id ORDER BY t.id LIMIT 1") or die(mysqli_error($db));
      $_SESSION['aktWno'] = 1;
    } elseif ( isset($bf[1]) ) {
      $quSen = mysqli_query($db, "SELECT t.id FROM test17e t, test17d b WHERE b.bog = '" . $bf[0] . "' AND t.fol = " . $bf[1] . " AND t.sno = 0 AND t.bid = b.id ORDER BY t.id LIMIT 1") or die(mysqli_error($db));
      $_SESSION['aktWno'] = 0;
    } else {
      $quSen = mysqli_query($db, "SELECT t.id FROM test17e t, test17d b WHERE b.bog = '" . $bf[0] . "' AND t.fol = 0 AND t.sno = 0 AND t.bid = b.id ORDER BY t.id LIMIT 1") or die(mysqli_error($db));
      $_SESSION['aktWno'] = 0;
    }
    $poSen = mysqli_fetch_assoc($quSen);
//  print_r($poSen); echo '<br>';
    if ( (mysqli_num_rows($quSen) > 0) && isset($poSen['id']) ) {
      $_SESSION['aktSen'] = $poSen['id'];
      selSent();
    } else {
      $_SESSION['dict'] = '-';
      vis();
    }
  } else {
    $_SESSION['dict'] = '-';
    vis();
  }
}

function findNoget() {
  if ( $_SESSION['dict'] == 'R' ) { findRod(); }
  elseif ( $_SESSION['dict'] == 'W' ) { findOrd(); }
  else { $_SESSION['dict'] = 'W'; findOrd(); }
}

function selRod() {
  global $db;
  $_SESSION['aktRod'] = $_POST['aktRod'];
  if ( $_SESSION['dict'] == 'W' ) {
    openDb();
    $qu = mysqli_query($db, "SELECT sea FROM test17b WHERE id = " . $_SESSION['aktRod']) or die(mysqli_error($db));
    $po = mysqli_fetch_assoc($qu);
    $_SESSION['sk'] = $po['sea'];
    $_SESSION['dict'] = 'R';
  }
  elseif ( $_SESSION['dict'] == 'R' ) {
    unset( $_SESSION['aktOrd']);
  }
  vis();
}

function selOrd() {
  global $db;
  $_SESSION['aktOrd'] = $_POST['aktOrd'];
  if ( $_SESSION['dict'] == 'R' ) {
    openDb();
	$qu = mysqli_query($db, "SELECT sea FROM test17c WHERE id = " . $_SESSION['aktOrd']) or die(mysqli_error($db));
    $po = mysqli_fetch_assoc($qu);
    $_SESSION['sk'] = $po['sea'];
    $_SESSION['dict'] = 'W';
  }
  elseif ( $_SESSION['dict'] == 'W' ) {
    openDb();
	$qu = mysqli_query($db, "SELECT sea FROM test17c WHERE id = " . $_SESSION['aktOrd']) or die(mysqli_error($db));
    $po = mysqli_fetch_assoc($qu);
    $_SESSION['sk'] = $po['sea'];
    unset($_SESSION['aktRod']);
    unset($_SESSION['aktSen']);
  }
  vis();
}

function selSent() {
  global $db;
  openDb();
  $quSen = mysqli_query($db, "SELECT t.id, b.bog, t.fol, t.sno FROM test17e t, test17d b WHERE t.id = " . $_SESSION['aktSen'] . " AND t.bid = b.id ORDER BY t.id") or die(mysqli_error($db));
  $poSen = mysqli_fetch_assoc($quSen);
  $_SESSION['aktBog'] = $poSen["bog"];
  $_SESSION['aktFol'] = $poSen["fol"];
  $_SESSION['aktSno'] = $poSen["sno"];
  vis();
}

function selSen() {
  global $db;
  if ( $_SESSION['dict'] == 'W' ) {
    $_SESSION['aktSen'] = $_POST['aktSen'];
    $_SESSION['aktWno'] = $_POST['aktWno'];
    $_SESSION['dict'] = 'T';
    selSent();
  }
  elseif ( $_SESSION['dict'] == 'T' ) {
    openDb();
    $quSwo = mysqli_query($db, "SELECT wid FROM test17cde WHERE tid = " . $_SESSION['aktSen'] . " AND wno = " . $_SESSION['aktWno']) or die(mysqli_error($db));
    $poSwo = mysqli_fetch_assoc($quSwo);
    if ( isset($poSwo["wid"]) ) {
      $_SESSION['aktOrd'] = $poSwo["wid"];
	  $qu = mysqli_query($db, "SELECT sea FROM test17c WHERE id = " . $_SESSION['aktOrd']) or die(mysqli_error($db));
      $po = mysqli_fetch_assoc($qu);
      $_SESSION['sk'] = $po['sea'];
      $_SESSION['dict'] = 'W';
    }
    vis();
  }
  else {
    $_SESSION['dict'] = '-';
    vis();
  }
}

function selSe3() {
  if ( $_SESSION['dict'] == 'T' ) {
    if     ( $_POST['aktSen'] == 'o' ) { $_SESSION['aktSen']--; $_SESSION['aktWno'] = 1; }
    elseif ( $_POST['aktSen'] == 'n' ) { $_SESSION['aktSen']++; $_SESSION['aktWno'] = 1; }
    elseif ( $_POST['aktSen'] == 'v' ) { $_SESSION['aktWno']--; }
    elseif ( $_POST['aktSen'] == 'h' ) { $_SESSION['aktWno']++; }
    selSent();
  }
  else {
    $_SESSION['dict'] = '-';
    vis();
  }
}

openDb();
if ( (!isset($_SESSION['oppe']) || ($_SESSION['oppe'] == 'Restart')) && !isset($_POST['knap']) ) {
//  echo '1. Frisk start.<br>';
  $_SESSION['oppe'] = '4733 MT/CoNDiP';
  $temp = '4733 MT/CoNDiP';
  $_SESSION['dict'] = '-';
  vis();
  echo '<script type="text/javascript"> rez(); </script>';
}
elseif ( ($h == 0) && !isset($_SESSION['oppe']) || ($_SESSION['oppe'] != '4733 MT/CoNDiP') ) {
//  echo '2. Ikke oppe.<br>';
  echo 'Sorry, time out :-( <br>Error: 0056<hr><a href="condip.php">Restart</a>';
  $_SESSION['oppe'] = 'Restart';
  fejl("Time out! Please restart.");
}
elseif ( ($h == 0) && isset($_POST['knap']) ) {
//  echo '3. Knap tryk.<br>';
  if     ( $_POST['knap'] == "Show"       ) { vis(); }
  elseif ( $_POST['knap'] == "find"       ) { findNoget(); }
  elseif ( $_POST['knap'] == "find Root"  ) { findRod(); }
  elseif ( $_POST['knap'] == "find Word"  ) { findOrd(); }
  elseif ( $_POST['knap'] == "find Text"  ) { findTxt(); }
  elseif ( $_POST['knap'] == "selRod"     ) { selRod(); }
  elseif ( $_POST['knap'] == "selOrd"     ) { selOrd(); }
  elseif ( $_POST['knap'] == "selSen"     ) { selSen(); }
  elseif ( $_POST['knap'] == "selSe3"     ) { selSe3(); }
  else {
    echo "Sorry, unknown request :-( <br>Error: 0054<hr>";
    fejl("Please close and try again.");
  }
}
elseif ( isset($_POST['sk']) ) {
    fejl('Testmessage 12.');
    findNoget();
}
else {
//    echo 'Sorry, out of sync :-( <br>Error: 0055 <hr><a href="condip.php">Restart</a>';
    echo 'Sorry, out of sync :-( <br>Error: 0055 <hr>';
    fejl("Out of sync! Please close and try again.");
}

closeDb();
/*
print_r($traf); echo "<br>";
  for ( $i = 0; $i < count($traf); $i++ ) { $traf[$i] = utf8_decode($traf[$i]); }
print_r($traf); echo "<br>";
print_r($trat); echo "<br>";
*/
ob_end_flush();
?>

</body>
</html>
