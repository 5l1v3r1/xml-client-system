# xml-client-system ~ by: BlackVikingPro
Advanced XML Client/User Authentication System | WITH AES 256 SUPPORT!

# About
This project was really about how well security could really go for me.
I built this in hopes of creating a bullet-proof client system in PHP
for whomever needs the security, but doesn't have the money for a SQL
database.

# Description
Advanced XML Client/User Authentication System in PHP 7 with AES-256
encryption support. The code is rather complicated for people not familiar
with PHP, so this is really for experienced programmers (in PHP of course).

# Features
- AES-256 Support (can be modified)
- XML System
- PHP 7
- User Signup / User Signin
- Password-Protected Signup Page
- Google reCaptcha Implementation
- Anti Brute-Force System
- Random Encryption Salt/IV Generation


# Q/A Below!
## What is XML?
XML is Extensible Markup Language. This is a really nice idea to use
for a user/client system because it generates bullet-proof security.
Therefore, no SQL Injection is possible, because it doesn't use SQL!
XML is like a local database (from a certain perspective), though it's
a tad harder to manage because you may have so many customers joining.
This would not be an ideal system to use if you plan on having an app
or multiple platforms that are using the same clients, because this only
works with the server it runs on.

## What types of attacks are still possible?
Well, I've taken into consideration that you can do a number of different attacks
on this web app. While exploitation here is VERY DIFFICULT; it's not
impossible (I'm sure). I realized that CSRF is a possibility,
though I am doing my best to understand how CSRF attacks work and will
eventually release an update that fixes this, as well as more support for
client management.

## So what makes this app so secure?
Well, firstly I use XML; which is not going to be exploited like an SQL
based application could be. The only way for an attacker to even get close to
your clients credentials is if a malicious person(s) had access to the
physical server (or at least FTP access). This way they could download all
the files associated with the accounts to brute force them as well as they
could. I use AES-256-CBC encryption by default (which can easily be changed),
which is considered "Military-Grade" encryption.

## Side notes
I also have noticed that the attacker would simply need to write a simple
php program to combine the salt/iv and the base64 encoded password solution
to generate a plain-text password. Dont' worry, I'm currently working
on this problem to make it where not even the server admin would have access
or enough information to get the password. 
