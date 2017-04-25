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
Run in a console :

```bash
Micro
```
### Project creation
Once installed, the simple `Micro new` command will create a fresh micro installation in the directory you specify. For instance, `Micro new blog` would create a directory named blog containing a Micro project:
```bash
Micro new blog
```
You can see more options about installation by reading the [Project creation section](http://micro-framework.readthedocs.io/en/latest/install.html).
