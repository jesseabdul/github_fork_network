<?php



//function to query the database to see if the owner record exists:
function owner_exists($owner_info, &$owner_db_info)
{
	//initialize return value
	$owner_db_info = null;
	
	//initialize the return value:
	$return_value = true;
	
	//initialize the value of $owner_id
	$owner_id = null;

	echo "\nrunning owner_exists(".var_export($owner_info['login'], true).", \$owner_id)\n";
	
	$query = "select * from ghnd_owners where source_owner_id = :source_owner_id";
	
//	echo "the value of \$query is: $query\n";
	
	

	// prepare the statement. the placeholders allow PDO to handle substituting
	// the values, which also prevents SQL injection
	$stmt = $GLOBALS['pdo']->prepare($query);

	// bind the parameters
	$stmt->bindValue(":source_owner_id", $owner_info['id']);
//	$stmt->bindValue(":owner_type", $owner_info['type']);

	if ($stmt->execute())
	{
		//the query was successful
		
		if ($owner_db_info = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			//the query returned a row:
			
			//store the matching owner record's owner_id in the $owner_id variable
//			$owner_id = $owner_db_info['owner_id'];
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



//function that inserts an owner record based on the values defined in the $owner_info array
function insert_owner ($owner_info, &$owner_id)
{
	echo "running insert_owner(".var_export($owner_info['login'], true).", \$owner_id)\n";

	$query = "insert into ghnd_owners (source_owner_id, login, owner_html_url, owner_type) VALUES (:source_owner_id, :login, :html_url, :owner_type)";

//	echo "the value of \$query is: $query\n";

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

		$owner_id = $GLOBALS['pdo']->lastInsertId();
		
//		echo "the auto insert value is: ".$owner_id."\n";
		return true;
	}
	else
		return false;

}



//this function will determine if an owner record exists for the $owner_info array that contains the owner information. If the owner record exists then the database owner_id will be returned.  If the owner record does not exist then it will be inserted intothe DB and the owner_id will be returned:
function process_owner_record ($owner_info, &$owner_id)
{
	
	echo "\nrunning process_owner_record (".var_export($owner_info, true).", $owner_id)\n";
	$return_value = true;
	$owner_id = null;
	
	//check if the owner exists in the database, if not insert it
	if (owner_exists($owner_info, $owner_db_info))
	{
		//the owner exists, use the $owner_id for the new repository

		echo "the owner exists\n";

		//set the value of $owner_id
		$owner_id = $owner_db_info['owner_id'];
		
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



//function that returns true if the owner has been processed in this script execution or if the repo is marked as being successfully processed and false if not:
function owner_processed ($owner_info)
{
	echo "running owner_processed (".$owner_info['login'].")\n";
	
	
	$return_value = true;
	
	//check if the owner has been processed yet (in the current processing attempt)
	echo "check if the owner has been processed yet (in the current processing attempt)\n";
	
	if ($return_value = in_array($owner_info['id'], $GLOBALS['processed_owner_ids']))
	{
		//the owner record has been processed in the current processing attempt
		echo "the owner record has been processed in the current processing attempt\n";
	}
	else
	{
		//check if the owner has been processed yet (indicated in the database)
		echo "check if the owner has been processed yet (indicated in the database)\n";
		//query the database
		if ($return_value = owner_exists(array("id"=>$owner_info['id'], "login"=>$owner_info['login']), $owner_db_info))
		{
			//the owner has already been processed:
			echo "the owner record exists in the DB, \$owner_db_info['owner_processed_yn'] is: ".$owner_db_info['owner_processed_yn']."\n";
		
			//set the return value based on if the owner record has been marked as processed (1) or not (0)
			$return_value = ($owner_db_info['owner_processed_yn'] == 1 ? true : false);
		}
		else
		{
			//the owner has not been processed yet, add it to the $GLOBALS['processed_owner_ids'] to indicate it has been initiated for processing
			
			echo "the owner has not been processed yet, add it to the global variable \$processed_owner_ids to indicate it has been initiated for processing";
			
			$GLOBALS['processed_owner_ids'][] = $owner_info['id'];
		}
	}
	return $return_value;
}



//this function processes a given owner record
function process_owner($owner_info)
{
	echo "running process_owner (".$owner_info['login'].")\n";
	
	
	//initialize the $return_value variable value:
	$return_value = true;
	
	
	//check if the current owner id has been processed in this current or previous execution yet:
	if (!owner_processed($owner_info))
	{
		//the current owner id has not been processed in this current execution or previous execution yet, process it now:

		echo "the current owner id has not been processed in this current execution or previous execution yet, process it now\n";
		
		//set the current owner json array for the owner_type array element based on the value of $owner_type 
	

		//attempt to process the owner
		if (process_owner_record($owner_info, $owner_id))
		{
			
			//initialize the owner repo 404 error variable that is passed by reference to the repo_request_loop() function
			$owner_repo_404_http_error = false;
			
			//request all the repos associated with the org, starting with the first page with a maximum of 100 repos per page:
			if (repo_request_loop($owner_info['repos_url']."?per_page=100", $owner_repo_404_http_error, $owner_id))
			{
				//the repo_request_loop for the current owner was processed successfully
				echo "the repo_request_loop for the current owner was processed successfully\n";
			
				//commit the transaction here and update the owner record to indicate it has been processed:
				if (update_owner_processed_yn ($owner_id))
				{
					//the owner record was updated successfully
					echo "owner record was updated to indicate it was successfully processed\n";
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
				//the repo_request_loop for the current owner was NOT processed successfully
				echo "the repo_request_loop for the current owner was NOT processed successfully\n";
				
				return false;
				
			}
		}
		else
		{
			//the current owner could not be processed successfully
			echo "the current owner could not be processed successfully\n";

			return false;
		}
	}
	else
	{
		//the current owner has already been processed, skip the current owner:
		
		echo "the current owner has already been processed, skip the current owner\n";
	}
	
	
	return $return_value;
}







function update_owner_processed_yn ($owner_id)
{
	$query = "update ghnd_owners set owner_processed_yn = 1 where owner_id = :owner_id";
	
//	echo "the value of \$query is: $query\n";

	$stmt = $GLOBALS['pdo']->prepare($query);

	$stmt->bindValue(":owner_id", $owner_id);

	if ($stmt->execute())
	{
		//the update query was successful
		echo "the update query was successful\n";

		return true;
	}
	else
		return false;
	
}


//this function requests the owners in the GitHub network
function owner_request_loop ($request_url, $owner_request_counter, $owner_type = "owner")
{
	echo "running owner_request_loop ($request_url)\n";

	$return_value = true;
	
	//increment the owner loop counter to stop the script before it loops too many times
	$owner_request_counter++;
	
	echo "The value of \$owner_request_counter is: ".$owner_request_counter."\n";
	
	if ($owner_request_counter > 2)
	{
		echo "The owner_request_counter is above 2, exit the program\n";
		exit;
	
	}
	
	//send the curl request for the owners:
	if (curl_request($request_url, $json_object, $next_link_url, $http_code))
	{
		//the curl request and response parsing was successful

//		echo $curl_response;
//		file_put_contents($GLOBALS['debug_path']."owner_".$owner_type."_".$owner_request_counter.".txt", $curl_response);

		//loop through the owners
		
		for ($i = 0; $i < count($json_object); $i ++)
//			for ($i = 0; $i < count($json_object); $i ++)
		{
			//since this is an owner loop it doesn't specify certain pieces of information like type and html_url
			$json_object[$i]['type'] = $owner_type;
			$json_object[$i]['html_url'] = "https://github.com/".$json_object[$i]['login'];

			echo "process the current owner is: ".$json_object[$i]['login']."\n";
			if (process_owner($json_object[$i]))
			{
				//the owner was processed successfully:
				echo "owner record was processed successfully and updated to indicate it was successfully processed\n";

				//**UPDATE: commit the transaction
				$GLOBALS['pdo']->commit();

				
				//begin the new transaction:
				$GLOBALS['pdo']->beginTransaction();

			}					
			else
			{
				//the owner was NOT processed successfully:
				echo "owner record was NOT processed successfully, rollback the transaction\n";
				
				//rollback the transaction:
				$GLOBALS['pdo']->rollback();
				
				//begin the new transaction:
				$GLOBALS['pdo']->beginTransaction();

			}
		}
		
		//check if the next_link_url is defined
		if (!is_null($next_link_url))
		{
			//the next link is defined, recursively call owner_request_loop with the $next_link_url

			echo "the next link is defined, recursively call owner_request_loop with the $next_link_url\n";


			if (owner_request_loop ($next_link_url, $owner_request_counter, $owner_type))
			{
				//the owner_request_loop was processed successfully:
				echo "the owner_request_loop (".$next_link_url.") was processed successfully\n";
			}
			else
			{
				//the owner_request_loop for the next link was NOT processed successfully:
				echo "the owner_request_loop for the next link (".$next_link_url.") was NOT processed successfully\n";

				//rollback the transaction:
				$GLOBALS['pdo']->rollback();

				//begin the new transaction:
				$GLOBALS['pdo']->beginTransaction();

				return false;					
			}
		}			
	}
	else
	{
		echo "The curl request (".$request_url.") or json response parsing was unsuccessful\n";

		$return_value = false;

		//rollback the transaction:
		$GLOBALS['pdo']->rollback();

		//begin the new transaction:
		$GLOBALS['pdo']->beginTransaction();
	}

	return $return_value;
	
}



//function that queries the DB for all owners that have not been marked as processed yet and process those recs in a loop using repo_request_loop()
function reprocess_owners ()
{
	echo "running reprocess_owners()\n";
	//initialize the return value
	$return_value = true;
	
	$query = "select source_owner_id id, login, owner_html_url html_url, owner_type type, (CASE WHEN owner_type = 'Organization' THEN CONCAT('https://api.github.com/orgs/', login , '/repos') ELSE CONCAT('https://api.github.com/users/', login , '/repos') END) repos_url from ghnd_owners where owner_processed_yn = 0";

	// prepare the statement. the placeholders allow PDO to handle substituting
	// the values, which also prevents SQL injection
	$stmt = $GLOBALS['pdo']->prepare($query);


	//execute the query
	if ($stmt->execute())
	{
		//the query was successfully executed
		
		//retrieve the owner records using repo_request_loop:
		while ($owner_info = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			echo "process the current owner is: ".$owner_info['login']."\n";
			if (process_owner($owner_info))
			{
				//the owner was processed successfully:
				echo "the owner record (".$owner_info['login'].") was processed successfully and updated to indicate it was successfully processed\n";

				//**UPDATE: commit the transaction
				$GLOBALS['pdo']->commit();

				
				//begin the new transaction:
				$GLOBALS['pdo']->beginTransaction();

			}					
			else
			{
				//the owner was NOT processed successfully:
				echo "owner record (".$owner_info['login'].") was NOT processed successfully, rollback the transaction\n";
				
				//rollback the transaction:
				$GLOBALS['pdo']->rollback();
				
				//begin the new transaction:
				$GLOBALS['pdo']->beginTransaction();

				$return_value = false;
			}
		}
	}
	else
	{
		//the query was not successfully executed
		return false;
	}
	

	echo "reprocess_owners() is finished executing\n\n";

	return $return_value;
}







?>