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
Ubiquity devtools (1.3.6)

■ init-cache [] =>
        · Init the cache for models, router, rest.
        · Aliases : init_cache,init:cache,initCache
        · Parameters :
                -t      shortcut of --type
                        Defines the type of cache to create.
                        Possibles values :
                        all,controllers,acls,rest,models
                        Default : [all]

        × Samples :
                Init all caches
                  · Ubiquity init-cache
                Init models cache
                  · Ubiquity init-cache -t=models

■ clear-cache [] =>
        · Clear models cache.
        · Aliases : clear_cache,clear:cache,clearCache
        · Parameters :
                -t      shortcut of --type
                        Defines the type of cache to reset.
                        Possibles values :
                        all,annotations,controllers,rest,models,queries,views
                        Default : [all]

        × Samples :
                Clear all caches
                  · Ubiquity clear-cache -t=all
                Clear models cache
                  · Ubiquity clear-cache -t=models

■ controller [controllerName] =>
        · Creates a new controller.
        · Aliases : create_controller,create:controller,create-controller,createController
        · Parameters :
                -v      shortcut of --views
                        creates an associated view folder and index.html
                        Possibles values :
                        true,false
                        Default : [false]

                -o      shortcut of --domain
                        The domain in which to create the controller.

        × Samples :
                Creates a controller
                  · Ubiquity controller UserController
                with its associated view
                  · Ubiquity controller UserController -v
                Creates a controller in the orga domain
                  · Ubiquity controller OrgaController -o=orga

■ action [controller.action] =>
        · Creates a new action in a controller.
        · Aliases : new-action,new_action,new:action,newAction
        · Parameters :
                -p      shortcut of --params
                        The action parameters (or arguments)

                -r      shortcut of --route
                        The associated route path

                -v      shortcut of --create-view
                        Creates the associated view
                        Possibles values :
                        true,false
                        Default : [false]

                -o      shortcut of --domain
                        The domain in which the controller is.

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

■ auth [authControllerName] =>
        · Creates a new controller for authentification.
        · Aliases : auth-controller,auth_controller,auth:controller,authController
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

                -o      shortcut of --domain
                        The domain in which to create the controller.

        × Samples :
                Creates a new controller for authentification
                  · Ubiquity auth AdminAuthController
                and associates a route to it
                  · Ubiquity auth AdminAuthController -p=/admin/auth
                allows customization of index and info templates
                  · Ubiquity auth AdminAuthController -t=index,info

■ crud-index [crudControllerName] =>
        · Creates a new index-CRUD controller.
        · Aliases : crud-index-controller,crud_index,crud:index,crudIndex
        · Parameters :
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
                        index,form,display,item,itemHome
                        Default : [index,form,display,home,itemHome]

                -p      shortcut of --path
                        The associated route
                        Default : [{resource}]

                -o      shortcut of --domain
                        The domain in which to create the controller.

        × Samples :
                Creates an index crud controller
                  · Ubiquity crud-index MainCrud -p=crud/{resource}
                allows customization of index and form templates
                  · Ubiquity index-crud MainCrud -t=index,form

■ crud [crudControllerName] =>
        · Creates a new CRUD controller.
        · Aliases : crud_controller,crud:controller,crud-controller,crudController
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

                -o      shortcut of --domain
                        The domain in which to create the controller.

        × Samples :
                Creates a crud controller for the class models\User
                  · Ubiquity crud CrudUsers -r=User
                and associates a route to it
                  · Ubiquity crud CrudUsers -r=User -p=/users
                allows customization of index and form templates
                  · Ubiquity crud CrudUsers -r=User -t=index,form
                Creates a crud controller for the class models\projects\Author
                  · Ubiquity crud Authors -r=models\projects\Author

■ new-class [name] =>
        · Creates a new class.
        · Aliases : new_class,new:class,newClass,class
        · Parameters :
                -p      shortcut of --parent
                        The class parent.

        × Samples :
                Creates a new class
                  · Ubiquity class services.OrgaRepository

