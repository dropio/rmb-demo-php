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
    fwrite($fp,"\$API_KEY='{$_POST['api_key']}';\n\n");

    if (!empty($_POST['api_secret']))
        fwrite($fp,"\$API_SECRET='{$_POST['api_secret']}';\n\n");

    fwrite($fp,'?>');
    fclose($fp);

    include_once (dirname(__FILE__) . '/../lib/DB.class.php');

    # Create the tables
    $sql =<<<EOL

DROP TABLE IF EXISTS `asset`;
CREATE TABLE IF NOT EXISTS `asset` (
  `id`          int(11) NOT NULL auto_increment,
  `created_at`  date NOT NULL,
  `name`        varchar(255),
  `drop_name`   varchar(100),
  `values`      text NOT NULL COMMENT 'json data array',
  `type`        varchar(20) NOT NULL,
  PRIMARY KEY   (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Asset Table'  ;

ALTER TABLE `asset` ADD UNIQUE (
`name` ,
`drop`
);

DROP TABLE IF EXISTS `event` ;
CREATE TABLE IF NOT EXISTS `event` (
  `event` varchar(20) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
EOL;

    $install = DB::getInstance()->query($sql);
}

# TODO: A little js validation? Or bail out somewhere in the post

?>
<html>
    <head>
        <title>Install the Dropio Pingback demo</title>
        <link rel="stylesheet" type="text/css" href="../../css/main.css"/>
    </head>
    <body>
        <div id="container">
        <h1>Install the Drop.io Pingback Demo</h1>
        <?php if(isset($install) && ($install !== FALSE)): ?>
            <p>Success! The database was installed.</p>
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
