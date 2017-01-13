#Publishing App

This application which I have named Publishing app is about public news publishing portal where news can be published and disseminated. The requirements are : 

1 - Any user could register with an email address. The application sends a verification link to the email address. 
When the user clicks the link, the application asks for a new password. 
Now the user is registered and is able to publish news. Without this verification user cannot publish news. (DONE)

2 - After log in the user could see his own published news list, remove or publish a new article. No edit of news is permitted. (DONE)

3 - Users could see the news highlights. Latest 10 news only. (DONE)

4 - Upon clicking an article highlight, the user is able to view a complete article. (DONE)
	 
5 - The user is able to download a PDF file of the displayed news article. (DONE)

6 - An RSS feed can be subscribed to, which includes latest 10 news articles. (DONE)


## CONFIGURE AND RUN THE APP 
 
 
 
To be able to run the app your environnement have to fulfill some requirements : 

    - A webserver (I have used apache 2.4.8, so this version and below should be fine) 
	- A server mysql 5.7.11 or below
    - a php 5.7.19 or below
	- composer 
	- fakeemail - https://github.com/tomwardill/FakeEmail
	
So with these requirements fulfilled, you can run application following these steps :

   - Copy the server folder into the www of your webserver
   - run this command into root folder of application: composer install,
   - Migrate your the test application :
      * Create an empty database and configure it in your .env file with all other connexion database related informations.
 	  * Inside the api folder run :  php artisan migrate 
	  * seed the database : php artisan db:seed
   - Run this command on root folder: php artisan serve
   - Install and run fakeemail through the following link : https://github.com/tomwardill/FakeEmail

Here we go you can navigate by visiting the link http://localhost:8000

 
## NOTE AND FEEDBACK 
 
 