■ create-theme [themeName] =>
        · Creates a new theme or installs an existing one.
        · Aliases : create_theme,create:theme,createTheme
        · Parameters :
                -x      shortcut of --extend
                        If specified, inherits from an existing theme (bootstrap,semantic or foundation).
                        Possibles values :
                        bootstrap,semantic,foundation

                -o      shortcut of --domain
                        The domain in which to create the theme.

        × Samples :
                Creates a new theme custom
                  · Ubiquity create-theme custom
                Creates a new theme inheriting from Bootstrap
                  · Ubiquity theme myBootstrap -x=bootstrap

■ theme [themeName] =>
        · Installs an existing theme or creates a new one if the specified theme does not exists.
        · Aliases : install_theme,install-theme,install:theme,installTheme
        · Parameters :
                -o      shortcut of --domain
                        The domain in which to install the theme.

        × Samples :
                Creates a new theme custom
                  · Ubiquity theme custom
                Install bootstrap theme
                  · Ubiquity theme bootstrap

■ project [projectName] =>
        · Creates a new #ubiquity project.
        · Aliases : new,create_project
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

                -n      shortcut of --nolr
                        Starts without live-reload.

                -l      shortcut of --lrport
                        Sets the live-reload listen port number.
                        Default : [35729]

                -t      shortcut of --type
                        Sets the server type.
                        Possibles values :
                        php,react,swoole,roadrunner
                        Default : [php]

        × Samples :
                Starts a php server at 127.0.0.1:8090
                  · Ubiquity serve
                Starts a reactPHP server at 127.0.0.1:8080
                  · Ubiquity serve -t=react

■ livereload [path] =>
        · Start the live reload server.
        · Aliases : live-reload,live
        · Parameters :
                -p      shortcut of --port
                        Sets the listen port number.
                        Default : [35729]

                -e      shortcut of --exts
                        Specify extentions to observe .
                        Default : [php,html]

                -x      shortcut of --exclusions
                        Exclude file matching pattern .
                        Default : [cache/,logs/]

        × Samples :
                Starts the live-reload server at 127.0.0.1:35729
                  · Ubiquity live-reload
                Starts the live-reload server at 127.0.0.1:35800 excluding logs directory
                  · Ubiquity live-reload -p=35800 -x=logs/

■ bootstrap [command] =>
        · Executes a command created in app/config/_bootstrap.php file for bootstraping the app.
        · Aliases : boot
        × Samples :
                Bootstrap for dev mode
                  · Ubiquity bootstrap dev
                Bootstrap for prod mode
                  · Ubiquity bootstrap prod

■ help [?] =>
        · Get some help about a dev-tools command.
        × Samples :
                Get some help about crud
                  · Ubiquity help crud

■ version [] =>
        · Return PHP, Framework and dev-tools versions.

■ model [modelName] =>
        · Generates models from scratch.
        · Aliases : create_model,create:model,create-model,createModel,new_model,new:model,new-model,newModel
        · Parameters :
                -d      shortcut of --database
                        The database connection to use
                        Default : [default]

                -o      shortcut of --domain
                        The domain in which to create the model.

                -k      shortcut of --autoincPk
                        The default primary key defined as autoinc.
                        Default : [id]

        × Samples :
                  · Ubiquity model User
                  · Ubiquity model Author -d=projects
                  · Ubiquity model Group,User -o=orga

■ genModel [tableName] =>
        · Generates a new model from an existing table.
        · Aliases : gen_model,gen:model,gen-model,genModel
        · Parameters :
                -d      shortcut of --database
                        The database connection to use
                        Default : [default]

                -a      shortcut of --access
                        The default access to the class members
                        Default : [private]

                -o      shortcut of --domain
                        The domain in which to create the model.

        × Samples :
                  · Ubiquity genModel User
                  · Ubiquity genModel Author -d=projects
                  · Ubiquity genModel Author -d=projects -a=protected

■ all-models [] =>
        · Generates all models from database.
        · Aliases : create-all-models,all_models,all:models,allModels
        · Parameters :
                -d      shortcut of --database
                        The database connection to use (offset)
                        Default : [default]

                -a      shortcut of --access
                        The default access to the class members
                        Default : [private]

                -o      shortcut of --domain
                        The domain in which to create the models.

        × Samples :
                  · Ubiquity all-models
                  · Ubiquity all-models -d=projects
                  · Ubiquity all-models -d=projects -a=protected

