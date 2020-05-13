# Fondo Merende
Fondo Merende is a web management software for office snacks supplies.

## Requirements
1. Any web server that supports [PHP](https://www.php.net/) ([nginx](https://nginx.org/) is recommended for clean URLs).
2. [MariaDB](https://mariadb.org/) or [MySQL](https://www.mysql.com/).
3. [APCu](https://www.php.net/manual/en/book.apcu.php), if you would like to have cached CSS, translations and images (optional).

## Installation
1. Clone the repository and configure your webserver to expose just the [public directory](public). If you use nginx take a look at [nginx.conf.sample](nginx.conf.sample).  
2. Create a MariaDB or MySQL database, extract [db-structure.sql.bz2](db-structure.sql.bz2) and run the SQL script on the database.  
3. Create a config.php file from [config.php.sample](config.php.sample).  

### Options  

#### BASE\_DIR
The base dir of the website. You need to change the default value if you want to install Fondo Merende in a subdirectory.

#### AUTH\_KEY
The authorization key to access the API. Like for passwords long is better.  
**YOU SHOULD ALWAYS CHANGE THE DEFAULT VALUE!**

#### MAINTENANCE
It defines if maintenance mode is on. Set it to `true` to activate it.  
While on maintenance Fondo Merende website and API will show a brief message explaning that the service is not available at the moment, but it should be back in short time. It's useful if you need to make some code changes directly on your production server, if you have to edit the database, or obviously if you are updating the software.

#### APCU\_INSTALLED
It defines if APCu is installed. Set it to `true` to activate caching of CSS, translations and images.  
**As the option name says, to use cached assets you must have APCu installed.**

#### CLEAN\_URLS
It tells Fondo Merende website to use pretty, "legible" and localised URL in links. Set it to `true` to activate it.  
**In order to make clean URLs work you have to copy the rewrites from [nginx.conf.sample](nginx.conf.sample) in your site configuration file, otherwise, if you don't use nginx, you will need to rewrite those rules for your web server of choice.**  
If you add a new language to the translations you must add new rewrite rules to the web server configuration as well.

#### DB\_SERVER
The host of the database. You need to change the default value if you want to install the database on a different computer from the one with the web server. 

#### DB\_USER
The user that can access Fondo Merende database to write and read. Usually it's better to avoid using root: instead create a dedicated user who has access to the required database only.

#### DB\_PASSWORD
The password for the user that can access Fondo Merende database. As for penises a long one is definitely better.

#### DB\_NAME
The name of Fondo Merende database. Change it accordingly to the name of the database you've crated during the installation second step.

## Usage
You can visit your Fondo Merende like every other website or call its API to develop a client application.  
The website is entirely text-based since it was designed to be cheap to host, fast and small both in script execution and development cycle; it doesn't need JavaScript, but the few lines of it are compatible with [LibreJS](https://www.gnu.org/software/librejs/).  
Fondo Merende works fairly well with mobile phones too, because its CSS is mobile friendly and mobile first.

### Create user
To create an user you need to go to Fondo Merende website, click on Add user in the login page and fill out the registration form. You might as well send the Add user request from Postman or any client application (see the [API Postman collection](Postman/Fondo&#32;Merende.postman_collection.json) for further details).  
For security reasons after the registration every user needs to be activated to carry out main actions like adding, buying and eating snacks. To activate an user set the active column of Fondo Merende users database table to `1`.

### API
To call the API you just need to point to [process-request.php](public/process-request.php) or, if have clean URLs on, to api.  
You can find more details about it in the [API Postman collection](Postman/Fondo&#32;Merende.postman_collection.json).  
A little piece of trivia: Fondo Merende was initially developped as a set of APIs for a future client application, then it was later modified to use its own application programming interface to serve web pages with the requested information. This design choice means that every new feature is automatically shared between the website and the API.

## Roadmap
Things that I would like to do for the project future (in order of when I'm planning to tackle them):

1. Get out of beta as soon as possible!
2. Start keeping a changelog.
3. Write some real documentation and not just that [API Postman collection](Postman/Fondo&#32;Merende.postman_collection.json).
4. Design a GUI for admin users to activate new ones, instead of having to edit a column in the database.
5. Implement some sort of equally distributed penalty for "lost snacks" (AKA snacks eaten without recording them on Fondo Merende). This power should be handed only to admin users and it should be employed through the aforementioned GUI.
6. A possible alternative to the previous point would be to create a tax model.
7. Write a web install script, like the one you're prompted with when you first set up WordPress or PrestaShop.
8. Add nice looking graphics... maybe.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to modify.  
Be sure to check out the dev branch before proposing new features, because they might be already in the works!

## Authors and acknowledgment
Developed by Matteo Bini.  
Italian translation by Matteo Luchetta.  
README template by [Make a README](https://www.makeareadme.com/).

## License
[GPL-3.0](https://www.gnu.org/licenses/gpl-3.0)
