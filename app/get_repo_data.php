<?php

//https://github.com/php-curl-class/php-curl-class
require_once 'c:/php/vendor/autoload.php';
use Curl\Curl;

//initialize the loop counter variable for organizations
$org_loop_counter = 0;

//initialize the loop counter variable for repos
$repo_loop_counter = 0;

$debug_path = "C:/Users/Jesse/Documents/Version Control/network_science_data_gathering/app/debug/";


//send the curl request for the organizations:
if (org_request_loop('https://api.github.com/organizations?per_page=100'))
{
	//the loop executed successfully:
	
	echo "the org_request_loop completed successfully\n";




}
else
{
	echo "The curl request was unsuccessful\n";

	
}


function curl_request($url, &$curl_response)
{
	
	$curl = new Curl();

	$curl->setOpt(CURLOPT_SSL_VERIFYPEER , false);
	$curl->setOpt(CURLOPT_HEADER, true);
	$curl->get($url);

	if ($curl->error) {
		$curl->diagnose();
		echo 'Error: ' . $curl->errorMessage . "\n";
	} else {
		$curl_response = $curl->response;
	}
	return (!$curl->error);
}


function parse_json_from_api ($json_content, &$json_object, &$next_link_url)
{
	echo "running parse_json_from_api (\$json_content, &\$json_object, &\$next_link_url)\n";
	
	$return_value = true;

//	if (preg_match($pattern='/link\:.+\<(.+)\>; rel="next",.+(\[.+\])/s', $json_content, $matches))	//regexp to get both the next link and the json content
	if (!($next_link_found = preg_match($pattern='/link\:.+\<(.+)\>; rel="next",/s', $json_content, $matches)))
	{
		//the next link was not found
		echo "The next link was not found\n";
		$next_link_url = null;
	}
	else	//the next link was found
		$next_link_url = $matches[1];


	echo "The value of \$next_link_url is: ".$next_link_url."\n";

	
	//parse the content part of the data:
	if (preg_match('/(\[.+\])/s', $json_content, $matches))
	{
//			echo "the value of matches is: ".var_export($matches, true)."\n";

		//convert the json content to a json object:
		$json_object = json_decode($matches[1], true);
	}
	else
	{
		echo "The json object content parsing was unsuccessful\n";
		$return_value = false;
		
	}
	return $return_value;
}



//this function requests the organizations in the GitHub network
function org_request_loop ($request_url)
{
	echo "running org_request_loop ($request_url)\n";

	$return_value = true;
	
	//increment the organization loop counter to stop the script before it loops too many times
	$GLOBALS['org_loop_counter']++;
	
	echo "The value of \$GLOBALS['org_loop_counter'] is: ".$GLOBALS['org_loop_counter']."\n";
	
	if ($GLOBALS['org_loop_counter'] > 2)
	{
		echo "The org_loop_counter is above 2, exit the program\n";
		exit;
	
	}
	
	//send the curl request for the organizations:
	if (curl_request($request_url, $curl_response))
	{

//		echo $curl_response;
		file_put_contents($GLOBALS['debug_path']."org_".$GLOBALS['org_loop_counter'].".txt", $curl_response);

		//parse the json
		if (parse_json_from_api ($curl_response, $json_object, $next_link_url))
		{
			//the org json data was parsed successfully
			
			
			//loop through the organizations
			
			for ($i = 0; $i < count($json_object) && $i < 3; $i ++)
//			for ($i = 0; $i < count($json_object); $i ++)
			{
				echo "The value of the current organization is: ".var_export($json_object[$i], true)."\n";

				//query for the owner record by owner_id and org_owner values, if it
					//if so then retrieve the id so it can be used for inserting repo data
					//if not then insert the owner record and return the id

				//set this as the pk id
				$owner_id = null;
				

				//request all the repos associated with the org:
				$return_value = repo_request_loop("https://api.github.com/orgs/".$json_object[$i]['id']."/repos?per_page=100&page=1", $owner_id, true);
				
			}
			

			if (!empty($next_link_url))
			{
				//the next link is defined, recursively call org_request_loop with the $next_link_url
				$return_value = org_request_loop ($next_link_url);
			}			
		}
		else
		{
			echo "The json header content parsing was unsuccessful\n";
			$return_value = false;
			
		}
	}
	else
	{
		echo "The curl request was unsuccessful\n";
		$return_value = false;
	}

	return $return_value;
	
}

//recursive function that loops through the repos for a given org or user identified by id and type
function repo_request_loop($request_url, $owner_id, $org_owner = true)
{
	echo "running repo_request_loop ($request_url, $owner_id, $org_owner)\n";

	$return_value = true;
	
	//increment the repo loop counter to stop the script before it loops too many times
	$GLOBALS['repo_loop_counter']++;
	
	echo "The value of \$GLOBALS['repo_loop_counter'] is: ".$GLOBALS['repo_loop_counter']."\n";
	
/*	if ($GLOBALS['repo_loop_counter'] > 2)
	{
		exit;
	}	
*/	
	//send the curl request for the repos:
	if (curl_request($request_url, $curl_response))
	{

//		echo "The return value is: \n";

//		echo $curl_response;

//		echo $curl_response;
		file_put_contents($GLOBALS['debug_path']."repo_".$GLOBALS['repo_loop_counter'].".txt", $curl_response);

		//parse the repo json content
		if (parse_json_from_api ($curl_response, $json_object, $next_link_url))
		{
			//the org json data was parsed successfully
			
			
			//loop through the repos
//			for ($i = 0; $i < 5; $i ++)
			for ($i = 0; $i < count($json_object); $i ++)
			{
//				echo "The value of the current repo is: ".var_export($json_object[$i], true)."\n";
				
				echo "The value of the current repo is: ".$json_object[$i]['full_name']."\n";

				//check if the repo exists in the DB based on the parsed id value
					//if it exists, do not insert it
					//if it does not exist, insert it
					
					
					
					
				//check if there are any forks for the current repo:
				if ($json_object[$i]['forks_count'] > 0)
				{
					//request the forks in a recursive function, this version must parse the owner from the response instead of the $owner_id since it is not based on a query for the owner:
					echo "This repo has more than one link: ".$json_object[$i]['forks_count']."\n";
				}
			}
			
			
			echo "The repo_request_loop has finished the current iteration\n";

			if (!empty($next_link_url))
			{
				//the next link is defined, recursively call repo_request_loop with the $next_link_url

				//request the next page of the list so it can be processed:
				//$return_value = repo_request_loop ($next_link_url, $owner_id, $org_owner);
			}			
		}
		else
		{
			echo "The json header content parsing was unsuccessful\n";
			$return_value = false;
			
		}
	}
	else
	{
		echo "The curl request was unsuccessful\n";
		$return_value = false;
	}

	return $return_value;
	
	
	
}


function user_request ()
{
	
	
	
}


function user_repo_request()
{
	
	
	
}



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