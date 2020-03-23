# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).
## [Unreleased]
- Nothing
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
- Update client libraries for new projects (Formantic 2.8, jQuery 3.4.1, phpMv-ui 2.3)
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
