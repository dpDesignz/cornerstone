# **Cornerstone PHP Framework**

This is the Cornerstone PHP Framework created and maintained by [dpdesignz](https://github.com/dpDesignz/)

***A PHP MVC framework created to make starting your PHP projects just that little bit easier, allowing you to focus on keeping control of your main code, while trying to keep up with best practices in security.***

The current Version is **0.5.4-alpha**

This is still in alpha stages while it is being developed. Use at your own risk. There are no guarantees offered with this framework.

There is currently no wiki available for this framework while it is still in alpha development

## Installation

Download a copy of the repo and unzip it.

You will need [Composer](https://getcomposer.org/) to install all the dependencies required to run your site.

Once you have installed the composer dependencies, upload the files to your server and then point your web browser to the folder you uploaded to and the install process should start.

**IMPORTANT SUBFOLDER INSTALL NOTE:** If you are installing this package in a subfolder on your webserver, open the `.htaccess` file in the `public` folder and change the `RewriteBase` in line 5 to include the subfolder at the start

_Example_
```
<IfModule mod_rewrite.c>
    Options -Multiviews
    RewriteEngine On
    # CHANGE THIS LINE BELOW TO INCLUDE YOUR SUBFOLDER
    RewriteBase /subfolder-to/public
    # Don't Redirect Existing Directory
    RewriteCond %{REQUEST_FILENAME} !-d
    # Don't Redirect Existing File
    RewriteCond %{REQUEST_FILENAME} !-f
    # Redirect everything else
    RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]
</IfModule>
```

## Contributing

Contributions are encouraged and welcome; I am always happy to get feedback or pull requests on Github! This is my first project of this size, and I'm definitely no perfect developer. Create [Github Issues](https://github.com/dpDesignz/cornerstone/issues) for bugs and new features and comment on the ones you are interested in.

## License

**Cornerstone** is open-sourced software licensed under [GNU LGPLv3](https://www.gnu.org/licenses/lgpl-3.0.en.html).