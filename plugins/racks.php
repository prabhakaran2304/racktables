<?php


$tabhandler['reports']['racks'] = 'renderRacksReport'; // register a report rendering function
$tab['reports']['racks'] = 'Racks'; // title of the report tab

function renderRacksReport()
{
    $racks = scanRealmByText ('rack');
    $i = 0;
    $prevRow = 0;

    foreach($racks as $rack_id => $rack) {
        // insert page break every 3 racks or when changing to a new rack
        if ($i > 0 and ($i % 3 == 0 or $prevRow != $rack['row_id'])) {
            pageBreak();
            $i = 0;
        }

        $i++;

        echo "<div style='float:left; padding: 0 20px 0 0;'>";
        renderMyRack ($rack_id);
        echo "</div>";

        $prevRow = $rack['row_id'];
    }
}
function pageBreak()
{
    echo '<div style="page-break-after: always;    visibility:hidden;    height:1px !important;    margin:0;"></div>';
    echo '<br style="clear:both"/>';
}

// This function renders rack as HTML table.
function renderMyRack ($rack_id, $hl_obj_id = 0)
{
    $rackData = spotEntity ('rack', $rack_id);
    amplifyCell ($rackData);
    markAllSpans ($rackData);
    if ($hl_obj_id > 0)
        highlightObject ($rackData, $hl_obj_id);
    $prev_id = getPrevIDforRack ($rackData['row_id'], $rack_id);
    $next_id = getNextIDforRack ($rackData['row_id'], $rack_id);

    echo '<h2>' . mkA($rackData['location_name'], 'location', $rackData['location_id']);
    echo ' / ';
    echo mkA($rackData['row_name'], 'row', $rackData['row_id']);
    echo ' / ';
    echo mkA ($rackData['name'], 'rack', $rackData['id']);
    echo '</h2>';



    echo "<table class=rack border=0 cellspacing=0 cellpadding=1>\n";
    echo '<caption style="caption-side:bottom;text-align:left; font-size:80%;font-weight:normal;">' . $rackData['comment'] . '</caption>';
    echo "<tr><th width='10%'>&nbsp;</th><th width='20%'>Front</th>";
    echo "<th width='50%'>Interior</th><th width='20%'>Back</th></tr>\n";
    for ($i = $rackData['height']; $i > 0; $i--)
    {
        echo "<tr><th>" . inverseRackUnit ($i, $rackData) . "</th>";
        for ($locidx = 0; $locidx < 3; $locidx++)
        {
            if (isset ($rackData[$i][$locidx]['skipped']))
                continue;
            $state = $rackData[$i][$locidx]['state'];
            echo "<td class='atom state_${state}";
            if (isset ($rackData[$i][$locidx]['hl']))
                echo $rackData[$i][$locidx]['hl'];
            echo "'";
            if (isset ($rackData[$i][$locidx]['colspan']))
                echo ' colspan=' . $rackData[$i][$locidx]['colspan'];
            if (isset ($rackData[$i][$locidx]['rowspan']))
                echo ' rowspan=' . $rackData[$i][$locidx]['rowspan'];
            echo ">";
            switch ($state)
            {
                case 'T':
                    printObjectDetailsForRenderRack ($rackData[$i][$locidx]['object_id'], $hl_obj_id);
                    break;
                case 'A':
                    echo '<div title="This rackspace does not exist">&nbsp;</div>';
                    break;
                case 'F':
                    echo '<div title="Free rackspace">&nbsp;</div>';
                    break;
                case 'U':
                    echo '<div title="Problematic rackspace, you CAN\'T mount here">&nbsp;</div>';
                    break;
                default:
                    echo '<div title="No data">&nbsp;</div>';
                    break;
            }
            echo '</td>';
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
    // Get a list of all of objects Zero-U mounted to this rack
    $zeroUObjects = getEntityRelatives('children', 'rack', $rack_id);
    if (count ($zeroUObjects) > 0)
    {
        echo "<br><table width='50%' class=rack border=0 cellspacing=0 cellpadding=1>\n";
        echo "<tr><th>Zero-U:</th></tr>\n";
        foreach ($zeroUObjects as $zeroUObject)
        {
            $state = ($zeroUObject['entity_id'] == $hl_obj_id) ? 'Th' : 'T';
            echo "<tr><td class='atom state_${state}'>";
            printObjectDetailsForRenderRack($zeroUObject['entity_id']);
            echo "</td></tr>\n";
        }
        echo "</table>\n";
    }
    echo "</center>\n";
}
