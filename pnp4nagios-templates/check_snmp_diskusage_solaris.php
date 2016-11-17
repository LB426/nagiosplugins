<?php
$_WARNRULE = '#FFFF00';
$_CRITRULE = '#FF0000';
$_AREA     = '#256aef';
$_LINE     = '#000000';

$hostname = $this->MACRO['HOSTNAME'];
$servicename = $this->MACRO['DISP_SERVICEDESC'];
$servicename_for_regexp = str_replace(' ', '.', $servicename);
$servicename_for_regexp = str_replace('/', '.', $servicename_for_regexp);

$title = "Disk I/O usage";
$title2 = "";       #blue      BlueViolet Brown      BurlyWood  CadetBlue  Chartreuse Chocolate  Coral
$linecolors = array("#0000ff", "#8A2BE2", "#A52A2A", "#DEB887", "#5F9EA0", "#7FFF00", "#D2691E", "#FF7F50", 
                    #CornflowerBlue Crimson    Cyan       DarkBlue   DarkCyan   DarkGoldenRod DarkGreen  DarkMagenta 
					"#6495ED",      "#DC143C", "#00FFFF", "#00008B", "#008B8B", "#B8860B",    "#006400", "#8B008B");
$gradients = array("#f0f0f0","#01DF01","#f0f0f0","#0000a0");
$def[0] = "";
$ds_name[0] = "kilobytes read per second";
$def[1] = "";
$ds_name[1] = "kilobytes written per second";
$services = $this->tplGetServices($hostname,$servicename_for_regexp);
$crit = 0;
$totalmem = 0;
foreach($services as $key=>$val){
	$data = $this->tplGetData($val['host'],$val['service']);
	#throw new Kohana_exception(print_r($data,TRUE));
	$count = 0;
	foreach($data['DS'] as $key2=>$val2){
		$varN    = "var$count";
		$rrdfile = $data['DS'][$count]['RRDFILE'];
		$dsN     = $data['DS'][$count]['DS'];
		$name    = $data['DS'][$count]['NAME'];
		if(preg_match("/kr.s/", $name))
		{
			$def[0] .= rrd::def($varN, $rrdfile, $dsN, "AVERAGE");
			$def[0] .= rrd::line1($varN, $linecolors[$count], $name , 0);
			$def[0] .= rrd::gprint($varN, array("LAST","MAX","AVERAGE"), "%3.4lf %s".$val2['UNIT']);
		}
		if(preg_match("/kw.s/", $name))
		{
			$def[1] .= rrd::def($varN, $rrdfile, $dsN, "AVERAGE");
			$def[1] .= rrd::line1($varN, $linecolors[$count], $name , 0);
			$def[1] .= rrd::gprint($varN, array("LAST","MAX","AVERAGE"), "%3.4lf %s".$val2['UNIT']);
		}
		$count++;
	}
	#throw new Kohana_exception(print_r($count,TRUE));
}
$def[0] .= rrd::comment("check_snmp_diskusage_solaris template\\r");
$def[1] .= rrd::comment("check_snmp_diskusage_solaris template\\r");

$vlabel = "--vertical-label \"Disk I/O kilobytes per second\"";
$title  = "--title \"".$hostname." / Disk I/O \"";
$opt[0] = $vlabel." ".$title." --upper=100 --lower=0 ";
$opt[1] = $vlabel." ".$title." --upper=100 --lower=0 ";


?>
