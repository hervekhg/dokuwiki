<?php
/* This file may be interpreted several times for a single PHP script, hence the 
 * if(!defined()).
 */

# The configuration is moved out of the DokuWiki installation directory, 
# /usr/share/dokuwiki/ in order not to expose it to the web.
if(!defined('DOKU_MAIN_CONF')) define( 'DOKU_MAIN_CONF', '/etc/dokuwiki/' );

# Allow the administrator to define it own preload code in 
# /etc/dokuwiki/preload.php.
if (file_exists("/etc/dokuwiki/preload.php")) include("/etc/dokuwiki/preload.php");


/******************************************************************************
 * Multisite support                                                          *
 ******************************************************************************/

/**
 * This overwrites the DOKU_CONF. Each animal gets its own configuration and data directory.
 *
 * The farm ($farm) can be any directory and needs to be set.
 * Animals are direct subdirectories of the farm directory.
 * There are two different approaches:
 *  * An .htaccess based setup can use any animal directory name:
 *    http://example.org/<path_to_farm>/subdir/ will need the subdirectory '$farm/subdir/'.
 *  * A virtual host based setup needs animal directory names which have to reflect
 *    the domain name: If an animal resides in http://www.example.org:8080/mysite/test/,
 *    directories that will match range from '$farm/8080.www.example.org.mysite.test/'
 *    to a simple '$farm/domain/'.
 *
 * @author Anika Henke <anika@selfthinker.org>
 * @author Michael Klier <chi@chimeric.de>
 * @author Christopher Smith <chris@jalakai.co.uk>
 * @author virtual host part of conf_path() based on conf_path() from Drupal.org's /includes/bootstrap.inc
 *   (see http://cvs.drupal.org/viewvc/drupal/drupal/includes/bootstrap.inc?view=markup)
 * @license GPL 2 (http://www.gnu.org/licenses/gpl.html)
*/
 
//$farm = '/var/www/farm'; // SET THIS to your farm directory
$farm_conf = '/etc/dokuwiki/farm';
$farm_data = '/var/lib/dokuwiki/farm/data';
 
if(!defined('DOKU_CONF')) define('DOKU_CONF', conf_path($farm_conf));
if(!defined('DOKU_FARM')) define('DOKU_FARM', false);
 
 
/**
 * Find the appropriate configuration directory.
 *
 * If the .htaccess based setup is used, the configuration directory can be
 * any subdirectory of the farm directory.
 *
 * Otherwise try finding a matching configuration directory by stripping the
 * website's hostname from left to right and pathname from right to left. The
 * first configuration file found will be used; the remaining will ignored.
 * If no configuration file is found, return the default confdir './conf'.
 */
function conf_path($farm_conf) {
 
    // htaccess based
    if(isset($_REQUEST['animal'])) {
        if(!is_dir($farm_conf.'/'.$_REQUEST['animal'])) nice_die("Sorry! This Wiki doesn't exist!");
        if(!defined('DOKU_FARM')) define('DOKU_FARM', 'htaccess');
        return $farm_conf.'/'.$_REQUEST['animal'].'/';
    }
 
    // virtual host based
    $uri = explode('/', $_SERVER['SCRIPT_NAME'] ? $_SERVER['SCRIPT_NAME'] : $_SERVER['SCRIPT_FILENAME']);
    $server = explode('.', implode('.', array_reverse(explode(':', rtrim($_SERVER['HTTP_HOST'], '.')))));
    for ($i = count($uri) - 1; $i > 0; $i--) {
        for ($j = count($server); $j > 0; $j--) {
            $dir = implode('.', array_slice($server, -$j)) . implode('.', array_slice($uri, 0, $i));
            if(is_dir("$farm_conf/$dir/")) {
                if(!defined('DOKU_FARM')) define('DOKU_FARM', 'virtual');
                return "$farm_conf/$dir/";
            }
        }
    }
 
    // default conf directory in farm
    if(is_dir("$farm_conf/default/")) {
        if(!defined('DOKU_FARM')) define('DOKU_FARM', 'default');
        return "$farm_conf/default/";
    }
    // farmer
    return DOKU_MAIN_CONF;
}
 
 
/* Use default config files and local animal config files */
$config_cascade = array(
    'main' => array(
        'default'   => array(DOKU_MAIN_CONF.'/dokuwiki.php'),
        'local'     => array(DOKU_CONF.'local.php'),
        'protected' => array(DOKU_CONF.'local.protected.php'),
    ),
    'acronyms'  => array(
        'default'   => array(DOKU_MAIN_CONF.'/acronyms.conf'),
        'local'     => array(DOKU_CONF.'acronyms.local.conf'),
    ),
    'entities'  => array(
        'default'   => array(DOKU_MAIN_CONF.'/entities.conf'),
        'local'     => array(DOKU_CONF.'entities.local.conf'),
    ),
    'interwiki' => array(
        'default'   => array(DOKU_MAIN_CONF.'/interwiki.conf'),
        'local'     => array(DOKU_CONF.'interwiki.local.conf'),
    ),
    'license' => array(
        'default'   => array(DOKU_MAIN_CONF.'/license.php'),
        'local'     => array(DOKU_CONF.'license.local.php'),
    ),
    'mediameta' => array(
        'default'   => array(DOKU_MAIN_CONF.'/mediameta.php'),
        'local'     => array(DOKU_CONF.'mediameta.local.php'),
    ),
    'mime'      => array(
        'default'   => array(DOKU_MAIN_CONF.'/mime.conf'),
        'local'     => array(DOKU_CONF.'mime.local.conf'),
    ),
    'scheme'    => array(
        'default'   => array(DOKU_MAIN_CONF.'/scheme.conf'),
        'local'     => array(DOKU_CONF.'scheme.local.conf'),
    ),
    'smileys'   => array(
        'default'   => array(DOKU_MAIN_CONF.'/smileys.conf'),
        'local'     => array(DOKU_CONF.'smileys.local.conf'),
    ),
    'wordblock' => array(
        'default'   => array(DOKU_MAIN_CONF.'/wordblock.conf'),
        'local'     => array(DOKU_CONF.'wordblock.local.conf'),
    ),
    'acl'       => array(
        'default'   => DOKU_CONF.'acl.auth.php',
    ),
    'plainauth.users' => array(
        'default'   => DOKU_CONF.'users.auth.php',
    ),
    'userstyle' => array(
        'default' => DOKU_CONF.'userstyle.css', // 'default' was renamed to 'screen' on 2011-02-26, so will be deprecated in the next version
        'screen'  => DOKU_CONF.'userstyle.css',
        'rtl'     => DOKU_CONF.'userrtl.css',
        'print'   => DOKU_CONF.'userprint.css',
        'feed'    => DOKU_CONF.'userfeed.css',
        'all'     => DOKU_CONF.'userall.css',
    ),
    'userscript' => array(
        'default' => DOKU_CONF.'userscript.js'
    ),
);
