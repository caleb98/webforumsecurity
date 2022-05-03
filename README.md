# WebForumSecurity
A basic web forum/disucssion board site that implmements security features such as upgradable passwords, role-based access control, and prevention methods for common web attacks such as SQL injection, html injection, and cross-site scripting.  

*Created for CS 560 - Security in Computing.*  

## Setup
This project was developed for the Apache 2.4.53 for Unix using PHP 8.1.4. 
To run the project locally, begin by installing the Apache webserver and PHP on your machine. Newest versions are preferred, but if compatibility issues arise the versions listed above may be used.  

This project also uses Composer for dependency management. You may follow the [instructions here](https://getcomposer.org/doc/00-intro.md) to setup composer.  

After composer is installed, you may clone the repository and download the required dependencies:  

```
git clone git@github.com:caleb98/webforumsecurity
cd webforumsecurity
composer install
```

Included in the repository's `setup` directory is a sql file which can be used to initialize the database. When connecting to the database, the web forum project will check for Apache environment variables to retrieve connection info. It will also check the environment variables to access the Discord API client information. These variables may be set in the Apache configuration file. Depending on your distribution, this file may be located in different locations. To set up the appropriate environment variables, add the following to your Apache configuration file using the proper information for your database/Discord API info:  

```
SetEnv WEBFORUM_USER "username"
SetEnv WEBFORUM_PASS "password"
SetEnv WEBFORUM_DBNAME "schema_name"
SetEnv WEBFORUM_DBHOST "localhost"

SetEnv DISCORD_CLIENT_ID "1234567890"
SetEnv DISCORD_CLIENT_SECRET "client_secret"
```

This project also makes use of the `mod_rewrite` module included in the Apache webserver to ensure that access URLs are properly mapped to application controllers. Ensure that this module is enabled before continuing.  

The final step to ensure that the correct roles and permissions are included in the database is to run the `createroleperms.php` file. This will create all necessary permissions and roles for the project. Additional roles/permissions may be created by modifying this file, or by manually inserting to the database.  

Once the above setup is completed, move the repository contents into your Apache root directory and restart the Apache webserver service. You should now be able to access the web forum project by navigating `localhost` in your browser.  
