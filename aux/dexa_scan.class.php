<?php
/**
 * dexa_scan.class.php
 *
 * A utility class to encapsulate dexa scans
 *
 * @author Dean Inglis <inglisd@mcmaster.ca>
 */

class dexa_scan
{
  public function __construct(
    $uid, $type, $side,
    $rank, $barcode, $serial_number,
    $apex_scan_id, $scan_type_id, $priority, $apex_host_id = NULL )
  {
    $this->uid = $uid;
    $this->type = $type;
    $this->side = $side;
    $this->rank = $rank;
    $this->barcode = $barcode;
    $this->serial_number = $serial_number;
    $this->apex_scan_id = $apex_scan_id;
    $this->apex_host_id = 'NULL' === $apex_host_id ? NULL : $apex_host_id;
    $this->priority = $priority;
    $this->scan_type_id = $scan_type_id;

    $this->copy_from = NULL;
    $this->copy_to = NULL;
    $this->import_datetime = NULL;
  }

  public function get_basefile_name()
  {
    return sprintf( '%s_%s_%s_%d.dcm',
      $this->type, $this->side, $this->barcode, $this->rank );
  }

  public function get_scan_file( $opal_source, $path, &$err )
  {
    $opal_var = $this->type;
    $opal_var .= 'none' == $this->side ? '' : '_' . $this->side;
    $opal_var .= '_image';

    $opal_source->set_view( 'image_' . $this->rank );

    // download the dicom file based on uid, rank, type, side, barcode to the working directory
    $res = $opal_source->get_participant( $this->uid );
    if( is_object( $res ) && property_exists( $res, 'values' ) )
    {
      $res = array_filter(
        $res->values,
        function ( $obj ) use( $opal_var )
        {
          return (
            property_exists( $obj, 'link' ) &&
            property_exists( $obj, 'length' ) &&
            0 < $obj->length &&
            false !== strpos( $obj->link, $opal_var )
          );
        }
      );
    }
    if( NULL === $res || false === $res )
    {
      $err = sprintf(
        'ERROR failed to retrieve opal image resource (%s, %s, %s) '.
        'for participant "%s" with args %s %s and opal obj %s',
        $this->type,
        $this->side,
        $this->rank,
        $this->uid,
        $path,
        $opal_var,
        print_r($opal_source,true)
      );
      return NULL;
    }

    $res = current( $res );
    $link = $res->link;
    $filename = sprintf( '%s/%s', $path, $this->get_basefile_name() );

    $opal_source->send( $link, array( 'output' => $filename ) );

    // verify the file is non-empty
    if( !file_exists( $filename ) || 0 == filesize( $filename ) )
    {
      $filename = NULL;
    }
    return $filename;
  }

  public function validate( $filename )
  {
    $file_error = false;
    if( NULL === $filename ) return $file_error;

    $validation_list = self::$validation_types[$this->type];
    foreach( $validation_list as $validation )
    {
      $res = self::get_dicom_tag_value($validation, $filename);
      if( 'LATERALITY' == $validation )
      {
        $laterality = 'left' == $this->side ? 'L' : 'R';
        if( $laterality != $res )
        {
          $file_error = true;
        }
      }
      else if( 'PATIENTID' == $validation )
      {
        if( $this->barcode != $res )
        {
          $file_error = true;
        }
      }
      else if( 'SERIAL_NUMBER' == $validation )
      {
        if( $this->serial_number != $res )
        {
          $file_error = true;
        }
      }
    }

    return $file_error;
  }

  public static function get_dicom_tag_value($tag_name, $filename)
  {
    if(!array_key_exists($tag_name, self::$gdcm_validation_list)) return null;

    $res = null;
    $gdcm_command = self::$gdcm_validation_list[$tag_name];
    $res = trim( shell_exec( sprintf( $gdcm_command, $filename ) ) );
    if('SERIAL_NUMBER' == $tag_name)
    {
      $res = preg_replace( '/[^0-9]/', '', $res );
      return $res;
    }
    else
    {
      return $res;
    }
  }

  private static $gdcm_validation_list = array(
    'SERIAL_NUMBER' =>
    "gdcmdump -d %s | grep -E '\(0008,1090\)' | awk '{print $4$5$6}'",
    'LATERALITY' =>
    "gdcmdump -d %s | grep -E '\(0020,0060\)' | awk '{print $4}'",
    'PATIENT_SEX' =>
    "gdcmdump -d %s | grep -E '\(0010,0040\)' | awk '{print $4}'",
    'PATIENT_DOB' =>
    "gdcmdump -d %s | grep -E '\(0010,0030\)' | awk '{print $4}'",
    'STUDY_DATE' =>
    "gdcmdump -d %s | grep -E '\(0008,0020\)' | awk '{print $4}'",
    'STUDY_TIME' =>
    "gdcmdump -d %s | grep -E '\(0008,0030\)' | awk '{print $4}'",
    'PATIENTID' =>
    "gdcmdump -d %s | grep -E '\(0010,0020\)' | awk '{print $4}'"
  );

  private static $validation_types = array(
    'hip' => array( 'SERIAL_NUMBER', 'LATERALITY', 'PATIENTID' ),
    'forearm' => array( 'SERIAL_NUMBER', 'LATERALITY', 'PATIENTID' ),
    'lateral' => array( 'SERIAL_NUMBER', 'PATIENTID' ),
    'spine' => array( 'SERIAL_NUMBER', 'PATIENTID' ),
    'wbody' => array( 'SERIAL_NUMBER', 'PATIENTID' )
  );

  public $uid;
  public $type;
  public $side;
  public $scan_type_id;
  public $serial_number;
  public $barcode;
  public $apex_scan_id;
  public $priority;
  public $apex_host_id;
  public $copy_from;
  public $copy_to;
  public $import_datetime;
}
