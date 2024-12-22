<?php

include_once (($current_dir = dirname(__FILE__))."/functions/config.php");
include_once ($current_dir."/functions/git_functions.php");

include_once ($current_dir."/functions/owner_functions.php");
include_once ($current_dir."/functions/repo_functions.php");




//initialize the loop counter variable for organizations
$org_loop_counter = 0;

//initialize the loop counter variable for repos
$repo_loop_counter = 0;

//$debug_path = $current_dir."./debug/";

//connect to mysql with PDO
connect_mysql ($pdo);



//query for the last org request link, if there is none start with the default request parameters

//begin the new transaction:
$GLOBALS['pdo']->beginTransaction();

//reprocess all owners that have not been marked as successfully processed owner_processed_yn = 1
echo "\nreprocess all owners that have not been marked as successfully processed owner_processed_yn = 1\n";
if (reprocess_owners())
{
	echo "the unprocessed owners have all been successfully processed\n";
}
else
{
	echo "one or more unprocessed owners have not been successfully processed\n";
}

//reprocess all repos that have not been marked as successfully processed repo_processed_yn = 1
echo "\nreprocess all repos that have not been marked as successfully processed repo_processed_yn = 1\n";
if (reprocess_repos())
{
	echo "the unprocessed repos have all been successfully processed\n";
}
else
{
	echo "one or more unprocessed repos have not been successfully processed\n";
}


echo "\nprocess the owner_request_loop()\n";
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


?>