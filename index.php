<?php
function FX($n,$m,$m_){
	$X=array_fill(0,$n,array_fill(0,$m_,1));
	for($i=0;$i<$n;$i++) if($m_-$m[$i]>0){
		$r=array_rand($X[$i],$m_-$m[$i]);
		if (is_array($r)) foreach($r as $k)
			$X[$i][$k]=0;
		else
			$X[$i][$r]=0;
	}
	return $X;
}
function FS_($X,$S){
	$S_=array();
	foreach($X as $i=>$r){
		$s=0;
		foreach($r as $k=>$x){
			$s+=$x;
			if ($x==0) $S_[$i][$k]='-';
			else $S_[$i][$k]=$S[$i][$s-1];
		}
	}
	return $S_;
}
function FP($S_,$mp,$ip){
	$p=0;
	for($i=0;$i<count($S_)-1;$i++) for($j=$i+1;$j<count($S_);$j++) for($k=0;$k<count($S_[$i]);$k++) if ($S_[$i][$k]!=$S_[$j][$k]){
		if ($S_[$i][$k]=='-' || $S_[$j][$k]=='-') 
			$p+=$ip;//InDel
		else 
			$p+=$mp;//Mute
	}
	return $p;
}
function PrintS($S){
	foreach($S as $s)
		echo implode("",$s)."\n";
}
function US($S){
	$s_='';
	foreach($S as $s)
		$s_.=implode("",$s)."\n";
	return md5($s_);
}
if (isset($_POST['submit'])){
	set_time_limit(0);
	$t = -microtime(1);
	echo "<pre>";
	$ar=explode("\n",$_POST['ms']);
	$S=array();
	$m_=0;
	$i=0;
	foreach($ar as $l) if (trim($l)!=''){
		$L=strtoupper(trim($l));
		$S[$i]=$L;
		$m[$i]=strlen($L);
		if ($m_<$m[$i]) $m_=strlen($L);
		$i++;
	}
	$n=count($S);
	if ($n<2) {echo "Error!";die();}
	$pad=intval($_POST['pad']);
	$mp=intval($_POST['mp']);
	$ip=intval($_POST['ip']);
	$NT=intval($_POST['nt']);;
	//print_r(get_defined_vars());
	$m_+=$pad;
	$min=-1;
	$S__=array();
	$aX=array();
	for($I=0;$I<$NT;$I++){		
		$X=FX($n,$m,$m_);
		$uX=US($X);
		if (isset($aX[$uX])) continue;
		$S_=FS_($X,$S);
		$p=FP($S_,$mp,$ip);
		if ($min==-1) $min=$p;
		if ($min>$p) $S__=array();
		if ($min>=$p){
			$S__[]=$S_;//
			$min=$p;
		}		
		if ($NT<1001){
			PrintS($S_);
			echo "P$I: $p <hr>";
			//flush(); @ob_flush();usleep(1000);
		}
		$aX[$uX]=$p;
	}
	foreach($S__ as $S_){
		PrintS($S_);
		echo "<hr>";
	}
	echo "MinP: $min\n";
	echo "Time: ".($t+microtime(1));
	
}else{
	echo '<form action="index.php" method="post">
			Write Sequences: <br><textarea name="ms" rows="5" cols="50">ATGCATC'."\n".'TCGAAT</textarea><br>
			Pad: <input name="pad" value=1><br>
			Mismatch Penalty: <input name="mp" value=1><br>
			InDel Penalty: <input name="ip" value=1><br>
			Random Seeds: <input name="nt" value=1000><br>
			<input name="submit" type="submit"><form>';
}
?>