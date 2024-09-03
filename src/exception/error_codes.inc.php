<?php
/**
 * error_codes.inc.php
 * 
 * This file is where all error codes are defined.
 * All error code are named after the class and function they occur in.
 */

/**
 * Error number category defines.
 */
define( 'ARGUMENT_SALIX_BASE_ERRNO',   180000 );
define( 'DATABASE_SALIX_BASE_ERRNO',   280000 );
define( 'LDAP_SALIX_BASE_ERRNO',       380000 );
define( 'NOTICE_SALIX_BASE_ERRNO',     480000 );
define( 'PERMISSION_SALIX_BASE_ERRNO', 580000 );
define( 'RUNTIME_SALIX_BASE_ERRNO',    680000 );
define( 'SYSTEM_SALIX_BASE_ERRNO',     780000 );

/**
 * "argument" error codes
 */

/**
 * "database" error codes
 * 
 * Since database errors already have codes this list is likely to stay empty.
 */

/**
 * "ldap" error codes
 * 
 * Since ldap errors already have codes this list is likely to stay empty.
 */

/**
 * "notice" error codes
 */

/**
 * "permission" error codes
 */

/**
 * "runtime" error codes
 */
define( 'RUNTIME__SALIX_UTIL__PARSE_DCM_REPORT__ERRNO',
        RUNTIME_SALIX_BASE_ERRNO + 1 );

/**
 * "system" error codes
 * 
 * Since system errors already have codes this list is likely to stay empty.
 * Note the following PHP error codes:
 *      1: error,
 *      2: warning,
 *      4: parse,
 *      8: notice,
 *     16: core error,
 *     32: core warning,
 *     64: compile error,
 *    128: compile warning,
 *    256: user error,
 *    512: user warning,
 *   1024: user notice
 */

