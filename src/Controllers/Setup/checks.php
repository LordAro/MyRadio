<?php
/**
 * Checks that necessary modules and resources are available for
 * MyRadio to get started.
 *
 * @version 20140501
 * @author Lloyd Wallis <lpw@ury.org.uk>
 * @package MyRadio_Core
 * @todo Check if rewrites work
 * @todo Check if static resources load?
 * @todo Check for PostgreSQL >=9.2
 */

/**
 * Helper function for size checks
 */
function convertPHPSizeToBytes($sSize)
{
    if (is_numeric($sSize)) {
        return $sSize;
    }
    $sSuffix = substr($sSize, -1);
    $iValue = substr($sSize, 0, -1);
    switch (strtoupper($sSuffix)) {
        case 'P':
            $iValue *= 1024;
            //no break
        case 'T':
            $iValue *= 1024;
            //no break
        case 'G':
            $iValue *= 1024;
            //no break
        case 'M':
            $iValue *= 1024;
            //no break
        case 'K':
            $iValue *= 1024;
            break;
    }
    return $iValue;
}

$required_modules = [
    [
        'module' => 'apc',
        'success' => 'APC will be used to make MyRadio run faster.',
        'fail' => 'If you had the <a href="http://www.php.net/manual/en/book.apc.php">APC extension</a> MyRadio could use it to run much, much faster.',
        'required' => false,
        'set_fail' => ['cache_enable', false]
    ],
    [
        'module' => 'curl',
        'success' => 'cURL can be used to embed the IRN news service into SIS.',
        'fail' => 'If you had the <a href="http://www.php.net/manual/en/book.curl.php">cURL extension</a> MyRadio could use it provide IRN news information in SIS.',
        'required' => false
    ],
    [
        'module' => 'fileinfo',
        'success' => 'The Fileinfo extension can be used to provide upload functionality for the Podcast and Library modules.',
        'fail' => 'If you had the <a href="http://www.php.net/manual/en/book.fileinfo.php">Fileinfo extension</a> MyRadio could be used to upload podcasts and manage a music library.',
        'required' => false
    ],
    [
        'module' => 'geoip',
        'success' => 'The GeoIP extension can be used to provide location functionality for Stats and SIS modules.',
        'fail' => 'If you had the <a href="http://www.php.net/manual/en/book.geoip.php">GeoIP extension</a> MyRadio could provide location information for the Studio Information Service and Statistics.',
        'required' => false
    ],
    [
        'module' => 'gd',
        'success' => 'The Image (GD) extension can be used to provide upload functionality for the Podcast, Profile and Website modules.',
        'fail' => 'If you had the <a href="http://www.php.net/manual/en/book.image.php">Image (GD) extension</a> MyRadio could be used to manage image content on Podcasts, Profiles and a frontend website.',
        'required' => false
    ],
    [
        'module' => 'ldap',
        'success' => 'The LDAP extension can be used to provide external authenticators that use the LDAP protocol.',
        'fail' => 'If you had the <a href="http://www.php.net/manual/en/book.ldap.php">LDAP extension</a> MyRadio could integrate with external authentication providers.',
        'required' => false
    ],
    [
        'module' => 'mcrypt',
        'success' => 'You have the mcrypt extension installed.',
        'fail' => 'The <a href="http://www.php.net/manual/en/book.mcrypt.php">mcrypt extension</a> is required for MyRadio to talk to authentication services.',
        'required' => true
    ],
    [
        'module' => 'pgsql',
        'success' => 'You have an appropriate database driver installed.',
        'fail' => 'The <a href="http://www.php.net/manual/en/book.pgsql.php">PostgreSQL extension</a> is required for MyRadio to talk to a database. Without this, it can\'t do much.',
        'required' => true
    ],
    [
        'module' => 'session',
        'success' => 'You have the session extension installed.',
        'fail' => 'The <a href="http://www.php.net/manual/en/book.session.php">Session extension</a> is required for MyRadio to talk to keep track of who is logged in.',
        'required' => true
    ]
];
$required_files = [
    [
        'file' => 'Twig/Autoloader.php',
        'success' => 'You have Twig installed! This is required for MyRadio to generate web pages.',
        'fail' => 'Your server needs to have Twig installed in order to continue. See <a href="http://twig.sensiolabs.org/doc/installation.html">the Twig documentation</a> for more information.',
        'required' => true
    ]
];
$function_checks = [
    [
        //Check that max post size is at least 40MB
        //this still won't be enough for most podcasts, but it should be for MP3s
        'function' => function () {
            return min(
                convertPHPSizeToBytes(ini_get('post_max_size')),
                convertPHPSizeToBytes(ini_get('upload_max_filesize'))
            ) > 40960;
        },
        'success' => 'Your server is configured to support large file uploads.',
        'fail' => 'Your server is set to have a small (<40MB) upload limit. Consider tweaking your php.ini to prevent issues using Show Planner, Podcasts and other file upload utilities.',
        'required' => false
    ]
];

