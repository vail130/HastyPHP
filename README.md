# Use-Case

+ You hate doing the following:
	* Creating Sign In/Register forms
	* Creating Forgot/Reset/Change Password systems
	* Creating HTML email templates, because TABLE, TBODY, TR and TD suck
	* Setting up Bcrypt, jsmin, cssmin, META tags, and mod_rewrite for the millionth time
	
+ You want to do the following:
	* Just work on the freaking project!

# About HastyPHP

The purpose of HastyPHP is to get you up and running as quickly and easily
as possible in web projects using Apache/MySQL/PHP. *It's not perfect.* It's not fully
HTML5, there is not a clear designated endpoint for AJAX calls, and
a thousand other things could be added. However, **it is simple!** I hope that it only
takes you about 10 minutes to really understand what's going on, and maybe only another
10 minutes to be up and running.

# Highlights

These are some nice-to-have features that I'll highlight:

+ JS minification
+ Server-size LESS compilation into a CSS file, which is minified
+ Uses Bcrypt for password hashing
+ Most most META tags, including open graph and itemprop (g+) are ready to go
+ Already includes a few helpful JS and PHP libraries
+ Full functionality without any JavaScript

# Getting Started

## /global.php

First, change the name of the top-level directory to your project name. Then, go
through /global.php item-by-item, and change everything that isn't accurate; it's
pretty self-explanatory. Here are a few things you may want to know:

+ $SITE['url'] is used globally for URLs pointing internally
+ $SITE['path'] is really only used for accessing non-web-accessible resources
	on the server-side (requiring, including, loading images for processing, etc.)
+ Meta-data variables ($SITE['displayName'], $SITE['description'], etc.) and references
	to specific icons are used in <link> and <meta> tags in /views/template.php
+ Database-related variables ($SITE['dbhost'], etc.) are using in methods in 
	/controllers/DatabaseController.php
+ Mailing-related variables ($MAIL) are used in the Email module (/models/Email.php).

## /dbschema.sql & /index.php

After you have /global.php taken care of, presumably you have set up a database and
loaded the relevant information into the $SITE variable. Now, build your schema in
/dbschema.sql, and actually create the database in phpMyAdmin or whatever you're using.
Then, in /index.php, you should un-comment lines 9, 10 and 17, which instantiate
DatabaseController, establish a link to the MySQL database and terminate the link,
respectively.  
  
In /index.php, you'll also notice a couple obnoxious ALL CAPS variables,
specifically $PAGE and $PARAMS. $PAGE is the name of the page, used throughout the
views that load, and $PARAMS is an associative array of values sent to the views that
load from the individual controllers that /controllers/RouteController.php loaded for a
page. These variables are usually values passed as a parameter in the URL in a GET
request that the website will use to retrieve a data model from the database for use in
a view. Examples include a code to fetch a specific request or an ID of a public item
(article, product, etc.).

## /views/template.php & /views/home.php

Now, you're ready to get started. Customize your header and footer in /views/tempate.php,
and set up the initial landing page in /views/home.php. As you can see in
/views/template.php, every page loads the JS file /js/site.js, and each page loads
another JS file in the same directory by the name of the page, but these files only
load if they exist.

## Resources

Check out /php-lib for the php libraries already included, and look in /js/util for the
JS libraries at your disposal. /views/template.php contains a few JavaScript includes
that are commented out in the HTML. For styling, use /css/style.less; /css/style.css
is compiled from /css/style.less on the server-side in /views/template.php on lines
61 - 65. /img/ contains a few, basic resources, along with the bootstrap icons.

# Getting familiar

+ Analyze and understand how /index.php and /controllers/RouteController.php work
	* Strict page validation: if the URL is not exactly correct, the user is redirected
		to the default URL.
	* Names of pages are added to the $pages array based on what kind of session exists
	* For page-specific validation and data manipulation, follow the pattern in
		/controllers/RouteController.php, which dispatches a specialized controller
		that handles all cases for a given $PAGE.
+ See what methods /models/Module.php entails and how they work. Note that they will all,
	most likely, be called through a descendent class/object
	* getUniqueName($field, $numChars) is good for producing a unique code for a data
		model
	* isValidID tells you if a data model ID is valid - good for page-specific validation
	* getRecord, setRecord are good for getting/setting a specific value for a data model
		instance
	* setModule resets multiple data fields in the database in one query
	* formatTimeAgo can be handy
	* filterCount takes a 1-D array, sorts the data, filters out duplicates, and returns
		an array of the unique data and another array with corresponding frequency of
		the data values
	* enhanceText removes HTML, renders links and turns new line characters into HTML
		line breaks
+ Implementing user referral system
	* Include a form input with name 'ref' that includes a referral code
	* Include custom logic at lines 91 & 92 of /controllers/RegisterController.php for
		giving credit to the referring and referred users for the referral

# Adding a data model to the database

## Defining the data model

As you can see in all of the models, they each extend /models/Module.php, and they all
have a few protected static variables. These are quite important.

+ $module_name should be the lowercase name of the module
+ $table_name should be the plural of the module name
+ $allFields should be an array of arrays, each with:
	* name, which matches the name of the field in the database
	* type, which can be int, string, or float
	* max, which is maximum length for strings and value for int/float (-1 means no limit)
	* min, which is minimum length for strings and value for int/float
+ $limFields is an array that restricts an entry in $allFields to specific values. To do
so:
	* Add a string to $limFields comprising the field name concatenated with 'Array'.
	* Create another protected static variable by the name of the entry you just added to
	$limFields, and set its value to an array of the possible values for this field.
+ Example: see lines 5 - 48 in /models/Email.php

## Creating a database entry for a data model

To create a database entry for a data model, you'll be using the create() method, found
in /models/Module.php, but you'll be calling it through the actual model that you want to
create an entry for, such as User. So, you'd actually be calling User::create(). This
method takes one parameter, which should be an array with all of the names of the fields
for the relevant data model with some sort of value, if it it's false, 0, or an empty
string. For an example, see lines 167 - 177 in /models/Email.php.
