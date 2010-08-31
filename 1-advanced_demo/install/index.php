<?php

# If this is a post then the user submitted the form
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
{
    
    # Handle when port is null;
    $port = (empty($_POST['port'])) ? '3306' : $_POST['port'];

    # Write the config
    $fp = fopen('../_config.inc.php','w+');
    fwrite($fp,"<?php\n\n");
    fwrite($fp,"\$user='{$_POST['user']}';\n");
    fwrite($fp,"\$pass='{$_POST['pass']}';\n");
    fwrite($fp,"\$host='{$_POST['host']}';\n");
    fwrite($fp,"\$port=$port;\n");
    fwrite($fp,"\$dbname='{$_POST['dbname']}';\n\n");
    fwrite($fp,"\$docroot='{$_POST['docroot']}';\n\n");
    fwrite($fp,"\$API_KEY='{$_POST['api_key']}';\n\n");

    if (!empty($_POST['api_secret']))
        fwrite($fp,"\$API_SECRET='{$_POST['api_secret']}';\n\n");

    fwrite($fp,'?>');
    fclose($fp);

    include_once (dirname(__FILE__) . '/../lib/DB.class.php');

    # Create the tables
    $sql =<<<EOL
-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 30, 2010 at 09:05 PM
-- Server version: 5.0.75
-- PHP Version: 5.2.6-3ubuntu4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `dropio`
--

-- --------------------------------------------------------

--
-- Table structure for table `asset`
--

DROP TABLE IF EXISTS `asset`;
CREATE TABLE IF NOT EXISTS `asset` (
  `id` int(11) NOT NULL auto_increment,
  `drop_id` int(11) default NULL,
  `created_at` date NOT NULL,
  `name` varchar(255) default NULL,
  `values` text NOT NULL COMMENT 'json data array',
  `type` varchar(20) NOT NULL,
  `is_complete` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_drop_id_name_uniq_idx` (`drop_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Asset Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `drop`
--

DROP TABLE IF EXISTS `drop`;
CREATE TABLE IF NOT EXISTS `drop` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `created_at` int(11) NOT NULL,
  `values` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `asset`
--
ALTER TABLE `asset`
  ADD CONSTRAINT `asset_ibfk_1` FOREIGN KEY (`drop_id`) REFERENCES `drop` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

EOL;

    $install = DB::getInstance()->query($sql);
}

# TODO: A little js validation? Or bail out somewhere in the post

?>
<html>
    <head>
        <title>Install the Dropio Pingback demo</title>
        <link rel="stylesheet" type="text/css" href="../../css/main.css"/>

        <!-- Load jQuery -->
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>

        <!-- Load Fancybox -->
        <script type="text/javascript" src="../../utils/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
        <link rel="stylesheet" type="text/css" href="../../utils/fancybox/jquery.fancybox-1.3.1.css"/>

    </head>
    <body>
        <div id="container">
        <h1>Install the Drop.io Pingback Demo</h1>
        <?php if(isset($install) && ($install !== FALSE)): ?>
        <script type="text/javascript" language="javascript">
            $(document).ready(function() {
                $(".fancyform").fancybox({
                 'type' : 'iframe',
                 'onClosed' : function(){
                     window.location = '<?php echo $_POST['docroot'] ?>/1-advanced_demo';
                 }
                });
            });
        </script>

            <p>Success! The database was installed.</p>
            <p><a class="fancyform" href="../drop-import_drop.php">Import your drops</a> or <a class="fancyform" href="../drop-create_drop.php">create a new one</a></p>
        <?php else: ?>
        <form action="" method="post">
            <fieldset>
            <legend>Database</legend>
            <label for="host">Hostname: </label>
            <input type="text" name="host" value="localhost"/>

            <label for="port">Port (optional): </label>
            <input type="text" name="port"/>

            <label for="user">Username: </label>
            <input type="text" name="user"/>

            <label for="pass">Password: </label>
            <input type="text" name="pass" />

            <label for="dbname">Database: </label>
            <input type="text" name="dbname" />
            </fieldset>

            <fieldset>
            <legend>Webserver</legend>
            <label for="host">Hostname: </label>
            <input type="text" name="docroot" value="<?php echo $_SERVER['SERVER_NAME'] ?>"/>
            </fieldset>
            
            <fieldset>
            <legend>API Access</legend>
            <label for="api_key">API Key: </label>
            <input type="text" name="api_key" />
            
            <label for="api_secret">API Secret: </label>
            <input type="text" name="api_secret" /><br/>
            <input type="submit"/>
            </fieldset>
        </form>

        <p>Note: The database must already exist and the user must already have
            sufficient privileges. Also, all tables are dropped and re-created
            with this operation.
        </p>
        </div>
        <?php endif ?>
    </body>
</html>
