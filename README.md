# Drop.io Rich Media Backbone PHP demo applications

## Overview

### Downloading

Download this demo's source with git by running `git clone git://github.com/dropio/Drop.io-RMBS-Demo-PHP.git`

Alternatively, you can click the "Download Source" button on this page above to download a zip or tar archive.

### API Keys

To run either of these demo applications you'll need an RMB API key (and optionally an API secret key).

Get yours from <http://backbone.drop.io>

### 0-simple_demo

This is a small PHP application demonstrating the basic functionality of the Rich Media Backbone API. With it you can create and manage drops as well as upload and manage assets.

**Installation:**

* Edit `config.inc.php.sample` to include your API key (and optionally your API secret if using a secure key).
* Rename `config.inc.php.sample` to `config.inc.php`.
* Upload this project to your web server, open the root directory in your browser, and click the "Simple Demo" link.

### 1-advanced_demo

This is a more full featured PHP application that demonstrates realtime storage and management of uploaded content. It caches information about drops and assets in a MySQL database and uses the RMB JavaScript client library for an AJAX interface with realtime updates provided by pingbacks.

**Installation**

* Create a new MySQL database and make a note of your MySQL username and password.
* Upload this project to your web server (if you haven't already), open the root directory in your browser, and click "Advanced Demo." This should bring you to an installation page.
* On the installation page, enter your credentials to the database you created earlier, make sure the hostname field is correct, and add your Drop.io RMB API key. Your API secret is optional, and used only with secure keys.
* Click "Submit". If your information is correct, you should be redirected to the advanced demo where you can begin by either importing one of your drops or creating a new one.