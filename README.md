# micro-devtools
Command line tools for micro-framework
## I - Installation

### Installing via Composer

Install Composer in a common location or in your project:

```bash
curl -s http://getcomposer.org/installer | php
```
Run the composer installer :

```bash
composer global require phpmv/micro-devtools 1.0.x-dev
```
Make sure to place the `~/.composer/vendor/bin` directory in your PATH so the **Micro** executable can be located by your system.

## II Devtools commands
### Information
To get a list of available commands just run in console:
```bash
Micro
```
This command should display something similar to:

```bash
#micro devtools (1.0.3)

project [projectName] =>
        * Creates a new #micro project.
        * Aliases : new,create-project
        * Parameters :
                -b      shortcut of --dbName
                        Sets the database name.

                -s      shortcut of --serverName
                        Defines the db server address.
                        Default : [127.0.0.1]

                -p      shortcut of --port
                        Defines the db server port.
                        Default : [3306]

                -u      shortcut of --user
                        Defines the db server user.
                        Default : [root]

                -w      shortcut of --password
                        Defines the db server password.

                -q      shortcut of --phpmv
                        Integrates phpmv-UI Toolkit.
                        Possibles values :
                        semantic,bootstrap,ui

                -m      shortcut of --all-models
                        Creates all models from database.


controller [controllerName] =>
        * Creates a new controller.
        * Aliases : create-controller

model [tableName] =>
        * Generates a new model.
        * Aliases : create-model

all-models [] =>
        * Generates all models from database.
        * Aliases : create-all-models

clear-cache [] =>
        * Clear models cache.
        * Parameters :
                -a      shortcut of --all
                        Clear annotations and models cache.
                        Possibles values :
                        true,false


init-cache [] =>
        * Creates the cache for models.
```

### Project creation
Once installed, the simple `Micro new` command will create a fresh micro installation in the directory you specify. For instance, `Micro new blog` would create a directory named blog containing a Micro project:
```bash
Micro new blog
```
You can see more options about installation by reading the [Project creation section](http://micro-framework.readthedocs.io/en/latest/install.html).

### Models creation
make sure that the database is configured properly in app/config/config.php file :
```php
<?php
return array(
		"siteUrl"=>"http://127.0.0.1/blog/",
		"database"=>[
				"dbName"=>"blog",
				"serverName"=>"127.0.0.1",
				"port"=>"3306",
				"user"=>"root",
				"password"=>"",
				"cache"=>false
		],
...
);
```
Execute the command, make sure you are also in the project folder or one of its subfolders :
```bash
Micro all-models
```
