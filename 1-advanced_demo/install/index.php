<?php

# If this is a post then the user submitted the form
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post')
{
    
    # Handle when port is null;
    $port = (empty($_POST['port'])) ? '3306' : $_POST['port'];

    # Write the config
    $fp = fopen('../_config.inc.php','w+');
    fwrite($fp,"<?php\n\n");
    fwrite($fp,"Config::\$user='{$_POST['user']}';\n");
    fwrite($fp,"Config::\$pass='{$_POST['pass']}';\n");
    fwrite($fp,"Config::\$host='{$_POST['host']}';\n");
    fwrite($fp,"Config::\$port=$port;\n");
    fwrite($fp,"Config::\$dbname='{$_POST['dbname']}';\n\n");
    fwrite($fp,"\$docroot='{$_POST['docroot']}';\n\n");
    fwrite($fp,"\$API_KEY='{$_POST['api_key']}';\n\n");

    if (!empty($_POST['api_secret']))
        fwrite($fp,"\$API_SECRET='{$_POST['api_secret']}';\n\n");

    fwrite($fp,'?>');
    fclose($fp);

    include_once (dirname(__FILE__) . '/../lib/DB.class.php');

    # Create the tables
    $sql ="DROP TABLE IF EXISTS `asset`;\r\n
		CREATE TABLE IF NOT EXISTS `asset` (\r\n
		  `id` int(11) NOT NULL auto_increment,\r\n
		  `drop_id` int(11) default NULL,\r\n
		  `created_at` date NOT NULL,\r\n
		  `asset_id` int(11) NOT NULL,\r\n
		  `name` varchar(255) default NULL,\r\n
		  `values` text NOT NULL COMMENT 'json data array',\r\n
		  `type` varchar(20) NOT NULL,\r\n
		  `is_complete` tinyint(1) NOT NULL default '0',\r\n
		  PRIMARY KEY  (`id`)\r\n
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Asset Table' AUTO_INCREMENT=1 ;\r\n
		DROP TABLE IF EXISTS `drop`;\r\n
		CREATE TABLE IF NOT EXISTS `drop` (\r\n
		  `id` int(11) NOT NULL auto_increment,\r\n
		  `name` varchar(255) NOT NULL,\r\n
		  `created_at` int(11) NOT NULL,\r\n
		  `values` text NOT NULL,\r\n
		  PRIMARY KEY  (`id`),\r\n
		  UNIQUE KEY `name` (`name`)\r\n
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;\r\n
		ALTER TABLE `asset`\r\n
		  ADD CONSTRAINT `asset_ibfk_1` FOREIGN KEY (`drop_id`) REFERENCES `drop` (`id`) ON DELETE CASCADE ON UPDATE CASCADE";
	$dbi = DB::getInstance($_POST['user'],$_POST['pass'],$_POST['dbname'],$_POST['host'],$port);
    $install = $dbi->exec($sql);
	#Redirect to the app
	header("Location: http://" . $_SERVER['SERVER_NAME'] ."/". substr($_SERVER["REQUEST_URI"], 0, strpos($_SERVER["REQUEST_URI"], "/install")));
	# TODO: A little js validation? Or bail out somewhere in the post
}
else{
?>
<html>
    <head>
        <title>Install the Dropio RMB Advanced demo</title>
        <link rel="stylesheet" type="text/css" href="../../css/main.css"/>

        <!-- Load jQuery -->
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>

        <!-- Load Fancybox -->
        <script type="text/javascript" src="../../utils/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
        <link rel="stylesheet" type="text/css" href="../../utils/fancybox/jquery.fancybox-1.3.1.css"/>

    </head>
    <body>
        <div id="container">
        <h1>Install the Drop.io RMB Advanced Demo</h1>
        
        <p><a class="fancyform" href="../drop-import_drop.php">Import your drops</a> or <a class="fancyform" href="../drop-create_drop.php">create a new one</a></p>
        
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
            <input type="text" name="docroot" value="http://<?php echo $_SERVER['SERVER_NAME'] ?>"/>
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
    </body>
</html>
<?php 
} 
?>