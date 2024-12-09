# Network Science Data Gathering

## Overview
This project was created to gather github public repo network 



- should we start with all companies/organizations and then follow the forks?
- Then start with 
- can we sort the public repo query? -> maybe oldest to youngest or most recent commits?
- How do we paginate the results?





## Data elements



-   Repos
	_ repo_id
	_ source_id
	_ fork_repo_id --this FK points to a repo_id of a repo that the given repo is a fork from.  this field is null when the given repo is not a fork from any repository
	_ name
	_ full_name
	_ html_url
	_ topics -- contains a comma-delimited list of topics defined for the repo
	_ created_at
	_ updated_at
	_ owner_id
	
	


-   Owners
	_ owner_id
	- type (org or user)
	- login (name of org/user)
	- id 
	- fork (true/false)
	- html_url



processing:
 	when processing the forks, owner_id will not be defined and it will need to be parsed from the json


forks_count in repo request can be used to find the forked repos

	

## analysis ideas
qualifying nodes for communities

node merging up to owner level?

R: calculate all metrics for the network and compare to random networks to determine how it's similar or different from random or degree distribution

Gephi: visualize with standard method




to do:
	_ implement for users


	_ implement for forks
		_ current repo is fork
		_ current repo has forks
		
	_ generate the network file (graphml, gml, or other with properties)

	_ implement transactions for the repo commits
		as long as the repo is successfully inserted then commit
		
		
	_ using https://github.com/php-curl-class/php-curl-class
		
		
	have a value in the DB with the last url used for users/orgs?
		so it can pick up where it left off?
		
	_ change the timeout setting to make it long or remove it
	
	_ rename all of the json files that are saved on the server (for debugging).  Have them all use the ID so we don't use the global variable and we can know what GH recs each one corresponds to.
	
	
	
	_ ** implement the checks in repo_request_loop() for when the owner type and id are null
	
	in the algorithm check if the repo exists before doing anything with the owner ->
		_ ** need to ensure that we use SQL transactions to make sure that when a repo is created and saved that all of its associated records also exist (forks, parents) so that we know we can ignore them
		
		
		// if parent_repo_id is defined we know this is a fork repo processing loop
		// if the owner_id is defined then we know this is an owner repo processing loop
		// if both are blank then this is a parent repo processing loop
		
		// both should not be defined since these are mutually exclusive types of executions
		
			



		repo_request_loop:
		
		
			
			
			if (repo_exists())
			{
				//the repo already exists, do nothing
			
			
			}
			else
			{

				//look at parent fork (if parent_repo_id is not defined)
					do not pass in the owner_id parameter for these requests so the function determines the owner id, return the parent_repo_id so it can be used when inserting the current repo that is being processed:
					
					//process the parent owner's repos (repo_request_loop)
					
					//pass in owner_id = null
					//pass in parent_repo_id = null
					

				//process the current repo:
					//check if the owner_id parameter is defined, if not use the owner object within the repo object to process the owner
					
					//use the parent_repo_id (if defined) when inserting the current repo
					
				//process the forks (if any)
					//use the repo_id as the parent_repo_id for all of the child repos
					
					//process the child repos (repo_request_loop)
					
					//pass in owner_id = null
					//pass in parent_repo_id = repo_id
				
				
				
				//if there are no errors then commit the transaction 


				

			
			}
			
		
		when we call repo_request_loop() on an owner with the owner's repos we know the owner_id so we can just pass it in to the process_repo function
			this also applies to each subsequent recursive call on the owner's repos

		when we call repo_request_loop on a fork's parent pass in owner_id = NULL because we don't know what the parent repo's owner is
		
		when we call repo_request_loop on a repo's forks we pass in owner_id = NULL because we don't know what the forked repos' owners are 
		
	
	//do we specify the type value in the $owner_info if it doesn't already exist?
		YES, instead of adding a new variable as an argument
		
	
	
	create a new function to handle repo records (supply the $repo_info and $owner_info)
		if the record exists then return the $repo_id
		if the record does not exist 
			_ create a new function to handle owner records (supply just the $owner_info and $owner_id reference variable
				if the record exists then return the $owner_id 
				if the record does not exist then insert it and return the owner_id
			then insert the repo record and return the $repo_id
			
			
	for each fork should we also loop through each repo from each child repo's owner?
		yes
		
	for each parent fork should we also loop through each repo from the parent repo's owner
		yes
		
	if each repo's owner's repos are all in the DB there is no reason to reprocess the 
		**_ need to make sure that the transactions are implemented this way
	
	
		
	owner_request_loop() will loop through each repo that belongs to the owner and process each with repo_request_loop()
		supply the owner_id to the repo_request_loop so it does not need to query for each 
	
	_ do we have a different function for the fork request loop?
		The only difference is that the owner_id is not known because it can vary (specify null for owner_id, and owner_type
		
	_ do we have a different function for the fork parent request?
		The only difference is that the owner_id is not known because it can vary
		
	
		
	
	//should the transactions be committed by the repo or by the owner?
		repo since there may be hundreds of repos for a single user
		
		