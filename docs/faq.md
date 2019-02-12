## FAQ

__Why are accounts not being created?__

This plugin does not create user accounts. It just authorizes already existing accounts to view site tracking reports. If automatic account creation is desired I would suggest looking at the LdapLogin plugin in the Marketplace.
You would need access to an Ldap directory for it to work however.

__We are always seeing token expired error?__

If your access provider code and Matomo are on seprate servers this could be a symptom of the clocks on either server being incorrect. Using a service such as ntpd on Linux is highly recommended. If you have full control of your server lookup how to setup ntpd for your distribution.
If you are using a hosting service and your system time is incorrect contact your hosting company to find out how to use the Network Time Protocol with your server. Timezone settings should not be a factor as we are using a UNIX TIMESTAMP for calculation.

__I built an access provider for xxxx CMS. Would you like to be informed?__

Please let me know by [reporting as an issue](https://github.com/jbrule/matomoplugin-SiteAccessProvisioner/issues). Maintaining a directory can be a demanding job so I do not have plans to maintain an access provider directory at this time.
If you create an access provider as a companion to this plugin please reference this plugin in your plugin/module documentation.