Shadow Framework

Shadow Framework is a php framework wiht a small overhead to be used to accelerate website development.

The main files of the project are stored in core, this directory houses the main files that the rest of the
application can use to help ease development, see individual file descriptions for more information.

Other directories (ex: admin, home) are used to house different modules and serve as both examples, and basis
to baise/build your own modules on.

Tempalte is the directory where all view code is stored. This folder attempts to be highly organized to make it
easy to find individual parts and modular to allow for easy/fast changes.


Get up and running:
1. Edit the path from base server to code inside the core/settings.php file (built to allow for both local and remote
        servers to be linked in the same settings file).
        
2. Enter the url, username, and password to connect to you database

3. Create the database from the .sql file provided

4. Start the web server and run, you should see our home page with a differnt description.

Get up and running (Complete Noob):
1. Install XAMPP
2. Copy ShadowFramework to html folder
3. Go to localhost/phpmyadmin and import the sql file
4. Check that lines 71/72 in core/settings.php show the proper directory name.
