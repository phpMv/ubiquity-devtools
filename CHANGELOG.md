# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [unreleased]
- nothing

## [1.4.3] - 2022-12-31
### Updated
- `aclInit` command (add table and models creation)
- `new project` (add init-cache after creation)

## [1.4.2] - 2022-12-19
### Fixed
- add `vlucas/phpdotenv` in composer.json

## [1.4.1] - 2022-12-18
### Fixed
- config cache first initialization

## [1.4.0] - 2022-12-18
### Updated
- Compatibility with Ubiquity 2.5.0
### Added
- env vars support
## [1.3.11] - 2022-05-06
### Updated
- Change root directory to `public` for async platforms

## [1.3.10] - 2022-02-15
### Fixed
- `InstallThemeCmd` pb with `DDDManager`

## [1.3.9] - 2022-02-13

### Updated
- Default vHeader and vFooter templates
- Add create and 2FA views for Auth controllers


## [1.3.8] - 2022-01-01
### Fixed
- cache initialization pb with `new-model` command

### Changed
- `str_pad` usage for questions in commands
- relocate livereload starting (after nonce creation)
- add nonce in template files
- add default nonce

## [1.3.7] - 2021-12-02
### Fixed
- Other models loading +cache re-init in `new-model` command
- Re-init cache before migrations commands
- typo in `addRelation`: `$namepsace`

## [1.3.6] - 2021-12-01

### Added
#### Migrations commands:

```
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
```

```
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
```
#### Models creation

```
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
```

## [1.3.5] - 2021-11-01

### Added
- Adds Domain option (`-o` or `--domain`) for commands: `controller, action, auth, crud-index, model, all-models, dao, rest, rest-api, info-model, info-models, info-validation`

### Updated
- Command names parsing:
  For all commands with multiple parts in the name, the following syntaxes can be used:
  ex: for `Ubiquity all-models`
    - `Ubiquity all_models`
    - `Ubiquity all:models`
    - `Ubiquity allModels`

## [1.3.4] - 2021-10-07
### Fixed
- assets folder location with Ubiquity server

## [1.3.3] - 2021-09-06
### Updated
- Default index page
- ui libraries
 
## [1.3.2] - 2021-07-11
#### Added
- `index-crud` command

## [1.3.1] - 2021-07-06
#### Fixed
- Application root pb (public folder) with embedded web server

## [1.3.0] - 2021-06-15
#### Models generation
- The regeneration of models preserves the code implemented on the existing models.

#### Application root (breaking change)
- For apache and nginX, root folder is set to public folder (for new projects since Ubiquity 2.4.5)

For an old project (created with a version prior to 2.4.5), you have to modify ``index.php`` and move the ``index.php`` and ``.htaccess`` files to the ``public`` folder.

```php
   <?php
   define('DS', DIRECTORY_SEPARATOR);
   //Updated with index.php in public folder
   define('ROOT', __DIR__ . DS . '../app' . DS);
   $config = include_once ROOT . 'config/config.php';
   require_once ROOT . './../vendor/autoload.php';
   require_once ROOT . 'config/services.php';
   \Ubiquity\controllers\Startup::run($config);
```

## [1.2.28] - 2021-02-15
### Updated
- `ubiquity-debug` integration

## [1.2.27] - 2021-03-29
### Fixed
- `info::routes` command bug => no routes displayed

## [1.2.26] - 2021-03-10
### Fixed
- crud & rest commands bug
>Call to a member function asAnnotation on null
>BaseControllerCreator line 58

## [1.2.25] - 2021-02-15
### Fixed
- Bug on new class command with parent class (inheritance)

## [1.2.24] - 2021-02-08
### Added
- `newClass` command for creating a new class

## [1.2.23] - 2021-02-06
### Updated
- replace `livereloadx` with `livereload` 
>livereloadx has not been updated for 2 years, and does not manage file operations (add, delete).

- add livereload with default php server

Starts php web server and livereload (on 35729 port)
```bash
Ubiquity serve
```

Starts php web server without livereload
```bash
Ubiquity serve -n
```

## [1.2.22] - 2021-02-05
### Added
- `live-reload` command for dev server

## [1.2.21] - 2021-01-17
### Added
- `newKey` command for generating the encryption key with Ubiquity-security

## [1.2.20] - 2020-12-31
### Fixed
- new action problem
>Call to a member function asAnnotation() on null

## [1.2.19] - 2020-12-31

### Updated
- Add attributes or annotations fix

## [1.2.18] - 2020-12-11

### Added
- `display-acls` command

### Updated
- composer for php 8

## [1.2.17] - 2020-09-30
### Updated
- add access option (member access) to all-models & create-model cmd
- add db offset param to info:models command
- add OS in version command

## [1.2.16] - 2020-07-28
### Updated
- Update client libraries for new projects (Fomantic 2.8.6)
- Fix session name generation pb (only alphanumeric chars)

## [1.2.15] - 2020-06-27
### Added
- Add `create:command`command
- support for custom commands
### Updated
- move utility classes to `ubiquity-commands` repo
- Update client libraries for new projects (Fomantic 2.8.5)

## [1.2.14] - 2020-05-06
#### Updated
- Update client libraries for new projects (Fomantic 2.8.4, jQuery 3.5.1)
- Add port checking for `Ubiquity serve` command
## [1.2.13] - 2020-03-23
#### Added
- roadrunner server command (Thanks @Lapinskas)

