<?php

///////////////////////////////////////////////////////////
// rt-rackunits
// Version: 1.0
//
// Description:
// Report plugin for Racktables listing rackunit usage of equipment
// contained in racks.
//
// Author: Ingimar Robertsson <iar@pjus.is>
//
// Installation:
// Copy rt-rackunits.php into the Racktables plugins folder.
//
// Version History:
// 1.0 - Initial version
///////////////////////////////////////////////////////////

// Variables:
$tabname = 'Rackunit Report';
$tableheader = 'Rackunit Report for Racktables';
$displaylinks = 1; // 1 = Display HTML links for devices and ports

///////////////////////////////////////////////////////////
$rrversion = "1.0";
$tabhandler['reports']['rackunitreport'] = 'RackunitReport'; // register a report rendering function
$tab['reports']['rackunitreport'] = $tabname; // title of the report tab

function RackunitReport()
{
	global $tableheader , $displaylinks, $rrversion;

	// Remote jQuery and DataTables files:
	echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.css">';
	echo '<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>';
	echo '<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.js"></script>';

	echo '<script>
$(document).ready(function() {
$("#rackunitreport").dataTable({
"bPaginate": "true",
"bLengthChange": "false",
"sPaginationType": "full_numbers",
"aaSorting": [[ 0, "desc" ]],
"iDisplayLength": 20,
"stateSave": false,
"oLanguage": {
"sLengthMenu": \'Display <select>\'+
\'<option value="10">10</option>\'+
\'<option value="20">20</option>\'+
\'<option value="30">30</option>\'+
\'<option value="40">40</option>\'+
\'<option value="50">50</option>\'+
\'<option value="-1">All</option>\'+
\'</select> records\'
}
});
});
</script>';

	echo "\n";
	echo '<div class=portlet>';
	echo '<h2>' . $tableheader . '</h2>';
	echo "\n";
	echo '<table id="rackunitreport" class="display">';
	echo "\n";
	echo '<thead><tr>';
	echo ' <th>Name</th>';
	echo ' <th>Type</th>';
	echo ' <th>HW Type</th>';
	echo ' <th>Serial</th>';
	echo ' <th>Asset Tag</th>';
	echo ' <th>Rack Name</th>';
	echo ' <th>Rack Location</th>';
	echo ' <th>U Count</th>';
	echo '</tr></thead>';
	echo "\n";
	echo '<tbody>';
	echo "\n";

	foreach (scanRealmByText ('object', '') as $object) {
		$rack_name = '';
		$rack_id = '';
		$rack_ucount = 0;
		amplifyCell($object);
		$t_allattributes = getAttrValues($object['id']);
		if( $object['rack_id'] ) {
			# Let's only work with Rack mounted objects, ignore others
			$t_rackobj = spotEntity('rack' , $object['rack_id']);
			amplifyCell($t_rackobj);
			$rack_id = $object['rack_id'];
			$rack_name = $t_rackobj['name'];
			$rack_location_id = $t_rackobj['location_id'];
			$rack_location = $t_rackobj['location_name'];
			$rack_height = $t_rackobj['height'];
			for( $u = 1 ; $u <= $rack_height ; $u++ ) {
				# Loop through all Us in rack
				$found_in_u = 0;
				for( $rpos = 0 ; $rpos <= 2 ; $rpos++ ) {
					# Loop through positions within U: front, interior, back
					if( $t_rackobj[$u][$rpos]['state'] == "T" && $t_rackobj[$u][$rpos]['object_id'] == $object['id'] ) {
						$found_in_u = 1;
					}
				}
				if( $found_in_u == 1 ) { $rack_ucount++; }
			}
			$id = $object['id'];
			$name = $object['name'];
			$type = decodeObjectType($object['objtype_id']);
			$serial = $t_allattributes[1]['value'];
			$midlkostn = $t_allattributes[10006]['value'];
			$samningur = $t_allattributes[10000]['value'];
			$muid = $t_allattributes[10010]['value'];
			$hwtype = rackunitreport_StripWiki(execGMarker($t_allattributes[2]['value']));
			$assetno = $object['asset_no'];
			echo '<tr>';
			echo '<td>';
			echo formatPortLink($id, $name);
			echo '</td><td>';
			echo $type;
			echo '</td><td>';
			echo $hwtype;
			echo '</td><td>';
			echo $serial;
			echo '</td><td>';
			echo $assetno;
			echo '</td><td>';
			echo '<a href="index.php?page=rack&rack_id=' . $rack_id . '">' . $rack_name . '</a>';
			echo '</td><td>';
			echo '<a href="index.php?page=location&location_id=' . $rack_location_id . '">' . $rack_location . '</a>';
			echo '</td><td>';
			echo $rack_ucount;
			echo '</td>';
			echo '</tr>';
			echo "\n";
		}
	}
	echo '</tbody></table><br/><br/>';
	echo 'rackunitreport version ' . $rrversion;
	echo '</div>';
}

function rackunitreport_StripWiki($string)
{
	if ( preg_match('/^\[\[(.+)\]\]$/', $string, $matches) ) {
		$s = explode ('|', $matches[1]);
		return(trim($s[0]));
	} else {
		return($string);
	}
}


?>