■ info-migrations [] =>
        · Returns the migration infos.
        · Aliases : info_migrations,info:migrations,infoMigrations
        · Parameters :
                -d      shortcut of --database
                        The database offset.
                        Default : [default]

                -o      shortcut of --domain
                        The domain in which the database models are.

        × Samples :
                Display all migrations for the default database
                  · Ubiquity info:migrations

■ migrations [] =>
        · Display and execute the database migrations.
        · Aliases : migrations,migrate
        · Parameters :
                -d      shortcut of --database
                        The database offset.
                        Default : [default]

                -o      shortcut of --domain
                        The domain in which the database models are.

        × Samples :
                Display and execute all migrations for the default database
                  · Ubiquity migrations

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

                -o      shortcut of --domain
                        The domain in which the models are.

        × Samples :
                Returns all instances of models\User
                  · Ubiquity dao getAll -r=User
                Returns all instances of models\User and includes their commands
                  · Ubiquity dao getAll -r=User -i=commands
                Returns the User with the id 5
                  · Ubiquity dao getOne -c="id=5"-r=User
                Returns the list of users belonging to the "Brittany" or "Normandy" regions
                  · Ubiquity uGetAll -r=User -c="region.name= ? or region.name= ?" -p=Brittany,Normandy

■ self-update [] =>
        · Updates Ubiquity framework for the current project.

■ composer [command] =>
        · Executes a composer command.
        · Aliases : compo
        × Samples :
                composer update
                  · Ubiquity composer update
                composer update with no-dev
                  · Ubiquity composer nodev
                composer optimization for production
                  · Ubiquity composer optimize

■ admin [] =>
        · Add UbiquityMyAdmin webtools to the current project.

■ rest [restControllerName] =>
        · Creates a new REST controller.
        · Aliases : rest-controller,rest:controller,rest_controller,restController
        · Parameters :
                -r      shortcut of --resource
                        The model used

                -p      shortcut of --path
                        The associated route

                -o      shortcut of --domain
                        The domain in which to create the controller.

        × Samples :
                Creates a REST controller for the class models\User
                  · Ubiquity rest RestUsers -r=User -p=/rest/users

■ restapi [restControllerName] =>
        · Creates a new REST API controller.
        · Aliases : restapi-controller,restapi:controller,restapi_controller,restapiController
        · Parameters :
                -p      shortcut of --path
                        The associated route

                -o      shortcut of --domain
                        The domain in which to create the controller.

        × Samples :
                Creates a REST API controller
                  · Ubiquity restapi -p=/rest

■ info-routes [] =>
        · Display the cached routes.
        · Aliases : info:r,info_routes,info:routes,infoRoutes
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
                All routes
                  · Ubiquity info:routes
                Rest routes
                  · Ubiquity info:routes -type=rest
                Only the routes with the method post
                  · Ubiquity info:routes -type=rest -m=-post

■ info-model [?infoType] =>
        · Returns the model meta datas.
        · Aliases : info_model,info:model,infoModel
        · Parameters :
                -s      shortcut of --separate
                        If true, returns each info in a separate table
                        Possibles values :
                        true,false
                        Default : [false]

                -m      shortcut of --model
                        The model on which the information is sought.

                -f      shortcut of --fields
                        The fields to display in the table.

                -o      shortcut of --domain
                        The domain in which the models is.

        × Samples :
                Gets metadatas for User class
                  · Ubiquity info:model -m=User

■ info-models [] =>
        · Returns the models meta datas.
        · Aliases : info_models,info:models,infoModels
        · Parameters :
                -d      shortcut of --database
                        The database connection to use (offset)
                        Default : [default]

                -m      shortcut of --models
                        The models on which the information is sought.

                -f      shortcut of --fields
                        The fields to display in the table.

                -o      shortcut of --domain
                        The domain in which the models are.

        × Samples :
                Gets metadatas for all models in default db
                  · Ubiquity info:models
                Gets metadatas for all models in messagerie db
                  · Ubiquity info:models -d=messagerie
                Gets metadatas for User and Group models
                  · Ubiquity info:models -m=User,Group
                Gets all primary keys for all models
                  · Ubiquity info:models -f=#primaryKeys

