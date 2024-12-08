<?php

//https://github.com/php-curl-class/php-curl-class
require_once 'c:/php/vendor/autoload.php';
use Curl\Curl;

//initialize the loop counter variable for organizations
$org_loop_counter = 0;

//initialize the loop counter variable for repos
$repo_loop_counter = 0;

$debug_path = "C:/Users/Jesse/Documents/Version Control/network_science_data_gathering/app/debug/";

//connect to mysql with PDO
connect_mysql ($pdo);



//query for the last org request link, if there is none start with the default request parameters

//send the curl request for the organizations:
if (owner_request_loop('https://api.github.com/organizations?per_page=100', "Organization"))
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
if (owner_request_loop('https://api.github.com/users?per_page=100', "User"))
{
	//the loop executed successfully:
	echo "the user owner_request_loop completed successfully\n";




}
else
{
	echo "the user owner_request_loop did NOT complete successfully\n";
	
}
*/


function curl_request($url, &$curl_response)
{
	
	$curl = new Curl();

	$curl->setOpt(CURLOPT_SSL_VERIFYPEER , false);
	$curl->setOpt(CURLOPT_HEADER, true);
	$headers = [
    'Authorization: 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',
    "X-GitHub-Api-Version: 2022-11-28"
	];
	
	$curl->setopt(CURLOPT_HTTPHEADER, $headers);

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
function owner_request_loop ($request_url, $owner_type = "Organization")
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
			
			
			//loop through the owners
			
			for ($i = 0; $i < count($json_object) && $i < 3; $i ++)
//			for ($i = 0; $i < count($json_object); $i ++)
			{
				echo "The value of the current owner is: ".var_export($json_object[$i], true)."\n";

				//query for the owner record by owner_id and owner_type values, if it
				if (!owner_exists($json_object[$i]['id'], $owner_type = 'Organization', $owner_id))
				{
					//the owner doesn't exist, insert the owner record

					echo "the owner doesn't exist, insert the owner record\n";

					if (insert_owner ($json_object[$i], $owner_type, $owner_id))
					{
						echo "the owner was inserted successfully\n";
					}
				}
				

				//request all the repos associated with the org, starting with the first page with a maximum of 100 repos per page:
				$return_value = repo_request_loop($json_object[$i]['repos_url']."?per_page=100", $owner_id, $owner_type);
				
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
function repo_request_loop($request_url, $owner_id = null, $owner_type = "Organization")
{
	echo "running repo_request_loop ($request_url, $owner_id, $owner_type)\n";

	//debugging statement
//	return true;

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

				//initialize the variable:
				$parent_repo_id = null;

				//check if the current repo is a fork, if so query for the repo it was forked from and insert the current repo with fork_repo_id:
				echo "check if the current repo has a fork\n";


				if ($json_object[$i]['fork'])
				{
					//the current repository is a forked repository, get the information from the "parent" property
					echo "the current repository is a forked repository, get the information from the fork url\n";


					//request the current repository information:
					if (curl_request($request_url, $parent_repo_curl_response))
					{
						//the detailed repo curl request was successful
						echo "the detailed repo curl request was successful\n";

						//parse the parent object to get the owner and the repo

				//		echo $parent_repo_curl_response;
						file_put_contents($GLOBALS['debug_path']."repo_#".$parent_json_object[$i]['parent']['id'].".txt", $parent_repo_curl_response);

						//parse the json
						if (parse_json_from_api ($parent_repo_curl_response, $parent_json_object, $next_link_url))
						{
							//the json response was parsed successfully
							
							echo "The parent repository name is: ".$parent_json_object[0]['parent']['name']."\n";

							echo "The parent name is: ".$parent_json_object[$i][0]['owner']['login']."\n";

							//check if the owner of the parent repository exists in the database, if not insert it
							if (owner_exists($parent_json_object[0]['parent']['owner']['id'], $parent_json_object[0]['parent']['owner']['type'], $parent_owner_id))
							{
								//the parent repository's owner exists, use the $parent_owner_id for the new repository

								echo "the parent repository's owner exists\n";
								
								
								
								
								
							}
							else
							{
								//the owner does not exist,
								
								echo "the parent repository's owner does not exist, insert it\n";
								
								if (insert_owner ($parent_json_object[0]['parent'], $parent_json_object[0]['parent']['owner']['type'], $parent_owner_id))
								{
									//the parent owner record was inserted successfully


									echo "the parent owner record was inserted successfully\n";
									
									
								}
								else
								{
									//the parent owner record could not be inserted
									$return_value = false;
									
									echo "the parent owner record was NOT inserted successfully\n";
							
									//return false to indicate a DB error in the function call
									return false;
							
								}
								
								
								
							}
							

							//check if the parent repo exists in the database, if not insert it
							if (!repo_exists($parent_json_object[0]['parent']['id'], $parent_repo_id))
							{
								echo "The parent repo does not already exist, insert it\n";
							
								//insert/retrieve the repo_id and use the repo_id for the current repo's fork_repo_id value
							

								//the parent repo does not exist, insert it now:
								if (insert_repo($parent_json_object[0]['parent'], $parent_owner_id, $parent_repo_id))
								{
									echo "The repo was inserted successfully\n";
									
									
								}
								else
								{
									
									echo "Error - The repo was NOT inserted successfully\n";
								
									//the app had a database error, return false to indicate the function call failed
									return false;
								}

							}



							//***the owner of a forked repository is an interesting node, check for all repositories for the owner now that the owner record exists
							
							//use the repos_url property of the parent repo's owner to construct a request for the repos for the parent repo's owner
							if ($return_value = repo_request_loop ($parent_json_object[0]['parent']['owner']['repos_url']."?per_page=100", $parent_owner_id, $parent_json_object[0]['parent']['owner']['type']))
							{
								//the parent repo's owner repo request loop was successful
								echo "the parent repo's owner repo request loop was successful\n";
								
							}
							else
							{
								//the repo_request_loop failed
								
								echo "the parent repo's owner repo request loop was NOT successful\n";
								
								//rollback the transaction:
								$GLOBALS['pdo']->rollback();
							
								//the app had a database error, return false to indicate the function call failed
								return false;
							}
								
						}
						else
						{
							//the parent repository json data could not be parsed
							echo "the parent repository json data could not be parsed\n";
							
						}
						
					}
					else
					{
						
						//the detailed repo curl request was not successful
						echo "the detailed repo curl request was not successful\n";
					}


					
					
					
				}
				
				//use the repo loop query except call it with owner_id = NULL so the owner will be determined by parsing the repo data:


				//check if there are any forks for the current repo:
				if ($json_object[$i]['forks_count'] > 0)
				{
					//request the forks in a recursive function, this version must parse the owner from the response instead of the $owner_id since it is not based on a query for the owner:
					echo "This repo has more than one fork: ".$json_object[$i]['forks_count']."\n";
					
					
					//query for the repos that were forked from the current repo and insert them using the repo loop query except call it with owner_id = NULL and owner_type = NULL so the owner will be determined by parsing the repo's fork data:
					
					
					if (repo_request_loop($json_object[$i]['forks_url']."?per_page=100", null, null))
					{
						//the repo's fork repo request loop was successful
						echo "the repo's fork repo request loop was successful\n";
						
					}
					else
					{
						//the repo's fork repo request loop was NOT successful
						
						echo "the repo's fork repo request loop was NOT successful\n";
						
						//rollback the transaction:
						$GLOBALS['pdo']->rollback();
					
						//the app had a database error, return false to indicate the function call failed
						return false;
					}
				}

				//check if the repo exists in the DB based on the parsed id value
				if (!repo_exists($json_object[$i]['id'], $repo_id))
				{
					echo "The repo does not already exist, insert it\n";

					//the repo does not exist, insert it now:
					
					//we need to include the $parent_repo_id if there is one when we insert this record
					if (insert_repo($json_object[$i], $owner_id, $repo_id, $parent_repo_id))
					{
						echo "The repo was inserted successfully\n";
						

						//the repo insert was successful, commit the transaction
						$GLOBALS['pdo']->commit();
					
					}
					else
					{
						
						echo "Error - The repo was NOT inserted successfully\n";
					
						//rollback the transaction:
						$GLOBALS['pdo']->rollback();
					
						//the app had a database error, return false to indicate the function call failed
						return false;
					}
				}
			}
			
			
			echo "The repo_request_loop has finished the current iteration\n";

			if (!empty($next_link_url))
			{
				//the next link is defined, recursively call repo_request_loop with the $next_link_url

				//request the next page of the list so it can be processed:
				$return_value = repo_request_loop ($next_link_url, $owner_id, $owner_type);
			}			
		}
		else
		{
			echo "Error - The json header content parsing was unsuccessful\n";

			//the app had a runtime error, return false
			return false;
			
		}
	}
	else
	{
		echo "Error - The curl request was unsuccessful\n";

		//the app had a runtime error, return false
		return false;

	}

	return $return_value;
}



