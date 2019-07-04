![img](https://github.com/phpmv/ubiquity-devtools/blob/master/.github/images/devtools.png?raw=true)

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

To confirm **Ubiquity** was successfully installed, type ``Ubiquity version``:

![img](https://github.com/phpmv/ubiquity-devtools/blob/master/.github/images/devtools-version.png)

<details>
        <summary>If you get the message <b>Ubiquity command not found</b></summary>
        Add composer's <code>bin</code> directory to the system path
        <ul>
                <li>On windows
                        <ul><li>
                                by adding the value <code>%USERPROFILE%\AppData\Roaming\Composer\vendor\bin</code> to the system PATH variable
                        </li></ul>
                </li>
                <li>On other systems
                        <ul><li>
                                by placing <code>export PATH="$HOME/.composer/vendor/bin:$PATH"</code> into your <code>~/.bash_profile</code> (Mac OS users) or into your <code>~/.bashrc</code> (Linux users).
                        </li></ul>
                </li>
        </ul>
</details>

## II Devtools commands
### Information
To get a list of available commands just run in console:
```bash
Ubiquity help
```
This command should display something similar to:

```bash
Ubiquity devtools (1.2.7)

■ project [projectName] =>
        · Creates a new #ubiquity project.
        · Aliases : new,create-project
        · Parameters :
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

                -h      shortcut of --themes
                        Install themes.
                        Possibles values :
                        semantic,bootstrap,foundation

                -m      shortcut of --all-models
                        Creates all models from database.

                -a      shortcut of --admin
                        Adds UbiquityMyAdmin tool.
                        Possibles values :
                        true,false
                        Default : [false]

                -i      shortcut of --siteUrl
                        Sets the site base URL.

                -e      shortcut of --rewriteBase
                        Sets .htaccess file rewriteBase.
                        
        × Samples :
                Creates a new project
                  · Ubiquity new blog
                With admin interface
                  · Ubiquity new blog -a
                and models generation
                  · Ubiquity new blog -a -m -b=blogDB

■ serve [] =>
        · Start a web server.
        · Parameters :
                -h      shortcut of --host
                        Sets the host ip address.
                        Default : [127.0.0.1]

                -p      shortcut of --port
                        Sets the listen port number.
                        Default : [8090]

                -t      shortcut of --type
                        Sets the server type.
                        Possibles values :
                        php,react
                        Default : [php]

        × Samples :
                Starts a php server at 127.0.0.1:8090
                  · Ubiquity serve
                Starts a reactPHP server at 127.0.0.1:8080
                  · Ubiquity serve -t=react

■ help [?] =>
        · Get some help about a dev-tools command.
        × Samples :
                Get some help about crud
                  · Ubiquity help crud

■ version [] =>
        · Return PHP, Framework and dev-tools versions.

■ controller [controllerName] =>
        · Creates a new controller.
        · Aliases : create-controller
        · Parameters :
                -v      shortcut of --views
                        creates an associated view folder
                        Possibles values :
                        true,false

        × Samples :
                Creates a controller
                  · Ubiquity controller UserController
                with its associated view
                  · Ubiquity controller UserController -v

■ model [tableName] =>
        · Generates a new model.
        · Aliases : create-model
        × Samples :
                  · Ubiquity model User

■ all-models [] =>
        · Generates all models from database.
        · Aliases : create-all-models
        × Samples :
                  · Ubiquity all-models

■ dao [command] =>
        · Executes a DAO command (getAll,getOne,count,uGetAll,uGetOne,uCount).
        · Aliases : DAO
        · Parameters :
                -r      shortcut of --resource
                        The model used

                -c      shortcut of --condition
                        The where part of the query

                -i      shortcut of --included
                        The associated members to load (boolean or array: client.*,commands)

                -p      shortcut of --parameters
                        The parameters for a parameterized query

                -f      shortcut of --fields
                        The fields to display in the response

        × Samples :
                Returns all instances of models\User
                  · Ubiquity dao getAll -r=User
                Returns all instances of models\User and includes their commands
                  · Ubiquity dao getAll -r=User -i=commands
                Returns the User with the id 5
                  · Ubiquity dao getOne -c="id=5"-r=User
                Returns the list of users belonging to the "Brittany" or "Normandy" regions
                  · Ubiquity uGetAll -r=User -c="region.name= ? or region.name= ?" -p=Brittany,Normandy
■ clear-cache [] =>
        · Clear models cache.
        · Parameters :
                -t      shortcut of --type
                        Defines the type of cache to reset.
                        Possibles values :
                        all,annotations,controllers,rest,models,queries,views

        × Samples :
                Clear all caches
                  · Ubiquity clear-cache -t=all
                Clear models cache
                  · Ubiquity clear-cache -t=models

■ init-cache [] =>
        · Init the cache for models, router, rest.
        · Parameters :
                -t      shortcut of --type
                        Defines the type of cache to create.
                        Possibles values :
                        all,controllers,rest,models

        × Samples :
                Init all caches
                  · Ubiquity init-cache
                Init models cache
                  · Ubiquity init-cache -t=models

■ self-update [] =>
        · Updates Ubiquity framework for the current project.

■ admin [] =>
        · Add UbiquityMyAdmin webtools to the current project.

■ crud [crudControllerName] =>
        · Creates a new CRUD controller.
        · Aliases : crud-controller
        · Parameters :
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

        × Samples :
                Creates a crud controller for the class models\User
                  · Ubiquity crud CrudUsers -r=User
                and associates a route to it
                  · Ubiquity crud CrudUsers -r=User -p=/users
                allows customization of index and form templates
                  · Ubiquity crud CrudUsers -r=User -t=index,form

■ auth [authControllerName] =>
        · Creates a new controller for authentification.
        · Aliases : auth-controller
        · Parameters :
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

        × Samples :
                Creates a new controller for authentification
                  · Ubiquity auth AdminAuthController
                and associates a route to it
                  · Ubiquity auth AdminAuthController -p=/admin/auth
                allows customization of index and info templates
                  · Ubiquity auth AdminAuthController -t=index,info

■ rest [restControllerName] =>
        · Creates a new REST controller.
        · Aliases : rest-controller
        · Parameters :
                -r      shortcut of --resource
                        The model used

                -p      shortcut of --path
                        The associated route

        × Samples :
                Creates a REST controller for the class models\User
                  · Ubiquity rest RestUsers -r=User -p=/rest/users

■ restapi [restControllerName] =>
        · Creates a new REST API controller.
        · Aliases : restapi-controller
        · Parameters :
                -p      shortcut of --path
                        The associated route

        × Samples :
                Creates a REST API controller
                  · Ubiquity restapi -p=/rest

■ action [controller.action] =>
        · Creates a new action in a controller.
        · Aliases : new-action
        · Parameters :
                -p      shortcut of --params
                        The action parameters (or arguments)

                -r      shortcut of --route
                        The associated route path

                -v      shortcut of --create-view
                        Creates the associated view
                        Default : [false]

        × Samples :
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
■ info:routes [] =>
        · Display the cached routes.
        · Aliases : info:r,info::routes
        · Parameters :
                -t      shortcut of --type
                        Defines the type of routes to display.
                        Possibles values :
                        all,routes,rest

                -l      shortcut of --limit
                         Specifies the number of routes to return.

                -o      shortcut of --offset
                        Specifies the number of routes to skip before starting to return.

                -s      shortcut of --search
                        Search routes corresponding to a path.

                -m      shortcut of --method
                        Allows to specify a method with search attribute.
                        Possibles values :
                        get,post,put,delete,patch

        × Samples :
                  · Ubiquity info:routes
                  · Ubiquity info:routes -type=rest
                Only the routes with the method post
                  · Ubiquity info:routes -type=rest -m=-post

■ info:model [infoType] =>
        · Returns the model meta datas.
        · Aliases : info-model
        · Parameters :
                -s      shortcut of --separate
                        If true, returns each info in a separate table

                -m      shortcut of --model
                        The model on which the information is sought.

                -f      shortcut of --fields
                        The fields to display in the table.

        × Samples :
                Gets metadatas for User class
                  · Ubiquity info:model -m=User

■ info:models [] =>
        · Returns the models meta datas.
        · Aliases : info-models
        · Parameters :
                -m      shortcut of --models
                        The models on which the information is sought.

                -f      shortcut of --fields
                        The fields to display in the table.

        × Samples :
                Gets metadatas for all models
                  · Ubiquity info:models
                Gets metadatas for User and Group models
                  · Ubiquity info:models -m=User,Group
                Gets all primary keys for all models
                  · Ubiquity info:models -f=#primaryKeys

■ info:validation [memberName] =>
        · Returns the models validation info.
        · Aliases : info-validation,info:validators,info-validators
        · Parameters :
                -s      shortcut of --separate
                        If true, returns each info in a separate table

                -m      shortcut of --model
                        The model on which the information is sought.

        × Samples :
                Gets validators for User class
                  · Ubiquity info:validation -m=User
                Gets validators for User class on member firstname
                  · Ubiquity info:validation firstname -m=User

■ config [] =>
        · Returns the config informations from app/config/config.php.
        · Aliases : info-config,info:config
        · Parameters :
                -f      shortcut of --fields
                        The fields to display.

        × Samples :
                Display all config vars
                  · Ubiquity config
                Display database config vars
                  · Ubiquity config -f=database

■ config:set [] =>
        · Modify/add variables and save them in app/config/config.php. Supports only long parameters with --.
        · Aliases : info-set,set:config,set-config
        × Samples :
                Assigns a new value to siteURL
                  · Ubiquity config:set --siteURL=http://127.0.0.1/quick-start/
                Change the database name and port
                  · Ubiquity config:set --database.dbName=blog --database.port=3307

■ theme [themeName] =>
        · Installs an existing theme or creates a new one if the specified theme does not exists.
        · Aliases : install-theme,install:theme
        × Samples :
                Creates a new theme custom
                  · Ubiquity theme custom
                Install bootstrap theme
                  · Ubiquity theme bootstrap

■ create-theme [themeName] =>
        · Creates a new theme or installs an existing one.
        · Aliases : create:theme
        · Parameters :
                -x      shortcut of --extend
                        If specified, inherits from an existing theme (bootstrap,semantic or foundation).
                        Possibles values :
                        bootstrap,semantic,foundation

        × Samples :
                Creates a new theme custom
                  · Ubiquity create-theme custom
                Creates a new theme inheriting from Bootstrap
                  · Ubiquity theme myBootstrap -x=bootstrap
```

### Project creation
Once installed, the simple `Ubiquity new` command will create a fresh micro installation in the directory you specify. For instance, `Micro new blog` would create a directory named blog containing an Ubiquity project:
```bash
Ubiquity new blog
```
You can see more options about installation by reading the [Project creation section](http://micro-framework.readthedocs.io/en/latest/install.html).

### Running
You can test with the php web server,
from the root folder of your web application, run :
```
Ubiquity serve
```

### Models creation
make sure that the database is configured properly in app/config/config.php file :
```bash
Ubiquity config -f=database
```

![img](https://github.com/phpmv/ubiquity-devtools/blob/master/.github/images/db-conf.png)

Execute the command, make sure you are also in the project folder or one of its subfolders :
```bash
Ubiquity all-models
```
