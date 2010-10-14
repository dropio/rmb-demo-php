<html>
    <head>
        <title>RMB Demo Applications for PHP</title>
        <link rel="stylesheet" type="text/css" media="screen" href="/css/main.css"/>
    </head>
    <body>
        <div id="container">
        <h1>RMB Demo Applications for PHP</h1>

        <p>These examples demonstrate how you can use the Rich Media Backbone along
     with our <a href="http://github.com/dropio/dropio-php" target="_blank">PHP helper library</a> to
    handle file uploads, conversion, storage, and delivery in your applications. The  <a href="http://github.com/dropio/Drop.io-RMBS-Demo-PHP">source for these demos is <a href="http://github.com/dropio/Drop.io-RMBS-Demo-PHP">available on Github</a>.</p>
    <p><a href="http://rmb.io" target="_blank">Learn more about the Rich Media Backbone</a></p>

        <h2><a href="0-simple_demo/">Simple Demo</a></h2>
        <p>The simple demo provides examples of each API call using our PHP helper library.</p>

        <h2><a href="1-advanced_demo/">Advanced Demo</a></h2>
        <p>The advanced demo presents a
    complete functional workflow for accepting user uploads, handling asynchronous conversion
    pingbacks, and updating the client in realtime when files are converted using our XMPP stream.
    This demo can also function as a scaffold that you can modify and build upon to create your own
    applications.</p>

    <p>To run the advanced demo on your own server, you will need to create a MySQL database for the application to
        store information about assets, as well as a publicly accessible web address for accepting
        pingback POSTs from the Rich Media Backbone when your conversions are complete.</p>
        </div>
    </body>
</html>