function insert_owner ($owner_info, $owner_type, &$owner_id)
{
	echo "running insert_owner(".var_export($owner_info, true).", \$owner_id)\n";

	$query = "insert into github_network.ghnd_owners (source_owner_id, login, html_url, owner_type) VALUES (:source_owner_id, :login, :html_url, :owner_type)";

	echo "the value of \$query is: $query\n";

	$stmt = $GLOBALS['pdo']->prepare($query);

	$stmt->bindValue(":source_owner_id", $owner_info['id']);
	$stmt->bindValue(":login", $owner_info['login']);
	$stmt->bindValue(":html_url", $owner_info['html_url']);
	
	//if the $owner_type argument is not specified use the value from the $owner_info array
	if (is_null($owner_type))
	{
		$owner_type = $owner_info['type'];
	}
	
	$stmt->bindValue(":owner_type", $owner_type);


	if ($stmt->execute())
	{
		//the insert query was successful
		echo "the insert query was successful\n";

		//return the owner_id value so it can be used for processing the data

		//commit the transaction
//        $GLOBALS['pdo']->commit();

		$owner_id = $GLOBALS['pdo']->lastInsertId();
		
		echo "the auto insert value is: ".$owner_id."\n";
		return true;
	}
	else
		return false;

}

//function to query the database to see if the owner record exists:
function owner_exists($source_owner_id, $owner_type, &$owner_id)
{
	//initialize the value of $owner_id
	$owner_id = null;

	echo "running owner_exists($source_owner_id, $owner_type, \$owner_id)\n";
	
	$query = "select owner_id from github_network.ghnd_owners where source_owner_id = :source_owner_id and owner_type = :owner_type";
	
	echo "the value of \$query is: $query\n";
	
	

	// prepare the statement. the placeholders allow PDO to handle substituting
	// the values, which also prevents SQL injection
	$stmt = $GLOBALS['pdo']->prepare($query);

	// bind the parameters
	$stmt->bindValue(":source_owner_id", $source_owner_id);
	$stmt->bindValue(":owner_type", $owner_type);

	$stmt->execute();
	if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$owner_id = $row['owner_id'];
	}	

	//return true if the owner record exists and false if the owner record does not exist:


	echo "the value of owner_exists() is: ".(!is_null($owner_id))."\n";

	return (!is_null($owner_id));
}





