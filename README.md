Additional Information
----

For deployment to kubernetes clusters we use Helm 3.

For an in depth installation guide you can refer to the [installation guide](INSTALLATION.md).

- [Contributing](CONTRIBUTING.md)

- [ChangeLogs](CHANGELOG.md)

- [RoadMap](ROADMAP.md)

- [Security](SECURITY.md)

- [Licence](LICENSE.md)

Description
----
The CommonGround Waardepapieren Interface

##About Waardepapieren

The waardenpapieren project aims at digitizing proof from the dutch government for its citizens (e.g. birth certificates, marriage certificates and proof of residence and residential history) it is based on the [W3C claims structure](https://w3c.github.io/vc-data-model/#claims) for standardization.

At the core of the waardepapieren concept is that a “proof” should be applicable both digital and non-digital. Therefore a proof is presented as a PDF containing an JTW based claim, the claim itself however can also be used separately. For more information about the inner workings of waardepapieren see the waardepapieren service at it [repro]( https://github.com/ConductionNL/waardepapieren-service).

##Online test environment
There are several online environments available for testing

1. [Example user interface](https://waardepapieren-gemeentehoorn.commonground.nu)
2. [Example registration desk interface](https://waardepapieren-gemeentehoorn.commonground.nu/waardepapieren-balie)
3. [Example Wordpress implementation](https://dev.zuiddrecht.nl)
4. [Example Waardepapieren Service](https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/waar)
5. [Example Waardepapieren Registration](https://waardepapieren-gemeentehoorn.commonground.nu/api/v1/wari )

##Dependencies

For this repository you will need an API key at a waardepapieren service, a valid api key can be obtained at [Dimpac](https://www.dimpact.nl/) a key for the test api can be obtained from [Conduction](https://condution.nl).

## Setup your local environment
Before we can spin up our component we must first get a local copy from our repository, we can either do this through the command line or use a Git client.

For this example we're going to use [GitKraken](https://www.gitkraken.com/) but you can use any tool you like, feel free to skip this part if you are already familiar with setting up a local clone of your repository.

Open gitkraken press "clone a repo" and fill in the form (select where on your local machine you want the repository to be stored, and fill in the link of your repository on github), press "clone a repo" and you should then see GitKraken downloading your code. After it's done press "open now" (in the box on top) and voilá your codebase (you should see an initial commit on a master branch).

You can now navigate to the folder where you just installed your code, it should contain some folders and files and generally look like this. We will get into the files later, lets first spin up our component!

Next make sure you have [docker desktop](https://www.docker.com/products/docker-desktop) running on your computer.

Open a command window (example) and browse to the folder where you just stuffed your code, navigating in a command window is done by cd, so for our example we could type
cd c:\repos\common-ground\my-component (if you installed your code on a different disk then where the cmd window opens first type <diskname>: for example D: and hit enter to go to that disk, D in this case). We are now in our folder, so let's go! Type docker-compose up and hit enter. From now on whenever we describe a command line command we will document it as follows (the $ isn't actually typed but represents your folder structure):

```CLI
$ docker-compose up
```

Your computer should now start up your local development environment. Don't worry about al the code coming by, let's just wait until it finishes. You're free to watch along and see what exactly docker is doing, you will know when it's finished when it tells you that it is ready to handle connections.

Open your browser type [<http://localhost/>](https://localhost) as address and hit enter, you should now see your common ground component up and running.


##Installation 
This repository comes with an helm installation package and guide for installations on kubernates and haven environments. The installation guide can be found under INSTALLATION.md[](INSTALLATION.md).

##Other Repro’s
*UI*
1. [Burger interface](https://github.com/ConductionNL/waardepapieren) 
2. [Ballie interface](https://github.com/ConductionNL/waardepapieren-ballie)

*Componenten*
1. [Motorblok](https://github.com/ConductionNL/waardepapieren-service) 
2. [Register](https://github.com/ConductionNL/waardepapieren-register) 

*Libraries*
1. [PHP](https://github.com/ConductionNL/waardepapieren-php)

*Plugins*
1. [Wordpress](https://github.com/ConductionNL/waardepapieren_wordpress) 
2. [Typo3](https://github.com/ConductionNL/waardepapieren_typo3) 
3. [Drupal](https://github.com/ConductionNL/waardepapieren_drupal) 


Credits
----

Information about the authors of this component can be found [here](AUTHORS.md)

This component is based on the [ICTU discipl project](https://github.com/discipl/waardepapieren)

Copyright © [Dimpact](https://www.dimpact.nl/) 2020

