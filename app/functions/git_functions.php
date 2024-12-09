<?php

//https://github.com/php-curl-class/php-curl-class
require_once 'c:/php/vendor/autoload.php';
use Curl\Curl;


function curl_request($url, &$curl_response)
{
	
	$curl = new Curl();

	$curl->setOpt(CURLOPT_SSL_VERIFYPEER , false);
	$curl->setOpt(CURLOPT_HEADER, true);
	$curl->setHeader('Authorization', 'Bearer '.GH_PAT);

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
	if (preg_match('/\R\R(.+)/s', $json_content, $matches))
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
function owner_request_loop ($request_url, $owner_request_counter, $owner_type = "Organization")
{
	echo "running org_request_loop ($request_url)\n";

	$return_value = true;
	
	//increment the owner loop counter to stop the script before it loops too many times
	$owner_request_counter++;
	
	echo "The value of \$owner_request_counter is: ".$owner_request_counter."\n";
	
	if ($owner_request_counter > 2)
	{
		echo "The owner_request_counter is above 2, exit the program\n";
		exit;
	
	}
	
	//send the curl request for the organizations:
	if (curl_request($request_url, $curl_response))
	{

//		echo $curl_response;
		file_put_contents($GLOBALS['debug_path']."owner_".$owner_type."_".$owner_request_counter.".txt", $curl_response);

		//parse the json
		if (parse_json_from_api ($curl_response, $json_object, $next_link_url))
		{
			//the org json data was parsed successfully
			
			
			//loop through the owners
			
			for ($i = 0; $i < count($json_object) && $i < 3; $i ++)
//			for ($i = 0; $i < count($json_object); $i ++)
			{
//				echo "The value of the current owner is: ".var_export($json_object[$i], true)."\n";


				//set the current owner json array for the owner_type array element based on the value of $owner_type 
				$json_object[$i]['type'] = $owner_type;
				$json_object[$i]['html_url'] = "https://github.com/".$json_object[$i]['login'];
				

				//attempt to process the owner
				if (process_owner($json_object[$i], $owner_id))
				{
					//request all the repos associated with the org, starting with the first page with a maximum of 100 repos per page:
					if (repo_request_loop($json_object[$i]['repos_url']."?per_page=100", $owner_id))
					{
						//the repo_request_loop for the current owner was processed successfully
						echo "the repo_request_loop for the current owner was processed successfully\n";
						
					}
					else
					{
						//the repo_request_loop for the current owner was NOT processed successfully
						echo "the repo_request_loop for the current owner was NOT processed successfully\n";
						
					}
				}
				else
				{
					//the current owner could not be processed successfully
					echo "the current owner could not be processed successfully\n";
					return false;
				}
			}
			
			//check if the next_link_url is defined
			if (!is_null($next_link_url))
			{
				//the next link is defined, recursively call org_request_loop with the $next_link_url
				if (owner_request_loop ($next_link_url, $owner_request_counter, $owner_type))
				{
					//the org_request_loop was processed successfully:
					echo "the org_request_loop was processed successfully\n";
				}
				else
				{
					//the org_request_loop for the next link was NOT processed successfully:
					echo "the org_request_loop for the next link was NOT processed successfully\n";
					return false;					
				}
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

//recursive function that loops through the repos for a given org or user identified by id 
function repo_request_loop($request_url, $owner_id = null, $parent_repo_id = null)
{
	echo "running repo_request_loop ($request_url, $owner_id, $parent_repo_id)\n";

	echo "the repo loop counter is ".(++$GLOBALS['repo_loop_counter'])."\n";

	//debugging statement
//	return true;

	$return_value = true;
	
	//send the curl request for the repos:
	if (curl_request($request_url, $curl_response))
	{
		//the repos request was successful:

		//save the contents in a temporary file for debugging purposes
		file_put_contents($GLOBALS['debug_path']."repo_".($GLOBALS['repo_loop_counter']).".txt", $curl_response);

		//parse the repo json content
		if (parse_json_from_api ($curl_response, $json_object, $next_link_url))
		{
			//the repo json data was parsed successfully
			
			echo "the repo json data was parsed successfully\n";
			
			//loop through the repos
			for ($i = 0; $i < count($json_object); $i ++)
			{
//				echo "The value of the current repo is: ".var_export($json_object[$i], true)."\n";
				
				echo "The value of the current repo is: ".$json_object[$i]['full_name']."\n";


				//check if the repo exists in the DB based on the parsed id value
				if (!repo_exists($json_object[$i], $repo_id))
				{
					//the current repo does not already exist, process it
					echo "the current repo does not already exist, process it\n";

					//initialize the variable:
					$parent_repo_id = null;

					//check if the current repo is a fork, if so query for the repo it was forked from and insert the current repo with fork_repo_id:
					echo "check if the current repo is a fork\n";


					if ($json_object[$i]['fork'])
					{
						//the current repository is a forked repository, get the information from the "parent" property
						echo "the current repository is a forked repository, get the information from the fork url\n";


						//request the current repository information:
						if (curl_request($json_object[$i]['url'], $parent_repo_curl_response))
						{
							//the detailed repo curl request was successful
							echo "the detailed repo curl request was successful\n";

							//parse the parent object to get the owner and the repo

					//		echo $parent_repo_curl_response;
							file_put_contents($GLOBALS['debug_path']."repo_#".$json_object[$i]['id'].".txt", $parent_repo_curl_response);

							//parse the json
							if (parse_json_from_api ($parent_repo_curl_response, $parent_json_object, $next_link_url))
							{
								//the json response was parsed successfully

								echo "The value of \$parent_json_object is: " . var_export($parent_json_object, true)."\n";

								
								echo "The parent repository name is: ".$parent_json_object['parent']['name']."\n";

								echo "The parent name is: ".$parent_json_object['owner']['login']."\n";
								
								//initialize the parent_owner_id to be null
								$parent_owner_id = null;
								
								//process the current parent repo:
								if (process_repo($parent_json_object['parent'], ($parent_owner_id), $parent_repo_id))
								{
									//the parent repo was processed successfully:
									echo "the parent repo was processed successfully\n";

								}
								else
								{
									//the parent repo was NOT processed successfully:
									
									echo "the parent repo was NOT processed successfully\n";
									
								}


								//***the owner of a forked repository is an interesting node, check for all repositories for the owner now that the owner record exists
								
								
								echo "process all of the parent repo's owner's repos\n";
								
								//use the repos_url property of the parent repo's owner to construct a request for the repos for the parent repo's owner
								if (repo_request_loop ($parent_json_object['parent']['owner']['repos_url']."?per_page=100", $parent_owner_id))
								{
									//the parent repo's owner repo request loop was successful
									echo "the parent repo's owner repo request loop was successful\n";
									
								}
								else
								{
									//the repo_request_loop failed
									
									echo "the parent repo's owner repo request loop was NOT successful\n";
									
									//rollback the transaction:
//									$GLOBALS['pdo']->rollback();
								
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





					echo "The repo does not already exist, insert it\n";

					//the repo does not exist, insert it now:
					
					//we need to include the $parent_repo_id if there is one when we insert this record
					$json_object[$i]['parent_repo_id'] = $parent_repo_id;

					
					
					//process the owner first, then insert the repo:
					if (process_owner ($json_object[$i]['owner'], $owner_id))
					{
						//the repo owner record was processed successfully

						//insert the repo
						if (insert_repo($json_object[$i], $owner_id, $repo_id))
						{
							echo "The repo was inserted successfully\n";
						}
						else
						{
							
							echo "Error - The repo was NOT inserted successfully\n";
						
							//rollback the transaction:
//							$GLOBALS['pdo']->rollback();
						
							//the app had a database error, return false to indicate the function call failed
							return false;
						}
					}
					else
					{
						//the owner was not processed successfully:

						echo "the owner for the current repo was not processed successfully\n";

						//rollback the transaction:
//						$GLOBALS['pdo']->rollback();

						return false;
					
					}

					
					//use the repo loop query except call it with owner_id = NULL so the owner will be determined by parsing the repo data:





					//check if there are any forks for the current repo:
					if ($json_object[$i]['forks_count'] > 0)
					{
						//request the forks in a recursive function, this version must parse the owner from the response instead of the $owner_id since it is not based on a query for the owner:
						echo "This repo has more than one fork: ".$json_object[$i]['forks_count']."\n";
						
						
						//query for the repos that were forked from the current repo and insert them using the repo loop query except call it with owner_id = NULL so the owner will be determined by parsing the repo's fork data:
						
						//process the current repo's forks and provide the $repo_id as the parent_repo_id of all the associated forked repos
						if (repo_request_loop($json_object[$i]['forks_url']."?per_page=100", null, $repo_id))
						{
							//the repo's fork repo request loop was successful
							echo "the repo's fork repo request loop was successful\n";
							
						}
						else
						{
							//the repo's fork repo request loop was NOT successful
							
							echo "the repo's fork repo request loop was NOT successful\n";
							
							//rollback the transaction:
//							$GLOBALS['pdo']->rollback();
						
							//the app had a database error, return false to indicate the function call failed
							return false;
						}
					}
					
					//check if there were any processing errors for the current repo:
					if ($return_value)
					{
						//there were no processing errors, commit the transaction
//						$GLOBALS['pdo']->commit();
					}
				}
				else
				{
					//The repo already exists, do nothing
					echo "The repo already exists, do nothing\n";
				}
			}

			echo "The repo_request_loop has finished the current iteration, check if the next_url_link is defined\n";

			if (!empty($next_link_url))
			{
				//the next link is defined, recursively call repo_request_loop with the $next_link_url

				//request the next page of the list so it can be processed:
				if (!repo_request_loop ($next_link_url, $owner_id, $parent_repo_id))
				{
					//the repo_request_loop for the next page was not successful
					echo "the repo_request_loop for the next page was not successful\n";

					return false;
				}
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


//function that inserts an owner record based on the values defined in the $owner_info array
function insert_owner ($owner_info, &$owner_id)
{
	echo "running insert_owner(".var_export($owner_info['id'], true).", \$owner_id)\n";

	$query = "insert into github_network.ghnd_owners (source_owner_id, login, html_url, owner_type) VALUES (:source_owner_id, :login, :html_url, :owner_type)";

	echo "the value of \$query is: $query\n";

	$stmt = $GLOBALS['pdo']->prepare($query);

	$stmt->bindValue(":source_owner_id", $owner_info['id']);
	$stmt->bindValue(":login", $owner_info['login']);
	$stmt->bindValue(":html_url", $owner_info['html_url']);
	$stmt->bindValue(":owner_type", $owner_info['type']);

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
function owner_exists($owner_info, &$owner_id)
{
	//initialize the return value:
	$return_value = true;
	
	//initialize the value of $owner_id
	$owner_id = null;

	echo "running owner_exists(".var_export($owner_info['id'], true).", \$owner_id)\n";
	
	$query = "select owner_id from github_network.ghnd_owners where source_owner_id = :source_owner_id and owner_type = :owner_type";
	
	echo "the value of \$query is: $query\n";
	
	

	// prepare the statement. the placeholders allow PDO to handle substituting
	// the values, which also prevents SQL injection
	$stmt = $GLOBALS['pdo']->prepare($query);

	// bind the parameters
	$stmt->bindValue(":source_owner_id", $owner_info['id']);
	$stmt->bindValue(":owner_type", $owner_info['type']);

	if ($stmt->execute())
	{
		//the query was successful
		
		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			//the query returned a row:
			
			//store the matching owner record's owner_id in the $owner_id variable
			$owner_id = $row['owner_id'];
		}	
		else
		{
			//there were no rows returned by the query
			echo "there were no rows returned by the query\n";

			return false;
		}
	}
	else
	{
		echo "the owner query was unsuccessful\n";
		
		return false;
	}
	
	return $return_value;
}





function insert_repo ($repo_info, $owner_id, &$repo_id)
{
	echo "running insert_repo(".var_export($repo_info['id'], true).", \$repo_id)\n";

	$query = "insert into github_network.ghnd_repos (source_repo_id, repo_name, full_name, repo_url, topics, created_at, updated_at, owner_id, parent_repo_id) VALUES (:source_repo_id, :repo_name, :full_name, :repo_url, :topics, STR_TO_DATE(:created_at,'%Y-%m-%dT%H:%i:%sZ'), STR_TO_DATE(:updated_at,'%Y-%m-%dT%H:%i:%sZ'), :owner_id, :parent_repo_id)";

	echo "the value of \$query is: $query\n";

	$stmt = $GLOBALS['pdo']->prepare($query);




	//fill this in once the topics parsing is implemented
	$stmt->bindValue(":topics", null);

	//bind the insert query variables:
	$stmt->bindValue(":source_repo_id", $repo_info['id']);
	$stmt->bindValue(":repo_name", $repo_info['name']);
	$stmt->bindValue(":full_name", $repo_info['full_name']);
	$stmt->bindValue(":repo_url", $repo_info['html_url']);
	$stmt->bindValue(":created_at", $repo_info['created_at']);
	$stmt->bindValue(":updated_at", $repo_info['updated_at']);
	$stmt->bindValue(":parent_repo_id", $repo_info['parent_repo_id']);
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
function repo_exists($repo_info, &$repo_id)
{
	//initialize the value of $return_value
	$return_value = true;

	//initialize the value of $repo_id
	$repo_id = null;
	
	echo "running repo_exists(".var_export($repo_info['id'], true).", \$repo_id)\n";
	
	$query = "select repo_id from github_network.ghnd_repos where source_repo_id = :source_repo_id";
	
	echo "the value of \$query is: $query\n";

	// prepare the statement. the placeholders allow PDO to handle substituting
	// the values, which also prevents SQL injection
	$stmt = $GLOBALS['pdo']->prepare($query);

	// bind the parameters
	$stmt->bindValue(":source_repo_id", $repo_info['id']);

	//execute the query
	if ($stmt->execute())
	{
		//the query was successfully executed
		
		//retrieve the first result set row:
		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			//the result row was successfully retrieved
			$repo_id = $row['repo_id'];
		}
		else
		{
			//the result row was not successfully retrieved
			return false;
		}	
	}
	else
	{
		//the query was not successfully executed
		return false;
	}

	return $return_value;
}

//this function will determine if a repo record exists for the $repo_info array that contains the repo information. If the repo record exists then the database repo_id will be returned.  If the repo record does not exist then it will be inserted into the DB and the repo_id will be returned:
//if $owner_id is null then the function will attempt to check if the owner exists from the "owner" property of the $repo_info array

function process_repo ($repo_info, &$owner_id, &$repo_id)
{

	echo "runnning process_repo (".var_export($repo_info['id'], true).", $owner_id, $repo_id)\n";

	$return_value = true;
	$repo_id = null;
	
	//check if the repository exists in the database, if not insert it
	if (repo_exists($repo_info, $repo_id))
	{
		//the repository exists, use the $repo_id for the new repository

		echo "the repository exists, \$repo_id = $repo_id\n";
		
	}
	else
	{
		//the repository does not exist,
		
		echo "the repository does not exist, insert it\n";
		

		//check if the owner_id is defined, if not then attempt to process the owner
		if ((!is_null($owner_id)) || (process_owner ($repo_info['owner'], $owner_id)))
		{
			//the owner_id is defined or the owner record was processed successfully:
			echo "the owner_id is defined or the owner record was processed successfully\n";
			
			//insert the repo
			if (insert_repo ($repo_info, $owner_id, $repo_id))
			{
				//the repo record was inserted successfully
				echo "the repo record was inserted successfully\n";
				
				
			}
			else
			{
				//the repo record could not be inserted

				echo "the repo record was NOT inserted successfully\n";
		
				//return false to indicate an error in the function call
				return false;
		
			}
			
		}
		else
		{
			echo "the owner record was not processed successfully\n";
	
			//return false to indicate an error in the function call
			return false;
			
			
		}
	}

	return $return_value;	
	
}


function connect_mysql (&$pdo)
{
	// connect to PDO
	$pdo = new PDO("mysql:host=localhost;dbname=github_network", "github_dev", "myadm1n");

	// the following tells PDO we want it to throw Exceptions for every error.
	// this is far more useful than the default mode of throwing php errors
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	
}

//this function will determine if an owner record exists for the $owner_info array that contains the owner information. If the owner record exists then the database owner_id will be returned.  If the owner record does not exist then it will be inserted intothe DB and the owner_id will be returned:
function process_owner ($owner_info, &$owner_id)
{
	
	echo "running process_owner (".$owner_info['id'].", $owner_id)\n";
	$return_value = true;
	$owner_id = null;
	
	//check if the owner exists in the database, if not insert it
	if (owner_exists($owner_info, $owner_id))
	{
		//the owner exists, use the $owner_id for the new repository

		echo "the owner exists\n";
		
	}
	else
	{
		//the owner does not exist,
		
		echo "the owner does not exist, insert it\n";
		
		if (insert_owner ($owner_info, $owner_id))
		{
			//the owner record was inserted successfully


			echo "the owner record was inserted successfully\n";
			
			
		}
		else
		{
			//the owner record could not be inserted
			$return_value = false;
			
			echo "the owner record was NOT inserted successfully\n";
	
			//return false to indicate a DB error in the function call
			return false;
	
		}
	}

	return $return_value;
}
?>