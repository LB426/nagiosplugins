<?php
$_WARNRULE = '#FFFF00';
$_CRITRULE = '#FF0000';
$_AREA     = '#256aef';
$_LINE     = '#000000';

$hostname = $this->MACRO['HOSTNAME'];
$servicename = $this->MACRO['DISP_SERVICEDESC'];
$servicename_for_regexp = str_replace(' ', '.', $servicename);

$title = "Physical memory load";
$title2 = "";       #blue      green     Olive     violet    brown CornflowerBlue Magenta  Orange    Navy      MediumPurple
$linecolors = array("#0000ff","#00CC00","#808000","#FF3399","#A52A2A","#6495ED","#FF00FF","#FFA500","#000080","#9370DB",);
$gradients = array("#f0f0f0","#01DF01","#f0f0f0","#0000a0");
$def[0] = "";
$ds_name[0] = "";
$def[1] = "";
$ds_name[1] = "";
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
		if (
		      (strcmp($name, "kernel") == 0) || 
		      (strcmp($name, "ZFS_Metadata") == 0) ||
		      (strcmp($name, "ZFS_File_Data") == 0) ||
		      (strcmp($name, "Anon") == 0) ||
		      (strcmp($name, "Exec_and_Libs") == 0) ||
		      (strcmp($name, "Page_cache") == 0) ||
		      (strcmp($name, "Free(cachelist)") == 0) ||
		      (strcmp($name, "Free(freelist)") == 0)
		   )
		{
			$ds_name[0] .= $name.", ";
			$def[0] .= rrd::def($varN, $rrdfile, $dsN, "AVERAGE");
			$def[0] .= rrd::line1($varN, $linecolors[$count], $name , 1);
			$def[0] .= rrd::gprint($varN, array("LAST","MAX","AVERAGE"), "%3.4lf %s".$val2['UNIT']);
		}
		if(strcmp($name, "Total_Memory") == 0)
		{
			$totalmem = $val2['ACT'];
		}
		if($data['DS'][$count]['CRIT'] != 0)
		{
			$crit    = $data['DS'][$count]['CRIT'];
			$def[0] .= rrd::hrule($crit, $_CRITRULE, "Critical $crit \\n");
			#$def[0] .= rrd::hrule(1000000000, $_CRITRULE, "Critical $crit \\n");
		}
		if(strcmp($name, "Free(freelist)") == 0)
		{
			$ds_name[1] .= $name.". Actually really free memory tend to zero.";
			$def[1] .= rrd::def($varN, $rrdfile, $dsN, "AVERAGE");
			$def[1] .= rrd::line1($varN, $linecolors[$count], $name , 1);
			$def[1] .= rrd::gprint($varN, array("LAST","MAX","AVERAGE"), "%3.4lf %s".$val2['UNIT']);
			$def[1] .= rrd::hrule(0, $_CRITRULE, "Critical 0 bytes \\n");
		}
		$count++;
	}
}
$def[0] .= rrd::comment("check_snmp_memory_solaris template\\r");

$vlabel = "--vertical-label \"Physical memory\"";
$title  = "--title \""."Solaris Physical memory distribution. Total memory size ".$totalmem." bytes \"";
$opt[0] = $vlabel." ".$title." --upper=100 --lower=0 ";
$opt[1] = $vlabel." ".$title." --upper=100 --lower=0 ";


?>
