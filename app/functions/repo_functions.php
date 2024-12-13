<?php


//function to query the database to see if the repo record exists:
function repo_exists($repo_info, &$repo_db_info)
{
	//initialize the value of $return_value
	$return_value = true;

	//initialize the value of $repo_id
	$repo_id = null;
	
//	echo "\nrunning repo_exists(".var_export($repo_info, true).", \$repo_id)\n";
	
	$query = "select * from ghnd_repos where source_repo_id = :source_repo_id";
	
//	echo "the value of \$query is: $query\n";

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
		if ($repo_db_info = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			//the result row was successfully retrieved
//			$repo_id = $row['repo_id'];
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

function insert_repo ($repo_info, $owner_id, &$repo_id)
{
	echo "\nrunning insert_repo(".var_export($repo_info['full_name'], true).", \$repo_id)\n";

	$query = "insert into ghnd_repos (source_repo_id, name, full_name, repo_html_url, topics, created_at, updated_at, owner_id, parent_repo_id) VALUES (:source_repo_id, :name, :full_name, :html_url, :topics, STR_TO_DATE(:created_at,'%Y-%m-%dT%H:%i:%sZ'), STR_TO_DATE(:updated_at,'%Y-%m-%dT%H:%i:%sZ'), :owner_id, :parent_repo_id)";

//	echo "the value of \$query is: $query\n";

	$stmt = $GLOBALS['pdo']->prepare($query);




	//fill this in once the topics parsing is implemented
	$stmt->bindValue(":topics", null);

	//bind the insert query variables:
	$stmt->bindValue(":source_repo_id", $repo_info['id']);
	$stmt->bindValue(":name", $repo_info['name']);
	$stmt->bindValue(":full_name", $repo_info['full_name']);
	$stmt->bindValue(":html_url", $repo_info['html_url']);
	$stmt->bindValue(":created_at", $repo_info['created_at']);
	$stmt->bindValue(":updated_at", $repo_info['updated_at']);
	$stmt->bindValue(":parent_repo_id", $repo_info['parent_repo_id']);
	$stmt->bindValue(":owner_id", $owner_id);

	if ($stmt->execute())
	{
		//the insert query was successful
		echo "the insert query was successful\n";

		//return the repo_id value so it can be used for processing the data

		$repo_id = $GLOBALS['pdo']->lastInsertId();
		
		echo "the auto insert value is: ".$repo_id."\n";
		return true;
	}
	else
		return false;

}




//this function will determine if a repo record exists for the $repo_info array that contains the repo information. If the repo record exists then the database repo_id will be returned.  If the repo record does not exist then it will be inserted into the DB and the repo_id will be returned:
//if $owner_id is null then the function will attempt to check if the owner exists from the "owner" property of the $repo_info array

function process_repo_record ($repo_info, &$owner_id, &$repo_id)
{

	echo "\n\n runnning process_repo_record (".var_export($repo_info['full_name'], true).", $owner_id, $repo_id)\n";

	$return_value = true;
	$repo_id = null;
	
	//check if the repository exists in the database, if not insert it
	if (repo_exists($repo_info, $repo_db_info))
	{
		//the repository exists, use the $repo_id for the new repository

		$repo_id = $repo_db_info['repo_id'];

		echo "the repository exists, \$repo_id = $repo_id\n";
		
	}
	else
	{
		//the repository does not exist,
		
		echo "the repository does not exist, insert it\n";
		

		//check if the owner_id is defined, if not then attempt to process the owner
		if ((!is_null($owner_id)) || (process_owner_record ($repo_info['owner'], $owner_id)))
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



//function that returns true if the repo has been processed in this script execution or if the repo is marked as being successfully processed and false if not:
function repo_processed ($repo_info)
{
	echo "running repo_processed (".$repo_info['full_name'].")\n";
	
	$return_value = true;
	
	//check if the repo has been processed yet (in the current processing attempt)
	echo "check if the repo has been processed yet (in the current processing attempt)\n";
	
	if ($return_value = in_array($repo_info['id'], $GLOBALS['processed_repo_ids']))
	{
		//the repo record has been processed in the current processing attempt
		echo "the repo record has been processed in the current processing attempt\n";
	}
	else
	{
		//check if the repo has been processed yet (indicated in the database)
		echo "check if the repo has been processed yet (indicated in the database)\n";
		//query the database
		if ($return_value = repo_exists(array("id"=>$repo_info['id']), $repo_db_info))
		{
			echo "the repo record exists in the DB, \$repo_db_info['repo_processed_yn'] is: ".$repo_db_info['repo_processed_yn']."\n";
		
			//set the return value based on if the repo record has been marked as processed (1) or not (0)
			$return_value = ($repo_db_info['repo_processed_yn'] == 1 ? true : false);
			
		}
		else
		{
			//the repo has not been processed yet, add it to the $GLOBALS['processed_repo_ids'] to indicate it has been initiated for processing
			
			echo "the repo has not been processed yet, add it to the global variable \$processed_repo_ids to indicate it has been initiated for processing";
			
			$GLOBALS['processed_repo_ids'][] = $repo_info['id'];
		}
	}
	return $return_value;
}



//process the current repository 
//$repo_info is an array from a json response to a repository query
//$repo_id contains the repo_id value from the DB for the $repo_info (either an existing record or a newly created record)
//$owner_id contains the owner_id value from the DB for the repository owner (this will be defined for owner processing loops and null for all others)
//$parent_repo_id contains the parent_repo_id for the 
function process_repo (&$repo_info, &$repo_id, $owner_id = null, $parent_repo_id = null, $bypass_repo_exists = false)
{
	echo "\n\n running process_repo (".$repo_info['full_name'].", $owner_id, $parent_repo_id)\n";
	
	//initialize the $return_value variable
	$return_value = true;

	//check if the repo_exists check should be bypassed
	if (!$bypass_repo_exists)
	{
		//do not bypass the repo_exists check, initialize the $repo_id to be null:
		
		//initialize the $repo_id variable
		$repo_id = null;
	}
	
//	echo "The value of the current repo is: ".$repo_info['full_name']."\n";


	//check if the current repo id has been processed in this current execution yet:
	if (!repo_processed($repo_info))
	{
		//the current repo id has not been processed in this current execution or in a previous execution yet, process it now:
		echo "the current repo id has not been processed in this current execution or previous execution yet, process it now\n";



		//check the $bypass_repo_exists flag or if the repo exists in the DB based on the parsed id value
		if (($bypass_repo_exists) || (!repo_exists($repo_info, $repo_db_info)))
		{ 
			//the $bypass_repo_exists flag is true or the current repo does not already exist, process it
			echo "the current repo does not already exist, process it\n";
			

			//check if the current repo is a fork, if so query for the repo it was forked from and insert the current repo with fork_repo_id:
			echo "check if the current repo is a fork\n";

			//check if the parent_repo_id is not defined and the current repository is a fork 
			if ((is_null($parent_repo_id)) && ($repo_info['fork']))
			{
				//the current repository is a forked repository, get the information from the "parent" property
				echo "the current repository is a forked repository, get the information from the fork url\n";


				//check if the repo_exists should be bypassed and if not request the current repository's detailed information:
				if (($bypass_repo_exists) || (curl_request($repo_info['url'], $single_repo_json_object, $next_link_url, $http_code)))
				{
					//the single repo curl request and response parsing was successful


					if ($bypass_repo_exists)
					{
						//reused the $repo_info object which is the results of the detailed repo API request
						$single_repo_json_object = $repo_info;
						
					}
					else
					{

						echo "the single repo curl request and response parsing was successful\n";
					}
					
					//parse the parent object to get the owner and the repo

			//		echo $single_repo_curl_response;
//					file_put_contents($GLOBALS['debug_path']."repo_#".$repo_info['id'].".txt", $single_repo_curl_response);


//					echo "saved the contents in a text file: ".$GLOBALS['debug_path']."repo_#".$repo_info['id'].".txt\n";

					//the json response was parsed successfully

//					echo "The value of \$single_repo_json_object is: " . var_export($single_repo_json_object, true)."\n";

					//release the long json string variable from memory
					unset($single_repo_curl_response);
					
					echo "The parent repository name is: ".$single_repo_json_object['parent']['name']."\n";

					echo "The parent name is: ".$single_repo_json_object['parent']['owner']['login']."\n";
					
					
					
					echo "The parent fork value is: ".$single_repo_json_object['parent']['fork']."\n";
					
					//initialize the value of the $parent_parent_repo_id to null;
					$parent_parent_repo_id = null;
					
					//check if the current parent repo has a parent:
					if ($single_repo_json_object['parent']['fork'])
					{
						echo "The current parent repo has a parent repo, recursively process this parent repo\n";
						
						//process the parent repo and return the $repo_id so it can be used for the current parent repo, owner_id and parent_repo_id are both null because we don't know anything about the parent repo's parent repo yet:
						if (process_repo($single_repo_json_object['parent'], $parent_parent_repo_id, null, null))
						{
							echo "the parent repo's parent repo was processed successfully\n";
							
						}
						else
						{
							//the parent repo's (".$single_repo_json_object['parent']['full_name'].") parent repo could not be processed
							
							echo "the parent repo's parent repo could not be processed\n";
							
						
							//the app had a database error, return false to indicate the function call failed
							return false;
							
						}
					}
					else
					{
						//The parent is not a forked repo
						echo "The parent is not a forked repo\n";
					}
					
					
					//initialize the parent_owner_id to be null since this is not known about the parent repo's owner
					$parent_owner_id = null;
					
					//set the parent_repo_id for the parent repo to the 
					$single_repo_json_object['parent']['parent_repo_id'] = $parent_parent_repo_id;
					
					//process the current parent repo:
					if (process_repo_record($single_repo_json_object['parent'], $parent_owner_id, $parent_repo_id))
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
					


					//initialize the parent owner repo 404 error variable that is passed by reference to the repo_request_loop() function
					$parent_owner_repo_404_http_error = false;
					
					echo "recursively process all of the parent repo's owner's (".$single_repo_json_object['parent']['owner']['login'].") repos\n";
					
					//use the repos_url property of the parent repo's owner to construct a request for the repos for the parent repo's owner
					if (repo_request_loop ($single_repo_json_object['parent']['owner']['repos_url']."?per_page=100", $parent_owner_repo_404_http_error, $parent_owner_id))
					{
						//the parent repo's owner repo request loop was successful
						echo "the parent repo's owner repo request loop was successful\n";
					}
					else
					{
						//the parent owner repo_request_loop failed
						
						echo "the parent repo's owner (".$single_repo_json_object['parent']['owner']['login'].") repo request loop was NOT successful\n";
						
					
						//the app had a database error, return false to indicate the function call failed
						return false;
					}
							
					
				}
				else
				{
					
					//the detailed repo curl request was not successful
					echo "the detailed repo curl request and response parsing was not successful\n";
				}
			}
			else
			{
				
				echo "The parent_repo_id is already defined (".$parent_repo_id.") or this is not a forked repository (".$repo_info['fork'].")\n";
			}

			echo "The repo does not already exist, insert it\n";

			//the repo does not exist, insert it now:
			
			if ((!is_null($owner_id)) || (process_owner_record ($repo_info['owner'], $owner_id)))
			{
				echo "the owner_id is specified or the process_owner_record() function was successfully executed\n";

				//we need to include the $parent_repo_id if there is one when we insert this record
				$repo_info['parent_repo_id'] = $parent_repo_id;
			

				//insert the repo or bypass the repo insertion if $bypass_repo_exists is true:
				if (($bypass_repo_exists) || (insert_repo($repo_info, $owner_id, $repo_id)))
				{
					echo "The repo (".$repo_info['full_name'].") was inserted successfully\n";
				}
				else
				{
					
					echo "Error - The repo was NOT inserted successfully\n";
				
			
					//the app had a database error, return false to indicate the function call failed
					return false;
				}
			}
			else
			{
				//the owner was not processed successfully:

				echo "the owner for the current repo was not processed successfully\n";


				return false;
			
			}
			
			//use the repo loop query except call it with owner_id = NULL so the owner will be determined by parsing the repo data:





			//check if there are any forks for the current repo:
			if ($repo_info['forks_count'] > 0)
			{
				//request the forks in a recursive function, this version must parse the owner from the response instead of the $owner_id since it is not based on a query for the owner:
				echo "\nThis repo has at least one fork: ".$repo_info['forks_count'].", recursively request all of the forked repos\n";
				
				
				//query for the repos that were forked from the current repo and insert them using the repo loop query except call it with owner_id = NULL so the owner will be determined by parsing the repo's fork data:
				
				//initialize the fork url 404 error variable that is passed by reference to the repo_request_loop() function
				$fork_404_http_error = false;
				
				
				//process the current repo's forks and provide the $repo_id as the parent_repo_id of all the associated forked repos
				if (repo_request_loop($repo_info['forks_url']."?per_page=100", $fork_404_http_error, null, $repo_id))
				{
					//the repo's fork repo request loop was successful
					echo "the repo's fork repo request loop was successful\n";
					
				}
				else
				{
					//the repo's fork repo request loop was NOT successful
					echo "the repo's fork repo request loop was NOT successful, the value of \$fork_404_http_error is: ".var_export($fork_404_http_error, true)."\n";
					
					
					
					//check if this is due to a 404 HTTP return code and if the $parent_repo_id is set
					if ($fork_404_http_error)
					{
						//the repo forks were not processed due to a 404 error where the parent_repo_id is set, allow the transaction for the repo to be committed and set repo_processed_yn = 1 since this 404 error will keep occurring and the rest of the processing was successful
						
				
						echo "due to a 404 error the repo's (".$repo_info['full_name'].") fork repo request loop was NOT successful, ignore the error since it was just the fork url loop that failed\n";
					}
					else
					{
						//the repo forks were not processed due to a non-404 error
						
					
						echo "the repo's (".$repo_info['full_name'].") fork repo request loop was NOT successful\n";
					
					
						//the app had a database error, return false to indicate the function call failed
						return false;
					}
				}
			}
			
			//check if there were any processing errors for the current repo:
			if ($return_value)
			{
				//there were no processing errors, commit the transaction

				//commit the transaction here and update the repo record to indicate it has been processed:
				if (update_repo_processed_yn ($repo_id))
				{
					//the repo record was updated successfully
					echo "repo record was updated to indicate it was successfully processed\n";
					
					
					//**UPDATE: commit the transaction
					$GLOBALS['pdo']->commit();

					
					//begin the new transaction:
					$GLOBALS['pdo']->beginTransaction();
					
				}
				else
				{
					//the repo record could not be updated successfully
					echo "the repo record could not be updated successfully\n";
					
					//there is no need to rollback the transaction, the error will bubble up to owner_request_loop()
					return false;
					
				}
			}
		}
		else
		{
			//The repo already exists, do nothing
			echo "The repo already exists, do nothing\n";
		}

		echo "At the end of the process_repo (".$repo_info['full_name'].") function the value of \$return_value is: ".$return_value."\n";

		//release the json array from memory
	//	$repo_info = null;

	}
	else
	{
		//the current repo has already been processed, skip the current repo:
		
		echo "the current repo has already been processed, skip the current repo (".$repo_info['full_name'].") \n";					
	}
		
	return $return_value;
	
}



function update_repo_processed_yn ($repo_id)
{
	$query = "update ghnd_repos set repo_processed_yn = 1 where repo_id = :repo_id";
	
//	echo "the value of \$query is: $query\n";

	$stmt = $GLOBALS['pdo']->prepare($query);

	$stmt->bindValue(":repo_id", $repo_id);

	if ($stmt->execute())
	{
		//the update query was successful
		echo "the update query was successful\n";

		return true;
	}
	else
		return false;
	
}




//recursive function that loops through the repos for a given org or user identified by id 
function repo_request_loop($request_url, &$http_404_error, $owner_id = null, $parent_repo_id = null)
{
	echo "\n\n running repo_request_loop ($request_url, $owner_id, $parent_repo_id)\n";

	echo "the repo loop counter is ".(++$GLOBALS['repo_loop_counter'])."\n";

	//debugging statement
//	return true;

	$return_value = true;
	
	//send the curl request for the repos:
	if (curl_request($request_url, $json_object, $next_link_url, $http_code))
	{
		//the repos request and response parsing was successful:

		//save the contents in a temporary file for debugging purposes
//		file_put_contents($GLOBALS['debug_path']."repo_".($GLOBALS['repo_loop_counter']).".txt", $curl_response);

		echo "the repo json data was parsed successfully, there are ".count($json_object)." repos returned by the json and \$next_link_url is: " .$next_link_url ."\n";
		
		//loop through the repos
		for ($i = 0; $i < count($json_object); $i ++)
		{
//				echo "\n\nFor the repo_request_loop function in the json processing loop, the value of the current repo is: ".var_export($json_object[$i], true)."\n\n";



			//process the current repository
			if (process_repo ($json_object[$i], $repo_id, $owner_id, $parent_repo_id))
			{
				//the current repo was processed successfully
				
				echo "the current repo was processed successfully\n";
				
				//commit the transaction here and update the repo record to indicate it has been processed:
				if (update_repo_processed_yn ($repo_id))
				{
					//the repo record was updated successfully
					echo "repo record was updated to indicate it was successfully processed\n";
					
					
					//**UPDATE: commit the transaction
					$GLOBALS['pdo']->commit();

					
					//begin the new transaction:
					$GLOBALS['pdo']->beginTransaction();

					echo "the transaction has been committed for the repo\n";
				}
				else
				{
					//the owner record could not be updated successfully
					echo "the owner record could not be updated successfully\n";
					
					return false;
					
				}
			}
			else
			{
				//the current repo was NOT processed successfully
				
				echo "the current repo (".$json_object[$i]['full_name'].") was NOT processed successfully\n";

				return false;
			}

			
		}

		echo "The repo_request_loop has finished the current iteration, check if the next_link_url is defined\n";

		if (!empty($next_link_url))
		{
			//the next link is defined, recursively call repo_request_loop with the $next_link_url

			echo "the next link is defined, recursively call repo_request_loop with the $next_link_url\n";

			//request the next page of the list so it can be processed:
			if (!repo_request_loop ($next_link_url, $owner_id, $parent_repo_id))
			{
				//the repo_request_loop for the next page was not successful
				echo "the repo_request_loop for the next page (".$next_link_url.") was not successful\n";

				return false;
			}
			else
			{
				//the repo_request_loop for the next page was successful
				
				echo "the repo_request_loop for the next page (".$next_link_url.") was successful\n";
				
				
			}
		}			
	}
	else
	{
		echo "Error - The curl request or response parsing was unsuccessful\n";

		//the app had a curl request error, return false

		//check if the http code is = 404:
		if ($http_code == 404)
		{
			
			//this is an http 404 error, set the variable value
			$http_404_error = true;

			echo "this is an http 404 error, set the variable value to true (".var_export($http_404_error, true)."\n";

		}
		return false;
	}

	echo "finished executing repo_request_loop(".$request_url.")\n";

	return $return_value;
}




//function that queries the DB for all repos that have not been marked as processed yet and process those recs in a loop using process_repo()
function reprocess_repos ()
{
	echo "running reprocess_repos()\n";
	//initialize the return value
	$return_value = true;
	
	$query = "select repo_id, source_repo_id id, name, full_name, repo_html_url, source_owner_id, login, owner_html_url, owner_type type, owner_id, CONCAT('https://api.github.com/repos/', login, '/', name) url from ghnd_owner_repos_v where repo_processed_yn = 0";

	// prepare the statement. the placeholders allow PDO to handle substituting
	// the values, which also prevents SQL injection
	$stmt = $GLOBALS['pdo']->prepare($query);


	//execute the query
	if ($stmt->execute())
	{
		//the query was successfully executed
		
		//retrieve the repo records using repo_request_loop:
		while ($db_repo_info = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			
			//transform the repo/owner results into a nested array like the json for a repo:
//			transform_repo_results ($db_repo_info);
			
			echo "process the current repo: ".$db_repo_info['full_name']."\n";
			
			
			//query for the repo info via the API:
			
			if (curl_request($db_repo_info['url'], $repo_info, $next_link_url, $http_code))
			{
				//the curl request/response processing was successful
			
			
				if (process_repo($repo_info, $db_repo_info['repo_id'], $db_repo_info['owner_id'],null, true))
				{
					//the repo was processed successfully:
					echo "the repo record (".$db_repo_info['full_name'].") was processed successfully and updated to indicate it was successfully processed\n";

					//**UPDATE: commit the transaction
					$GLOBALS['pdo']->commit();

					
					//begin the new transaction:
					$GLOBALS['pdo']->beginTransaction();

				}					
				else
				{
					//the repo was NOT processed successfully:
					echo "repo record (".$db_repo_info['full_name'].") was NOT processed successfully, rollback the transaction\n";
					
					//rollback the transaction:
					$GLOBALS['pdo']->rollback();
					

					//begin the new transaction:
					$GLOBALS['pdo']->beginTransaction();

					$return_value = false;
				}
			}
			else
			{
				//the curl request/response processing was not successful
				$return_value = false;
				
			}
		}
	}
	else
	{
		//the query was not successfully executed
		return false;
	}
	

	echo "reprocess_repos() is finished executing\n\n";

	return $return_value;
}









?>