`Ubiquity serve -t=roadrunner`
#### Added

## [1.2.12] - 2020-01-25
#### Added
- Mailer commands (mailer, newMail, sendMail)
- opcache preloading in project creation
#### Changed
- set `help` as default command
- require php 7.4

## [1.2.11] - 2019-11-18
#### Changed
- Update client libraries for new projects (Fomantic 2.8, jQuery 3.4.1, phpMv-ui 2.3)
- require php 7.2

## [1.2.10] - 2019-10-28
#### Added
- Composer create-project
```
composer create-project phpmv/ubiquity-project {projectName}
```

## [1.2.9] - 2019-09-25
### Fixed
- [Cannot set database](https://github.com/phpMv/ubiquity/issues/74)
- https://github.com/phpMv/ubiquity/issues/72
- Fix https://github.com/phpMv/ubiquity-devtools/commit/c06b6704126a4bf56b2a6a52c60aa1d40edcfcdb
### Added
#### Commands
- `composer` [cmd]
 
Samples:
```
Ubiquity composer update
Ubiquity composer nodev
Ubiquity composer optimize
```
- `bootstrap` [cmd]

Execute the `cmd` method from the `.ubiquity/_bootstrap.php` file to prepare an environment.

Sample:
```
Ubiquity bootstrap prod
Ubiquity bootstrap dev
```
## [1.2.8] - 2019-08-01
### Changed
- `model` (`create-model`) command
  - added parameter `d`(`database`): the database connection name defined in config file (use default connection if absent)

Samples:
```
Ubiquity model Author -d=projects
Ubiquity model Author --database=projects
```

- `all-models` (`create-all-models`) command
  - added parameter `d`(`database`): the database connection name defined in config file (use default connection if absent)
  - removed parameter `b`(`dbName`): the database name defined in config file

Samples:
```
Ubiquity all-models -d=projects
Ubiquity create-all-models --database=projects
```
## [1.2.7] - 2019-07-03
### Changed
- Checks if devtools are globally installed in ``sefUpdate`` op
- Integrates ubiquity-webtools ``2.2.0`` (in a separate repository) 

### Fixed
- Remove warning for ``\DS`` constant redefinition (thanks  @TakeMeNL)

## [1.2.6] - 2019-06-13
### Added
- Ubiquity Swoole server: ``Ubiquity serve -t=swoole``
- Parameters for `new` command see [#45](https://github.com/phpMv/ubiquity/issues/45)
  - `siteUrl (i)` : Sets the site base URL.
  - `rewriteBase (e)` : Sets .htaccess file rewriteBase.

Use
```
Ubiquity new fooProject -i=http://foo.local -w=foo
```
or
```
Ubiquity new fooProject --siteUrl=http://foo.local --rewriteBase=foo
```
## [1.2.5] - 2019-05-10
### Fixed
- Warning in pages with php Web server Fix: [#5](https://github.com/phpMv/ubiquity-devtools/issues/5)

## [1.2.4] - 2019-05-09
### Added
- **README.md** file for new projects
- ReactPHP server: ```Ubiquity serve t=react```

### Fixed
- Change of theme without control in the ``ct`` action of ``IndexController`` for new projects : see Ubiquity issue [#38](https://github.com/phpMv/ubiquity/issues/38)

## [1.2.3] - 2019-04-03
 - relooking of the messages for clarity
 
## [1.2.2] - 2019-04-02
 - Fix issue [#22](https://github.com/phpMv/ubiquity/issues/22) (install without -a option bug)
 
## [1.2.0] - 2019-04-01
### Added
- Commands
  - `install-theme` for installing Bootstrap, Semantic-UI or Foundation
  - `create-theme` for creating a new theme (eventually based on a ref theme)
### Changed
- `services.tpl file`
### Fixed
- An exception is thrown In case of problem with the Database connection (in `DataBase::connect` method) see https://github.com/phpMv/ubiquity/issues/12
>The connection to the database must be protected by a `try/catch` in `app/config/services.php`
```
try{
	\Ubiquity\orm\DAO::startDatabase($config);
}catch(Exception $e){
	echo $e->getMessage();
}
```

## [1.1.6] - 2019-03-14
### Added
- New commands
 - ``Ubiquity restapi`` -> create a REST API controller (based on JsonApi)
  - ``Ubiquity rest`` -> create a REST controller associated to a model
  - ``Ubiquity dao`` -> query the database
    - getOne
    - getAll
    - uGetOne
    - uGetAll
    - count
    - uCount

### Fixed
 - [New project template has invalid link to Admin page](https://github.com/phpMv/ubiquity/issues/8)

## [1.1.5] - 2019-02-22
### Added
- New commands
  - ``Ubiquity config`` -> display config file variables
  - ``Ubiquity config:set --database.dbName=blog`` -> modify/add and save config variables
  - ``Ubiquity info:models`` -> display all models metadatas
  - ``Ubiquity info:model -m=User`` -> display metadatas for the selected model
  - ``Ubiquity info:validation`` -> display validation infos for all models or the selected one

### Changed
- Project structure (commands are in separate classes).
- services.tpl template for new project creation

## [1.1.4] - 2019-02-18
### Added
- New commands
  - ``Ubiquity info:routes`` -> display the router informations/test the routes resolution (with -s parameter)

### Changed
- Project structure (src folder).

## [1.1.3] - 2019-02-13
### Added
- New commands
  - ``Ubiquity serve`` -> php internal web server for dev
