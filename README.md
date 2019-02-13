# ubiquity-devtools
[![Latest Stable Version](https://poser.pugx.org/phpmv/ubiquity-devtools/v/stable)](https://packagist.org/packages/phpmv/ubiquity-devtools)
[![Total Downloads](https://poser.pugx.org/phpmv/ubiquity-devtools/downloads)](https://packagist.org/packages/phpmv/ubiquity-devtools)
[![License](https://poser.pugx.org/phpmv/ubiquity-devtools/license)](https://packagist.org/packages/phpmv/ubiquity-devtools)
[![Documentation Status](https://readthedocs.org/projects/micro-framework/badge/?version=latest)](http://micro-framework.readthedocs.io/en/latest/?badge=latest)

Command line tools for [Ubiquity framework](https://github.com/phpMv/ubiquity)
## I - Installation

### Installing via Composer

Install Composer in a common location or in your project:

```bash
curl -s http://getcomposer.org/installer | php
```
Run the composer installer :

```bash
composer global require phpmv/ubiquity-devtools
```
Make sure to place the `~/.composer/vendor/bin` directory in your PATH so the **Ubiquity** executable can be located by your system.

## II Devtools commands
### Information
To get a list of available commands just run in console:
```bash
Ubiquity help
```
This command should display something similar to:

```bash
Ubiquity devtools (1.1.2)

project [projectName] =>
        * Creates a new #ubiquity project.
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

                -a      shortcut of --admin
                        Adds UbiquityMyAdmin tool.
                        Possibles values :
                        true,false
                        Default : [false]

        * Samples :
                Creates a new project
                  · Ubiquity new blog
                With admin interface
                  · Ubiquity new blog -q=semantic -a
                and models generation
                  · Ubiquity new blog -q=semantic -a -m -b=blogDB

serve [] =>
        * Start the php web server.
        * Parameters :
                -h      shortcut of --host
                        Sets the host ip address.
                        Default : [127.0.0.1]

                -p      shortcut of --port
                        Sets listen port number.
                        Default : [8090]

        * Samples :
                Starts the server on 127.0.0.1:8090
                  · Ubiquity serve

help [?] =>
        * Get some help about a dev-tools command.
        * Samples :
                Get some help about crud
                  · Ubiquity help crud

controller [controllerName] =>
        * Creates a new controller.
        * Aliases : create-controller
        * Parameters :
                -v      shortcut of --views
                        creates an associated view folder
                        Possibles values :
                        true,false

        * Samples :
                Creates a controller
                  · Ubiquity controller UserController
                with its associated view
                  · Ubiquity controller UserController -v

model [tableName] =>
        * Generates a new model.
        * Aliases : create-model
        * Samples :
                  · Ubiquity model User

all-models [] =>
        * Generates all models from database.
        * Aliases : create-all-models
        * Samples :
                  · Ubiquity all-models

clear-cache [] =>
        * Clear models cache.
        * Parameters :
                -t      shortcut of --type
                        Defines the type of cache to reset.
                        Possibles values :
                        all,annotations,controller,rest,models,queries,views

        * Samples :
                Clear all caches
                  · Ubiquity clear-cache -t=all
                Clear models cache
                  · Ubiquity clear-cache -t=models

init-cache [] =>
        * Init the cache for models, router, rest.
        * Parameters :
                -t      shortcut of --type
                        Defines the type of cache to create.
                        Possibles values :
                        all,controller,rest,models

        * Samples :
                Init all caches
                  · Ubiquity init-cache
                Init models cache
                  · Ubiquity init-cache -t=models

self-update [] =>
        * Updates Ubiquity framework for the current project.

admin [] =>
        * Add UbiquityMyAdmin webtools to the current project.

crud [crudControllerName] =>
        * Creates a new CRUD controller.
        * Aliases : crud-controller
        * Parameters :
                -r      shortcut of --resource
                        The model used

                -d      shortcut of --datas
                        The associated Datas class
                        Possibles values :
                        true,false
                        Default : [true]

                -v      shortcut of --viewer
                        The associated Viewer class
                        Possibles values :
                        true,false
                        Default : [true]

                -e      shortcut of --events
                        The associated Events class
                        Possibles values :
                        true,false
                        Default : [true]

                -t      shortcut of --templates
                        The templates to modify
                        Possibles values :
                        index,form,display
                        Default : [index,form,display]

                -p      shortcut of --path
                        The associated route

        * Samples :
                Creates a crud controller for the class models\User
                  · Ubiquity crud -r=User
                and associates a route to it
                  · Ubiquity crud -r=User -p=/users
                allows customization of index and form templates
                  · Ubiquity crud -r=User -t=index,form

auth [authControllerName] =>
        * Creates a new controller for authentification.
        * Aliases : auth-controller
        * Parameters :
                -e      shortcut of --extends
                        The base class of the controller (must derived from AuthController)
                        Default : [Ubiquity\controllers\auth\AuthController]

                -t      shortcut of --templates
                        The templates to modify
                        Possibles values :
                        index,info,noAccess,disconnected,message,baseTemplate
                        Default : [index,info,noAccess,disconnected,message,baseTemplate]

                -p      shortcut of --path
                        The associated route

        * Samples :
                Creates a new controller for authentification
                  · Ubiquity auth AdminAuthController
                and associates a route to it
                  · Ubiquity auth AdminAuthController -p=/admin/auth
                allows customization of index and info templates
                  · Ubiquity auth AdminAuthController -t=index,info

action [controller.action] =>
        * Creates a new action in a controller.
        * Aliases : new-action
        * Parameters :
                -p      shortcut of --params
                        The action parameters (or arguments)

                -r      shortcut of --route
                        The associated route path

                -v      shortcut of --create-view
                        Creates the associated view
                        Default : [false]

        * Samples :
                Adds the action all in controller Users
                  · Ubiquity action Users.all
                Adds the action display in controller Users with a parameter
                  · Ubiquity action Users.display -p=idUser
                and associates a route to it
                  · Ubiquity action Users.display -p=idUser -r=/users/display/{idUser}
                with multiple parameters
                  · Ubiquity action Users.search -p=name,address
                and create the associated view
                  · Ubiquity action Users.search -p=name,address -v
```

### Project creation
Once installed, the simple `Ubiquity new` command will create a fresh micro installation in the directory you specify. For instance, `Micro new blog` would create a directory named blog containing an Ubiquity project:
```bash
Ubiquity new blog
```
You can see more options about installation by reading the [Project creation section](http://micro-framework.readthedocs.io/en/latest/install.html).

### Testing
You can test with the php web server,
from the root folder of your web application, run :
```
Ubiquity serve
```

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
Ubiquity all-models
```