■ info-validation [?memberName] =>
        · Returns the models validation info.
        · Aliases : info_validation,info:validation,infoValidation,info_validators,info-validators,info:validators,infoValidators
        · Parameters :
                -s      shortcut of --separate
                        If true, returns each info in a separate table
                        Possibles values :
                        true,false
                        Default : [false]

                -m      shortcut of --model
                        The model on which the information is sought.

                -o      shortcut of --domain
                        The domain in which the models is.

        × Samples :
                Gets validators for User class
                  · Ubiquity info:validation -m=User
                Gets validators for User class on member firstname
                  · Ubiquity info:validation firstname -m=User

■ config [] =>
        · Returns the config informations from app/config/config.php.
        · Aliases : info_config,info-config,info:config,infoConfig
        · Parameters :
                -f      shortcut of --fields
                        The fields to display.

        × Samples :
                Display all config vars
                  · Ubiquity config
                Display database config vars
                  · Ubiquity config -f=database

■ config-set [] =>
        · Modify/add variables and save them in app/config/config.php. Supports only long parameters with --.
        · Aliases : set_config,set-config,set:config,setConfig
        × Samples :
                Assigns a new value to siteURL
                  · Ubiquity config:set --siteURL=http://127.0.0.1/quick-start/
                Change the database name and port
                  · Ubiquity config:set --database.dbName=blog --database.port=3307

■ mailer [part] =>
        · Displays mailer classes, mailer queue or mailer dequeue.
        × Samples :
                Display mailer classes
                  · Ubiquity mailer classes
                Display mailer messages in queue(To send)
                  · Ubiquity mailer queue
                Display mailer messages in dequeue(sent)
                  · Ubiquity mailer dequeue

■ new-mail [name] =>
        · Creates a new mailer class.
        · Aliases : new_mail,new:mail,newMail
        · Parameters :
                -p      shortcut of --parent
                        The class parent.
                        Default : [\Ubiquity\mailer\AbstractMail]

                -v      shortcut of --view
                        Add the associated view.

        × Samples :
                Creates a new mailer class
                  · Ubiquity newMail InformationMail

■ send-mail [] =>
        · Send message(s) from queue.
        · Aliases : send-mails,send_mails,send:mails,sendMails
        · Parameters :
                -n      shortcut of --num
                        If specified, Send the mail at the position n in queue.

        × Samples :
                Send all messages to send from queue
                  · Ubiquity semdMails
                Send the first message in queue
                  · Ubiquity sendMail 1

■ create-command [commandName] =>
        · Creates a new custom command for the devtools.
        · Aliases : create_command,create:command,createCommand
        · Parameters :
                -v      shortcut of --value
                        The command value (first parameter).

                -p      shortcut of --parameters
                        The command parameters (comma separated).

                -d      shortcut of --description
                        The command description.

                -a      shortcut of --aliases
                        The command aliases (comma separated).

        × Samples :
                Creates a new custom command
                  · Ubiquity create-command custom

■ acl-init [] =>
        · Initialize Acls defined with annotations in controllers.
        · Aliases : acl_init,acl:init,aclInit
        × Samples :
                Initialize Acls
                  · Ubiquity aclInit

■ acl-display [] =>
        · Display Acls defined with annotations in controllers.
        · Aliases : acl_display,acl:display,aclDisplay
        · Parameters :
                -v      shortcut of --value
                        The ACL part to display.
                        Possibles values :
                        all,role,resource,permission,map,acl
                        Default : [acl]

        × Samples :
                Display all defined roles with ACL annotations
                  · Ubiquity aclDisplay role

■ new-key [cypher] =>
        · Generate a new encryption key using a cipher.
        · Aliases : new_key,new:key,newKey
        × Samples :
                Generate a key for AES-128
                  · Ubiquity new-key 128

■ domain [name] =>
        · Creates a new domain (for a Domain Driven Design approach).
        · Aliases : new-domain,new_domain,new:domain,newDomain
        · Parameters :
                -b      shortcut of --base
                        The base folder for domains.
                        Default : [domains]

        × Samples :
                Creates a new domain users
                  · Ubiquity domain users
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
