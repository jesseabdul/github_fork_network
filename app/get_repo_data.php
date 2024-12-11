<?php

include_once (($current_dir = dirname(__FILE__))."/functions/config.php");
include_once ($current_dir."/functions/git_functions.php");





//initialize the loop counter variable for organizations
$org_loop_counter = 0;

//initialize the loop counter variable for repos
$repo_loop_counter = 0;

$debug_path = "L:/Documents/Version Control/network_science_data_gathering/app/debug/";

//connect to mysql with PDO
connect_mysql ($pdo);



//query for the last org request link, if there is none start with the default request parameters

//begin the new transaction:
$GLOBALS['pdo']->beginTransaction();



//query all of the orgs in the DB that have not been successfully processed yet

	//write and execute a function for this





//send the curl request for the organizations:
if (owner_request_loop('https://api.github.com/organizations?per_page=100', 0, "Organization"))
{
	//the loop executed successfully:
	echo "the organization owner_request_loop completed successfully\n";




}
else
{
	echo "the organization owner_request_loop did NOT complete successfully\n";
	
}

//query for the last user request link 
/*
//send the curl request for the users:
if (owner_request_loop('https://api.github.com/users?per_page=100', 0, "User"))
{
	//the loop executed successfully:
	echo "the user owner_request_loop completed successfully\n";




}
else
{
	echo "the user owner_request_loop did NOT complete successfully\n";
	
}
*/



//algorithm:

//request organizations in a loop

	//parse header for subsequent pages link


	//parse json content for organizations
	
	
	//loop through each organization
	
		//insert the organization into the DB if it doesn't already exist
		
		
		//request organization repos in a loop
		
			//parse header for subsequent pages link


			//parse json content for repos

			//loop through each repo in the organization
				
				//insert the repo and associate with the org (node type is "org")
				
				//check if there are any forks, if so then loop through them and request each repo and link it to the source repo
				
					//if forks > 0 then use the fork URL to identify all forked urls and insert into repos with the appropriate fork_repo_id
					//check if owner already exists, if so then use the appropriate ID and if not then insert the new owner record with the appropriate type

				
				
				



//make the org loop a recursive function


//make the user loop a recursive function, stop when no/null "next" link


	//can make both org and user loops the same function with a different type argument 



//create a function for parsing/looping through the repos and inserting them using the "next" link



?>