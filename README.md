# Sallegram
This is the final project developed in the Web Projects subject at university. The
objectives were to implement a web portal similar to Instagram. The result was
an application with the following features:

* Registration : Although any user can see the public posts, to be capable
of sharing posts and do other actions too a registration process must be submitted. The user has to
introduce its personal data and confirm its email by a email confirmation process.
* Follow : Any user registered can follow other users and be followed by them.
* Share posts : The user can share photos with the rest of users or in a private mode,
    where only the users who follow him can see them.
* Comment : A registered user can write a comment in any post he see. He can also
            delete any comment wrote.
* Like : Give a 'like' to a posts the user liked. The user can unlike the posts whenever he wants.
* Notifications : The user receives notifications on the web portal when any other
                  user follows him, comments or likes any of its posts.
* Top posts: A section with the five posts more visited by the users of the application.
* Profile editing: The user can update its personal data.


### Technologies used

We used different languages, frameworks or packages to implement this project.

#### Languages
* PHP : All the back-end of the application is developed in PHP. Access to database, session
        control, handle http requests...
* MySQL : The database is implemented in MySQL following a relational and normalized model.
* Javascript : Used for front-end management and to send asynchronous requests to the back-end.
* HTML and CSS : We used them in Twig templates.

#### Frameworks

* Silex : A PHP Framework based on Symfony.
* Bootstrap : Used for the front-end of the application.
* Symfony: Used for the front-end of the application.

#### Packages/Templates

* Twig : Used as a template to build in PHP.
* Doctrine : Used to manage database storage.

#### Dependency Management

We used composer to install the libraries we need for the project.



#### Setup

In the file Database.php on the path ```./app/config/prod.php``` you can use your own dbname, user and password to access your own Database.

```composer install``` command **must be executed ** before start using the application
correctly. This command will instal the Bootstrap package as well as the other project
packages dependencies.

The project structure follows the MVC pattern (Model - View - Controller).