$ready = true;
$problems = [];
$warnings = [];
$successes = [];

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

if (PHP_VERSION_ID < 50400) {
    $ready = false;
    $problems[] = 'You must be running at least PHP 5.4.';
} else {
    $successes[] = 'You are running PHP '.PHP_VERSION.'.';
}

foreach ($required_modules as $module) {
    if (!extension_loaded($module['module'])) {
        if ($module['required']) {
            $ready = false;
            $problems[] = $module['fail'];
        } else {
            $warnings[] = $module['fail'];
        }
        if (isset($module['set_fail'])) {
            $config_overrides[$module['set_fail'][0]] = $module['set_fail'][1];
        }
    } else {
        $successes[] = $module['success'];
    }
}
foreach ($required_files as $file) {
    if (!stream_resolve_include_path($file['file'])) {
        if ($file['required']) {
            $ready = false;
            $problems[] = $file['fail'];
        } else {
            $warnings[] = $file['fail'];
        }
    } else {
        $successes[] = $file['success'];
    }
}
foreach ($function_checks as $check) {
    if (!$check['function']()) {
        if ($check['required']) {
            $ready = false;
            $problems[] = $check['fail'];
        } else {
            $warnings[] = $check['fail'];
        }
    } else {
        $successes[] = $check['success'];
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Welcome to MyRadio</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="Stylesheet" type="text/css" href="css/normalise.css">
        <link rel="Stylesheet" type="text/css" href="css/vendor/themes/ury-purple/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="css/main.css">
        <link rel="Stylesheet" type="text/css" href="css/style.css">
        <script type="text/javascript" src="js/vendor/jquery-2.1.0.min.js"></script>
    </head>
    <body>
      <div id="grid" class="transBG clearfix">
          <header id="content-header"><h2>Hello there</h2></header>
          <div id="content-body" class="clearfix">
              <p>It looks like you're trying to install MyRadio! Would you like some help with that? No? Well too bad, I'm not a paperclip you can hide.</p>
              <p>I'm just running some background checks to see if you're ready to go.</p>
              <?php
              if ($ready) {
                  ?>
                  <p class="ui-state-highlight">Good news! It looks like you're ready to go. <a href="?c=dbserver">Click here to continue</a>.</p>
              <?php
              } else {
                  ?>
                  <p class="ui-state-error">Uh oh! It looks like there's some things you'll have to get sorted out before you can continue. Follow the advice below, then <a href=''>refresh this page</a> to try again.</p>
              <?php
                  echo '<h3>The following tests failed and must be fixed before you can proceed:</h3><ul>';
                  foreach ($problems as $problem) {
                      echo '<li>'.$problem.'</li>';
                  }
                  echo '</ul>';
              }

              if (empty($warnings)) {
                  if ($ready) {
                      echo '<p><span class="ui-icon ui-icon-circle-check fleft"></span>Amazing! Your server is absolutely <em>perfect</em> for running MyRadio.</p>';
                  }
              } else {
                  echo '<h3>The following tests failed, but they aren\'t required for MyRadio to run:</h3><ul>';
                  foreach ($warnings as $warning) {
                      echo '<li>'.$warning.'</li>';
                  }
                  echo '</ul>';
              }

              if (!empty($successes)) {
                  echo '<h3>The following tests passed without any issues:</h3><ul>';
                  foreach ($successes as $success) {
                      echo '<li>'.$success.'</li>';
                  }
                  echo '</ul>';
              }

              if ($ready === false or !empty($warnings)) {
                  ?>
                  <h3>Cheating</h3>
                  <p>If you're using Ubuntu, the following commands (as root) will get you most of the way:</p>
                  <code>
                      apt-get install php-apc php5-curl php5-geoip php5-gd php5-ldap php5-mcrypt php5-pgsql php5-dev php-pear<br>
                      pear channel-discover pear.twig-project.org<br>
                      pear install twig/Twig<br>
                      pear install twig/CTwig<br>
                      echo "extension=mcrypt.so" > /etc/php5/mods-available/mcrypt.ini<br>
                      ln -s /etc/php5/mods-available/mcrypt.ini /etc/php5/apache2/conf.d/20-mcrypt.ini<br>
                      echo "extension=twig.so" > /etc/php5/mods-available/twig.ini<br>
                      ln -s /etc/php5/mods-available/twig.ini /etc/php5/apache2/conf.d/20-twig.ini<br>
                      service apache2 restart
                  </code>
              <?php
              }
              ?>
          </div>
      </div>
      <footer id="pageFooter" class="clearfix">
        <div id="copyright"><p>MyRadio by <a href="mailto:webmaster@ury.org.uk">URY Computing Team</a></p></div>
      </footer>
    </body>
</html>
