<?PHP

$stegn = array(0 => "Ikke angivet", 1 => "Vandmand", 2 => "Fisk", 3 => "Vædder", 4 => "Tyr", 5 => "Tvilling",
  6 => "Krebs", 7 => "Løve", 8 => "Jomfru", 9 => "Vægt", 10 => "Skorpion", 11 => "Skytte", 12 => "Stenbuk");
$lokat = array(0 => "Ikke angivet", 1 => "Bornholm", 2 => "Sjælland med øer", 3 => "Fyn med øer", 4 => "Jylland",
  5 => "Færøerne", 6 => "Grønland", 7 => "Ikke Danmark");
$sex = array(0 => "Ikke angivet", 1 => "Hunkøn", 2 => "Hankøn");

$findsex = array(0 => "Alle", 1 => "Ikke angivet", 2 => "Hunkøn", 3 => "Hankøn");

$q = "'";

function fejl($st) {
  echo '<script type="text/javascript"> alert("' . $st . '"); </script>';
#  echo '<script type="text/javascript"> history.back(-1); </script>';
#  echo '<script type="text/javascript"> window.open("test011z.php", "Test","width="+win_width+",height="+win_height+",top="+pos_vertical+",left="+pos_horizon); </script>';
}

function selud($a) {
  $st = "";
  foreach($a as $n => $v) {
    $st = $st . '<option value="' . $n . '">' . $v . '</option>';
  }
  return $st;
}

function checknick($n) {
  $r = true;
  $t = 0;
  $l = strlen($n);
#  echo '<br>Ok Nick: "';
  while( $r && ($t < $l) ) 
  { 
#    echo $n[$t];
    if ( !((($n[$t] >= "a") && ($n[$t] <= "z")) or 
           (($n[$t] >= "A") && ($n[$t] <= "Z")) or 
           (($n[$t] >= "0") && ($n[$t] <= "9")) or 
           ($n[$t] == "æ") or ($n[$t] == "ø") or ($n[$t] == "å") or
           ($n[$t] == "Æ") or ($n[$t] == "Ø") or ($n[$t] == "Å") or 
           ($n[$t] == " ")) ) { 
#           ($n[$t] == " ") or ($n[$t] == "_") or ($n[$t] == "-")) ) { 
      $r = false;
    }
    $t++; 
  } 
#  echo '"<br>';
  return $r;
}

function smil($txt) {
  $t1 = $txt;
  $t1 = str_replace(':-)', '<img src="S2.bmp">', $t1);
  $t1 = str_replace(':-(', '<img src="S3.bmp">', $t1);
  $t2 = explode(chr(10), $t1);
  return $t2;
}

function visgbog($txt) {
  $t1 = smil($txt);
  echo '<form id="visbr" name="visbr" action="test011e.php" method="post">';
  foreach($t1 as $linie) { 
    if ( $linie[0] == "[" ) {
      $linie = str_replace('[', '<input type="submit" name="visuser" style="background: greenyellow;" value="', $linie);
      $linie = str_replace(']', '">', $linie);
    }
    echo $linie . "<br>"; 
  }
  echo '</form>'; 
}

function flt($s, $t, $f) {
  return $s . $f . $t . '</font></td>';
}

function Da2Us($d) {
  return substr($d, 6, 4) . '-' . substr($d, 3, 2) . '-' . substr($d, 0, 2);
}

function Us2Da($d) {
  return substr($d, 8, 2) . '-' . substr($d, 5, 2) . '-' . substr($d, 0, 4);
}

?>
