# Configurations

### What are configurations?
<p>Every application has an defaultConfiguration. <br>
    In the configuration we have a property called configuration that is of the type array. <br>
    With the configurations inside this array we can execute all kind of actions. <br>
    In this document we define all the possible default options that will work. <br>
    Of course there is the possibility to create new configurations you wish <br>
    to use in templates.</p>
    
    
---
    
## Default Options:

####userPage:
    As value of userPage we give a slug (e.g: /users).
    After we login as a user the userController checks if the
    configuration userPage is set. if this returns as true it will redirect
    the user to the given slug otherwise it sends the user to: 
    app_default_index (the index page).
    We do however have to make sure that the slug we send the user to
    exsists as a template in the component: WRC.
    
    This configuration can be used in: Proto application, Commonground dashboard
    
####cityNames:
    As value of cityNames we give an array of city names.
    When we log in the commongroundProvider checks if this configuration is set.
    if this returns as true the provider will check if the city the user lives in
    corresponds with one of the cities in the array.
    When there is a match the user will be set as a resident.
    The default value for resident is false so if the configuration is not set
    nobody can get a true value for resident.
    
    This configuration can be used in: Proto application

####sideMenu:
    As value of sideMenu we give the uri from an menu object that lives in the WRC component.
    If this configuration is set the menu items of the menu will be shown in the
    left sidebar of the dashboard as links.
    
    This configuration can be used in: Commonground dashboard

####loggedIn & loggedOut:
    As value for loggedIn and loggedOut we give the uri from an menu object that lives
    in the WRC component.
    If this configuration is set the menu items will be put in the
    login or logout dropdown button in the navbar of the application.
    
    This configuration can be used in: Proto application
    
####footer1 - 4:
    As value of footer# we give the uri from an menu object that lives in the WRC component or
    the uri from a template that lives in the component WRC.
    If this configuration is set it wil either use the menu items in a list for your footer.
    Or render the template for the selected footer.
    
    This configuration can be used in: Proto application

####colorSchemeFooter & colorSchemeMenu:
    As value of these configurations we give a string.
    If this configuration is set the string will be used as class for either the menu or the footer.
    This way we can use the class for styling in the style object that is linked to the application.
    
    This configuration can be used in: Proto application

####mainMenu:
    As value of mainMenu we give the uri from an menu object that lives in the wrc component.
    If this configuration is set it will use the menu items in the menu
    to fill the menu bar of the application.
    If this configuration is not set only the login dropdown menu will be shown.
    
    This configuration can be used in: Proto application
    
####home:
    As value of home we give the uri of a template that lives in the WRC component.
    this template is then used as the home page for your application.
    
    This configuration can be used in: Proto application

####newsGroups:
    as value of newsGroups we give an array of the group id's you wish to use.
    The template uses these numbers to determine which group you wish to subsribe to for the newsletter.
    
    This configuration can be used in: Proto application