function insert_repo ($repo_info, $owner_id, &$repo_id, $parent_repo_id = null)
{
	echo "running insert_repo(".var_export($repo_info, true).", \$repo_id)\n";

	$query = "insert into github_network.ghnd_repos (source_repo_id, fork_repo_id, repo_name, full_name, repo_url, topics, created_at, updated_at, owner_id, parent_repo_id) VALUES (:source_repo_id, :fork_repo_id, :repo_name, :full_name, :repo_url, :topics, STR_TO_DATE(:created_at,'%Y-%m-%dT%H:%i:%sZ'), STR_TO_DATE(:updated_at,'%Y-%m-%dT%H:%i:%sZ'), :owner_id, :parent_repo_id)";

	echo "the value of \$query is: $query\n";

	$stmt = $GLOBALS['pdo']->prepare($query);

	$stmt->bindValue(":source_repo_id", $repo_info['id']);



	//**Fill this in later when the forking is implemented (both ways)
	$stmt->bindValue(":fork_repo_id", null);

	//fill this in once the topics parsing is implemented
	$stmt->bindValue(":topics", null);

	$stmt->bindValue(":repo_name", $repo_info['name']);
	$stmt->bindValue(":full_name", $repo_info['full_name']);
	$stmt->bindValue(":repo_url", $repo_info['html_url']);
	$stmt->bindValue(":created_at", $repo_info['created_at']);
	$stmt->bindValue(":updated_at", $repo_info['updated_at']);
	$stmt->bindValue(":parent_repo_id", $parent_repo_id);
	
	
	$stmt->bindValue(":owner_id", $owner_id);

	if ($stmt->execute())
	{
		//the insert query was successful
		echo "the insert query was successful\n";

		//return the repo_id value so it can be used for processing the data

		//commit the transaction
//        $GLOBALS['pdo']->commit();

		$repo_id = $GLOBALS['pdo']->lastInsertId();
		
		echo "the auto insert value is: ".$repo_id."\n";
		return true;
	}
	else
		return false;

}

//function to query the database to see if the repo record exists:
function repo_exists($source_repo_id, &$repo_id)
{
	//initialize the value of $repo_id
	$repo_id = null;
	
	echo "running repo_exists($source_repo_id, \$repo_id)\n";
	
	$query = "select repo_id from github_network.ghnd_repos where source_repo_id = :source_repo_id";
	
	echo "the value of \$query is: $query\n";
	
	

	// prepare the statement. the placeholders allow PDO to handle substituting
	// the values, which also prevents SQL injection
	$stmt = $GLOBALS['pdo']->prepare($query);

	// bind the parameters
	$stmt->bindValue(":source_repo_id", $source_repo_id);

	$stmt->execute();
	if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$repo_id = $row['repo_id'];
	}	

	//return true if the repo record exists and false if the repo record does not exist:


	echo "the value of repo_exists() is: ".(!is_null($repo_id))."\n";

	return (!is_null($repo_id));
}


function connect_mysql (&$pdo)
{
	// connect to PDO
	$pdo = new PDO("mysql:host=localhost;dbname=github_network", "github_dev", "myadm1n");

	// the following tells PDO we want it to throw Exceptions for every error.
	// this is far more useful than the default mode of throwing php errors
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	
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