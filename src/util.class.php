<?php
/**
 * util.class.php
 */

namespace salix;

/**
 * util: utility class of static methods
 *
 * Extends cenozo's util class with additional functionality.
 */
class util extends \cenozo\util
{
  /**
   * Pulls numerical data from an DXA report provided by Apex
   * 
   * The expected array returned by this function is as follows:
   * [
   *   "Data Set 1": [
   *     "Neck": [
   *       "Area": [ "unit": "cm2", "value": "5.43" ],
   *       "BMC": [ "unit": "g", "value": "3.68" ],
   *       "BMD": [ "unit": "g\/cm2", "value": "0.678" ],
   *       "T-score": [ "unit": "no units", "value": "-1.5" ],
   *       "Peak Reference": [ "unit": "Percent", "value": "80" ],
   *       "Z-score": [ "unit": "no units", "value": "0.1" ],
   *       "Age Matched": [ "unit": "Percent", "value": "101" ]
   *     ],
   *     "Total": [
   *       "Area": [ "unit": "cm2", "value": "33.45" ],
   *       "BMC": [ "unit": "g", "value": "26.10" ],
   *       "BMD": [ "unit": "g\/cm2", "value": "0.780" ],
   *       "T-score": [ "unit": "no units", "value": "-1.3" ],
   *       "Peak Reference": [ "unit": "Percent", "value": "83" ],
   *       "Z-score": [ "unit": "no units", "value": "0.0" ],
   *       "Age Matched": [ "unit": "Percent", "value": "100" ]
   *     ]
   *   ],
   *   "k": [ "unit": "no units", "value": 1.117009 ],
   *   "d0": [ "unit": "no units", "value": 52.858125 ],
   *   "Thickness": [ "unit": "no units", "value": 5.985641 ],
   *   "ROI Width": [ "unit": "no units", "value": 100 ],
   *   "ROI Height": [ "unit": "no units", "value": 102 ],
   *   "Neck Width": [ "unit": "no units", "value": 49 ],
   *   "Neck Height": [ "unit": "no units", "value": 15 ],
   *   "DAP": [ "unit": "cGy*cm2", "value": 3.480145 ]
   * ]
   * 
   * @param string $dcm_filename The full path to an Apex DCM Report file
   * @return array
   * @static
   * @access public
   */
  public static function parse_dcm_report( $dcm_filename )
  {
    $output = NULL;
    $result_code = NULL;
    exec(
      sprintf(
        'dcmdump +T %s | strings | grep "CodeMeaning\|NumericValue"',
        $dcm_filename
      ),
      $output,
      $result_code
    );

    if( 0 != $result_code )
    {
      throw lib::create( 'exception\runtime',
        sprintf(
          'Unable to parse DCM report file "%s" (error code %d)',
          $dcm_filename,
          $result_code
        ),
        __METHOD__
      );
    }

    $data = [];
    $ignore_values = [
      'Scan Information',
      'Region',
      'Data Set Title',
      'Footnote',
    ];
    $parent = NULL;
    $sub_parent = NULL;
    $label = NULL;
    foreach( $output as $line )
    {
      // each line's tree depth is determined by | at the start
      $matches = [];
      if( !preg_match( '/^([ |]*)([^| ].*)/', $line, $matches ) ) continue;

      $tree_parts = $matches[1];
      $line_data = $matches[2];
      $depth = count( explode( '| ', $tree_parts ) );

      // all line data has a string, and may be followed by data enclosed in []
      $matches = [];
      if( !preg_match( '/^([^ ]+)( +(.+))?/', $line_data, $matches ) ) continue;

      $key = $matches[1];
      $value = 4 == count( $matches ) ? trim( $matches[3], '[] ' ) : NULL;

      // ignore certain values
      if( preg_match( sprintf( '/%s/', implode( '|', $ignore_values ) ), $value ) ) continue;

      if( 7 == $depth && 'CodeMeaning' == $key )
      {
        $parent = $value;
        $data[$parent] = [];
      }
      else if( 'Data Set' == substr( $parent, 0, 8 ) )
      {
        if( 9 == $depth && 'CodeMeaning' == $key )
        {
          $sub_parent = $value;
          $data[$parent][$sub_parent] = [];
        }
        else if( 11 == $depth && 'CodeMeaning' == $key )
        {
          $label = $value;
          $data[$parent][$sub_parent][$label] = [];
        }
        else if( 13 == $depth && 'CodeMeaning' == $key )
        {
          $data[$parent][$sub_parent][$label]['unit'] = $value;
        }
        else if( 11 == $depth && 'NumericValue' == $key )
        {
          $data[$parent][$sub_parent][$label]['value'] = $value;
        }
      }
      else if( !is_null( $parent ) )
      {
        if( 9 == $depth && 'CodeMeaning' == $key )
        {
          $data[$parent]['unit'] = $value;
        }
        else if( 7 == $depth && 'NumericValue' == $key )
        {
          $data[$parent]['value'] = floatval( $value );
        }
      }
    }

    return $data;
  }
}
