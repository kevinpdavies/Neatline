<?php

/**
 * @package     omeka
 * @subpackage  neatline
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Convert KML -> WKT.
 *
 * @param string $wkt The coverage.
 * @return string|null The WKT.
 */
function nl_kml2wkt($kml) {

    $wkt = null;

    // Is the input valid KML?
    if (geoPHP::detectFormat($kml) == 'kml') {
        $geo = geoPHP::load($kml);
        $wkt = geoPHP::geometryReduce($geo)->out('wkt');
    }

    return $wkt;

}

/**
 * Convert a lon/lat coordinate in EPSG4326 (degrees) to mercator
 * See https://gist.github.com/waynegraham/9213901
 *
 * @param float lon longitude
 * @param float lat latitude
 */
function reproject($lon_lat) {
    $space = strpos($lon_lat, " ");
    $lon = floatval(substr($lon_lat, 0, $space));
    $lat = floatval(substr($lon_lat, $space));
    $x = $lon * 20037508.34 / 180.0;
    $y = log(tan((90 + $lat) * pi() / 360.0)) / (pi() / 180.0);
    $y = $y * 20037508.34 / 180.0;
    $lon_lat_new = strval($x)." ".strval($y);
    return $lon_lat_new;
}

/**
 * Given a raw coverage value, try to extract a valid WKT string. If the value
 * is already WKT, return it unchanged. If it's KML, convert it to WKT.
 *
 * @param string $coverage A coverage.
 * @return string|null WKT.
 */
function nl_getWkt($coverage) {
    debug("nl_getWkt");
    $wkt = null;

    // Return existing WKT.
    if (Validator::isValidWkt($coverage)) {
        $matches = array();
        preg_match_all('/(\d+.\d+ -?\d+.\d+)/', strtolower($coverage), $matches);
        foreach($matches[1] as &$value) {
            $value = reproject($value);
        }
        $coverage = "polygon((".implode(",", $matches[1])."))";
        # Update lat/long pairs
        return $coverage;
    }

    // Otherwise, KML -> WKT.
    else $wkt = nl_kml2wkt($coverage);

    return $wkt;

}


/**
 * Return record coverage data from the NeatlineFeatures plugin.
 *
 * @param $record NeatlineRecord The record to get the feature for.
 * @return string|null
 */
function nl_getNeatlineFeaturesWkt($record) {

    // Halt if Features is not present.
    if (!plugin_is_active('NeatlineFeatures')) return;

    $db = get_db();

    // Get raw coverage.
    $result = $db->fetchOne(
        "SELECT geo FROM `{$db->prefix}neatline_features`
        WHERE is_map=1 AND item_id=?;",
        $record->item_id
    );

    if ($result) {

        // If KML, convert to WKT.
        if (geoPHP::detectFormat($result) == 'kml') {
            $result = nl_kml2wkt(trim($result));
        }

        // If WKT, implode and wrap as `GEOMETRYCOLLECTION`.
        else {
            $result = 'GEOMETRYCOLLECTION(' .
                implode(',', explode('|', $result)) .
            ')';
        }
    }

    return $result;

}
