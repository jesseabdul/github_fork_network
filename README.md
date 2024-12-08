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
		
		
		
		
		
	have a value in the DB with the last url used for users/orgs?
		so it can pick up where it left off?
		
	_ change the timeout setting to make it long or remove it
	
	_ rename all of the json files that are saved on the server (for debugging).  Have them all use the ID so we don't use the global variable and we can know what GH recs each one corresponds to.
	
	
	
	_ ** implement the checks in repo_request_loop() for when the owner type and id are null
	
	in the algorithm check if the repo exists before doing anything with the owner ->
		_ ** need to ensure that we use SQL transactions to make sure that when a repo is created and saved that all of its associated records also exist (forks, parents) so that we know we can ignore them
		if (repo_exists())
		{
			//the repo already exists, do nothing
		
		
		}
		else
		{
			//check if owner exists:
			
			if (owner_exists())
			{
				//use the owner_id
			
			}
			else
			{
				//create the owner 
			
			}
		
			create the repository
		
		}