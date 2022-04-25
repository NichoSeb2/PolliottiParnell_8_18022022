# PolliottiParnell_8_18022022

# Code quality
[![Maintainability](https://api.codeclimate.com/v1/badges/684a0d862c23e13ca6de/maintainability)](https://codeclimate.com/github/NichoSeb2/PolliottiParnell_8_18022022/maintainability)
[![CI - CD - Symfony](https://github.com/NichoSeb2/PolliottiParnell_8_18022022/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/NichoSeb2/PolliottiParnell_8_18022022/actions/workflows/ci-cd.yml)

# Prerequisite
* A Web Server (Apache, Nginx...)
* PHP 8.0
* Composer
* A Database engine (Mysql, PostgreSQL...)
* Symfony CLI
  * All requirement should be matched, to check them use : `symfony check:requirements`

## Site installation
* Clone or download the project
* Go to project folder in a terminal
* Type `composer install`
* Copy `.env` to `.env.local` and edit sql and app secret parameters
* Configure a new Virtual host in your web server configuration with `public/` folder as DocumentRoot

## Database setup
Type the following to setup the database :
 * `php bin/console doctrine:database:create`
 * `php bin/console doctrine:migrations:migrate`

**Important :** To be able to load the provided data, you need to set the **APP_ENV** to **dev** in your .env.local file.

To load provided samples data : `php bin/console doctrine:fixtures:load --group=samples_data`

## Secure your site
### Admin access 
By default, you can log as admin with : `Administrateur / Password`

### Other users
Samples data come with 2 additional users :
* `Anonyme`, a user anonymous who owns old task, nobody can connect with this account
* `John Doe / Password`, a normal user
