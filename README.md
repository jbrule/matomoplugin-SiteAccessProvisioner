# Matomo SiteAccessProvisioner Plugin

##Description
Plugin for the Matomo Web Analytics software package that facilitates an easy process to grant users access to site reports. A companion access provider is required, usually in the form of a website CMS plugin/module (you may need to build this if one does not exist).

##Instructions
The easiest way to install is to find the plugin in the [Matomo Marketplace](https://plugins.matomo.org/).
A shared secret must be etablished before the plugin will function.
You will then need to implement an access provider (see example code below) which will generate an access request link users can use.

##Usage

Implementation code for access provider. Should be trivial to port to other languages.
```php
$sharedSecret = "xxxxxxxxxxxxx"; //Shared secret. This needs to match the secret set in the Plugin settings. Should only be accessible by admin.
$idUser = "myusername"; //Matomo username
$site = "www.example.com/practicesubsite"; //URL of the site to request access for. Also accepts idSite.
$timestamp = time();
$token = hash('sha256', implode('',[$sharedSecret, $idUser, $site, $timestamp]));

$linkHref = sprintf("http://matomoinstall.example.com/matomo/index.php?%s", http_build_query(["module"=>"SiteAccessProvisioner", "action"=>"accessRequest", "idUser"=>$idUser, "site"=>$site, "timestamp"=>$timestamp, "token"=>$token]));
```
##License
GPL v3 / fair use

## Support
Please [report any issues](https://github.com/jbrule/matomoplugin-SiteAccessProvisioner/issues). Pull requests welcome.
