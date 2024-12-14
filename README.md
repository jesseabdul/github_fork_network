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

	
	
	
	
business rules:
	_ graphml file is saved in the /graphml_output folder (with date from runtime)
	_ each API request is saved to the app/debug folder as a .txt file to help with troubleshooting
	
	
	

## analysis ideas (look at 12/12/24 class)
qualifying nodes for communities

node merging up to owner level?

R: calculate all metrics for the network and compare to random networks to determine how it's similar or different from random or degree distribution
	assortativity
	betweenness, etc.
	transitivity
	components
	
	
make a note of how many isolates were filtered out but don't include them in the network


	
compare to random models 
	Gnm
	rewired
	configuration model
	power law?
	
look at domain explanations for why it differs from degree distribution random models



Use RMarkdown, Gephi for the paper
	Do we need to use Word/Google Docs?
	

Gephi: visualize with standard method



calculate communities using different methods
	louvain, infomap, link communities

look at high degree nodes, look for hubs
	roll up the 


bipartite graph?
temporal -> no time to do it myself, but there is time to requery the graph and compare the two sets of nodes/links


Primary School Contacts.Rmd: (43:00) 12/12/24 video
	Assortativity Computations






_ do we want to commit owners/repos even if they weren't successfully processed?
	having a processed_yn = 0 will still have the object reprocessed




_ process_repo()
	run recursively from process_repo on the parent repo (when exists)
	run in repo_request_loop()
	
	
	//the difference between the reprocess and the repo_request_loop
	
	
	process_repo() checks if the repo has been processed yet, if not then process it
		check if repo record exists in the DB, if so then do not process it
		if the repo record does not exist then process it (parent, current repo, child repos)
		
	
_ reprocess_owners() - make sure the owner is marked off as processed
	_ make sure these are added to the processed_id() array

_ process_repo() -> need to address the existing repo record that still needs to be processed (parent, child, owner repos)
	


known issues:
	github returns a 404 response for certain users/repos when the user is queried directly 
		Example: 
			"url": "https://api.github.com/users/world-admin"
			"html_url": "https://github.com/world-admin" 
			"forks_url": "https://api.github.com/repos/world-admin/aiohttp-demos/forks"
			
		To handle this issue a repo will continue to be processed even if the forks url returns a 404 code but it will not be marked as processed_yn = 1 because the forks were not processed.  




_ code optimization:
	when I store the repo/owner source id also save the corresponding DB ID too? -> we may not have it yet if it hasn't been processed yet
		This would save a query when the DB ID has been processed
			can also add the ID to the global array even if it has been processed before so it won't keep checking




how to handle the fork url failures?
	We want to be able to skip the current repo and move on




to do:
	debugging:
		huggingface/accelerate-wip is causing an infinite loop somehow
			This is happening after huggingface/accelerate is processed and it checks for all its forked urls which huggingface/accelerate-wip is one of them:
				it never inserts huggingface/accelerate-wip as a repo
				//maybe need to check if the owner id is the same as the owner id of the previous function call?
				
				//no need to do the parent owner repos or owner repos if the owner has already been processed



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
	
	
	
	_ how do we prevent the same owner from being queried for each time
		Check if the owner of the parent repo is the same as the owner of the current repo, if so there is no need to process the parent, just use the same owner_id when the record is processed
		We should track each owner that has been processed successfully
		
		Transactions will not work since they span different owners
			can't really do repositories either since they can be executed on parents or children at any time
		
		if a repo is not marked as successfully processed then the next time the process runs it can reprocess the owner and its associated repos

		Is there a case where the owner will finish processing but the repo is not done processing and a commit would cause a repo that has not been completely processed to be committed
			Yes, but that would be ok as long as the repo_processed_yn has not been updated
			Same goes for the owners, if they are committed but haven't been marked as owner_processed_yn they should be reprocessed
			
	business rules:
		tracking each repo and owner, once they are completely processed the script commits the transaction
			If the record exists and the processed_yn flag is not set then it should be reprocessed
			
			Resume functionality:
				Query for all orgs that have not been processed 
				Then start in on the normal processing on orgs
			
			
	_ check if we can get rid of type for owners in the processing code (DB only)
		these are likely unique ids for both sets of objects combined



	_ ** how do we handle 404 responses?
		Just continue to process the other repos/owners but don't update the repo/owner with processed_yn = 1?
		
		repo_request_loop() is running with parent_repo_id = X then we can ignore the problem and just not update the current repo as processed_yn = 1
			//we know this is a fork url processing



			if repo_request_loop() is running with 

	
	
	
	
	Algorithm overview:
		request the owners and loop through each of them (org or user)
			request the list of repositories for the current owner
				process each of the repositories
					if the repo already exists in the DB, do nothing
					If the repo does not already exist:
						If the repo is a forked repo:
							
							
							request the detailed repo info and process the parent repository 
							if the parent repo exists then use the repo_id as the parent_repo_id for the current repo
							If the parent report does not exist in the DB then insert the parent repo and the parent repo owner (if they don't already exist)
								****Check if the parent repository has a parent (fork: true)****
									//do this recursively, keep following the parent repo until there is no parent
										each time a new parent is found do the same recursive loop for the parent's owner repositories
										There is no need to check the 
										
										//**check for all other forks from the parent repository
											//use the fork url to process this
										
										

								Use the new repo_id as the parent_repo_id of the current owner repo in the processing loop
							
							process all of the parent repo owner's repos using repo_request_loop()
						
						process the current repo
							The owner_id is specified so each repo will be associated with the owner
							If the owner_id is not specified it will attempt to determine if the owner record exists and retrieve the owner_id and if the record doesn't exist it will insert the owner rec
							
							Insert the repo record with the parent_repo_id (if any)
							
						Check if the current repo has any forks
							if so, loop through all the forks using the fork request URL by calling the repo_request_loop with the current repo_id as the parent_repo_id parameter value
							

				If there was a next page link recursively call repo_request_loop() to process the next page for the current organization/fork list





_ implement separate functions for each of the types of processing needed in each case
	recursive function for processing parent repo and parent owner's repos
		end condition will be fork = false
		
	

	
	
	main recursive function for repo processing:
		Use the different arguments to determine if certain checks should be run (e.g. owner_id, parent_repo_id)
		
	function - process the current repo
		
	fork processing:
		find the fork url and call the repo_request_loop() with parent_repo_id = repo_id
		
	
		
	
	
** think about how to ensure that transactions are implemented properly and that the process can be resumed where it left off and no partial transactions are committed due to a rate limit or processing error	
	
	
								
							
						
						
	
	
	
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
					
					
					//check if the parent repo is a fork (recursive function call?)
						//we want to check if the parent has a parent
							//if not, then finish processing the current parent repo normally
							//if so, then 
					
					
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
		
		