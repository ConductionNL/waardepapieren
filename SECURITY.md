# SECURITY

Security of your common ground component depends on a few factors and is (in fact) for the most part provided by the common ground ecosystem. But there are definitely some steps that you should undertake yourself. We will however first briefly explain the security principles set in place so that you understand how you are being supported and what the limitation of that support is.

## Codebase
First of the code base, if you are extending the common ground-proto-component your code base will exist out of three parts.
- The general API-Platform framework and vendor libraries
- The common ground specific extensions
- Your personal code

For the first two parts there is very little to worry about, symphony has an excellent safety reputation.
   
What you should however definitely do is keeping your dependencies, symfony, doctrine and others, up to date.

## Containers


## Deployment Clusters
It is best practise to run Common Ground components on clusters that comply to [haven](https://haven.commonground.nu). This means compliancy to the best practices for information security, both by industry standards as governmental standards.

## Authentication versus Authorization (Better known as access) 
At this moment the component is protected using a general API key, which is set in the APP_COMMONGROUND_KEY and APP_APPLICATION_KEY in the .env-file, hence in security.commongroundKey and security.applicationKey.

This API key has to be passed to the API using the "Authorization" header when sending a request to the component, granting you the rights to create, read, update and delete.

However, this methodology will shortly be replaced by the use of the [Authorization component](https://github.com/ConductionNL/Authorization-component), which uses public/private key pairs to authenticate the user (application) and contains the authorizations for the application that is authenticated. 

## Automated testing (Github CI/CD)
The component contains an automated CI/CD street for running, testing and releasing the component's containers to a container registry (i.e. Github Container Registry).

This CI/CD street tests the component for a number of parameters, including:

- Is the component working: The components are tested with the use of a postman script that describes the calls that can be made to the component. If all calls succeed, the component is assumed to be working in order. This script is found in the [schema folder](api/public/schema).
- Are the dependencies up-to-date, using the sensiolabs security checker

Also, with the help of StyleCI, the components are checked for adherence to the symfony code standards, while dependabot also checks them for outdated dependencies.

## So what should you do?
-	Follow the steps to regularly merge updates from the common ground proto repository into your codebase
-	Use the provided GitHub Actions CI scipting

