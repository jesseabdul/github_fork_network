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


	