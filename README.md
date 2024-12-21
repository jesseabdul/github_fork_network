# GitHub Repositories Fork Network Data System

## Overview
This project was created to gather public repositories and owner information from GitHub using the REST API to construct a network of forked repositories.

## Technology Stack
-   PHP version 8.x (command line interface)
-   MySQL Community version 8.x

## PHP Scripts
-   The [get_repo_data.php](./app/get_repo_data.php) script utilizes the GitHub REST API to retrieve the repositories and owner information and insert the information into the MySQL database.  
    -   ### Algorithm overview
		-   Since the focus of this network science project is the relationships between forked repositories a depth-first search approach was implemented to follow forks in each direction anytime there were identified.  
		-   It was assumed that the owners that would have the most repositories would be organizations. 
			-   The script begins by querying all of the organizations and then querying for all of the repositories owned by the organization and processing the repositories in a loop.  If an owner record has been marked as successfully processsed it is ignored
				-   Each time a repository is processed it uses the API to check if there is a parent repository that the current repository references, if so it will attempt to process the parent repository by calling the repository processing function recursively.
				-   Then the current repository is inserted into the database
				-   Then the script uses the API to check if there are any child repositories for the current repository and attempts to process them
				-   If the parent repository and all child repositories for the current repository are successfully processed the current repository is updated in the database to mark it as successfully processed.
			-   If all of the repositories that belong to a given owner are successfully processed the owner is updated to mark it as successfully processed in the database
		-   Since this is a very large network the script was developed to allow resuming the querying process after a network/app/system failure or if the user stops the script from running.  SQL transactions were implemented to ensure that incomplete database actions were not saved.  
			-   When the script resumes it will attempt to process all repositories in the database that have not been marked as successfully processed.
			-   Next, the script will attempt to process all owners in the database that have not been marked as successfully processed.
			-   Next, the script will use the API to retrieve the list of organizations and attempt to process the repositories for each organization.  
-   The [generate_graphml.php](./app/generate_graphml.php) script queries the database and generates a network file that contains repositories and associated owner information in .graphml format.  

## MySQL Database Structure
-   Repositories 
	-   Table: ghnd_repos
	-   Fields:
		-   repo_id (unique numeric primary key defined in the database)
		-   source_repo_id (the id from the GitHub API)
		-   parent_repo_id (this foreign key points references the repo_id value of a repository that the given repository is a fork from.  This field is null when the given repository is not a fork from any repositories)
		-   name (the name from the GitHub API)
		-   full_name (the full name from the GitHub API that includes the owner login value)
		-   repo_html_url (the HTML URL for the given repository within GitHub)
		-   created_at (date/time the repository was created in GitHub)
		-   updated_at (date/time the repository was last updated in GitHub)
		-   owner_id (foreign key reference to the ghnd_owners table)
		-   repo_processed_yn (flag that indicates if the repository has been successfully processed)
-   Owners
	-   Table: ghnd_owners
	-   Fields:
		_   owner_id (unique numeric primary key defined in the database)
		-   source_owner_id (the id of the owner record from the GitHub API)
		-   login (the login from the GitHub API)
		-   owner_html_url (the HTML URL for the given owner within GitHub)
		-   owner_type (the type from the GitHub API)
		-   owner_processed_yn (flag that indicates if the owner has been successfully processed)
