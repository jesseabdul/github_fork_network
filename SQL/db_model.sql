DROP TABLE ghnd_repos;
drop table ghnd_owners;
drop view ghnd_owner_repos_v;
drop view ghnd_parent_child_owner_repos_v;
drop view parent_repo_count_v;
drop view repo_summ_v;
drop view owner_summ_v;


CREATE TABLE `github_network`.`ghnd_owners` (
  `owner_id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique numeric primary key',
  `source_owner_id` INT NOT NULL COMMENT 'The id defined in the source system (e.g. GitHub) for the given owner',
  `login` VARCHAR(100) NOT NULL COMMENT 'The organization or user name in the source data system',
  `owner_html_url` VARCHAR(500) NOT NULL COMMENT 'The URL for the owner in the source system',
  `owner_type` ENUM('Organization', 'User') NOT NULL COMMENT 'The owner type defined in the source system, User or Organization',
  `owner_processed_yn` tinyint default 0 COMMENT 'flag to indicate if the current owner was successfully processed, 1 indicate the owner has been successfully processed and 0 indicates is has not',
  PRIMARY KEY (`owner_id`),
  UNIQUE INDEX `source_owner_id_UNIQUE` (`source_owner_id` ASC) VISIBLE,
  INDEX `owner_type_indx` (`owner_type` ASC) VISIBLE,
  INDEX `login_indx` (`login` ASC) VISIBLE)
COMMENT = 'GitHub Network Data - Repository Owners';



CREATE TABLE `github_network`.`ghnd_repos` (
  `repo_id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique numeric primary key',
  `source_repo_id` INT NOT NULL COMMENT 'The id defined in the source system (e.g. GitHub) for the given repo',
  `parent_repo_id` INT NULL COMMENT 'foreign key that references the ghnd_repo_owners table that defines the fork dependency for the given repo (if any).  This foreign key points to a repo_id of a ghnd_repos record that the given repo is a fork from.  This field is null when the given repo is not a fork from any repository',
  `name` VARCHAR(100) NOT NULL COMMENT 'The repository name',
  `full_name` VARCHAR(400) NOT NULL COMMENT 'The full name for the repository',
  `repo_html_url` VARCHAR(500) NOT NULL COMMENT 'The URL for the owner in the source system',
  `topics` VARCHAR(1000) NULL COMMENT 'The comma-delimited list of topics defined for the repo in the source system',
  `created_at` DATETIME NULL COMMENT 'The repository\'s created_at value',
  `updated_at` DATETIME NULL COMMENT 'The repository\'s updated_at value',
  `owner_id` INT NOT NULL COMMENT 'Foreign key references to the ghnd_owners record that owns the repository',
  `repo_processed_yn` tinyint default 0 COMMENT 'flag to indicate if the current repo was successfully processed, 1 indicate the repo has been successfully processed and 0 indicates is has not',
  PRIMARY KEY (`repo_id`),
  UNIQUE INDEX `source_repo_id_UNIQUE` (`source_repo_id` ASC) VISIBLE,
  INDEX `parent_repo_id` (`parent_repo_id` ASC) VISIBLE,
  INDEX `owner_id` (`owner_id` ASC) VISIBLE)
COMMENT = 'GitHub Network Data - Repositories';

create or replace view ghnd_owner_repos_v as 
select

ghnd_owners.owner_id,
ghnd_owners.source_owner_id,
ghnd_owners.login,
ghnd_owners.owner_html_url,
ghnd_owners.owner_type,
ghnd_owners.owner_processed_yn,
ghnd_repos.repo_id,
ghnd_repos.source_repo_id,
ghnd_repos.parent_repo_id,
ghnd_repos.name,
ghnd_repos.full_name,
ghnd_repos.repo_html_url,
ghnd_repos.topics,
ghnd_repos.created_at,
ghnd_repos.updated_at,
ghnd_repos.repo_processed_yn
from 
ghnd_owners inner join ghnd_repos on ghnd_owners.owner_id = ghnd_repos.owner_id 
order by ghnd_owners.owner_type, ghnd_owners.login, ghnd_repos.name; 

create or replace view ghnd_parent_child_owner_repos_v as 
select
parent_owner_repos.owner_id parent_owner_id,
parent_owner_repos.source_owner_id parent_source_owner_id,
parent_owner_repos.login parent_login,
parent_owner_repos.owner_html_url parent_owner_html_url,
parent_owner_repos.owner_type parent_owner_type,
parent_owner_repos.owner_processed_yn parent_owner_processed_yn,
parent_owner_repos.repo_id parent_repo_id,
parent_owner_repos.source_repo_id parent_source_repo_id,
parent_owner_repos.parent_repo_id parent_parent_repo_id,
parent_owner_repos.name parent_name,
parent_owner_repos.full_name parent_full_name,
parent_owner_repos.repo_html_url parent_repo_html_url,
parent_owner_repos.topics parent_topics,
parent_owner_repos.created_at parent_created_at,
parent_owner_repos.updated_at parent_updated_at,
parent_owner_repos.repo_processed_yn parent_repo_processed_yn,
child_owner_repos.owner_id child_owner_id,
child_owner_repos.source_owner_id child_source_owner_id,
child_owner_repos.login child_login,
child_owner_repos.owner_html_url child_owner_html_url,
child_owner_repos.owner_type child_owner_type,
child_owner_repos.owner_processed_yn child_owner_processed_yn,
child_owner_repos.repo_id child_repo_id,
child_owner_repos.source_repo_id child_source_repo_id,
child_owner_repos.parent_repo_id child_parent_repo_id,
child_owner_repos.name child_name,
child_owner_repos.full_name child_full_name,
child_owner_repos.repo_html_url child_repo_html_url,
child_owner_repos.topics child_topics,
child_owner_repos.created_at child_created_at,
child_owner_repos.updated_at child_updated_at,
child_owner_repos.repo_processed_yn child_repo_processed_yn
from ghnd_owner_repos_v parent_owner_repos inner join
ghnd_owner_repos_v child_owner_repos on 
parent_owner_repos.repo_id = child_owner_repos.parent_repo_id

order by parent_owner_repos.owner_type, parent_owner_repos.login, parent_owner_repos.name, child_owner_repos.owner_type, child_owner_repos.login, child_owner_repos.name; 


/*implement summary reports

	summary by parent repo
	
	do we want a hierarchical query that we can use to roll up all the parents?
	
	summarize by owner
		
	group by owner
	
	
	group by repo
	
	
	
	


*/



/*implement a network export view so we can construct the gml or graphml file*/


/*simple owner summary query that counts each repo and the number or forked repositories*/

/*
create or replace view owner_summary_v
as
	select 
	count(*) total_repos,
	SUM(case when ghnd_owners.parent_repo_id is null then 0 else 1 END) forked_child_repos,
	ghnd_owners.owner_id,
	ghnd_owners.source_owner_id,
	ghnd_owners.login,
	ghnd_owners.owner_html_url,
	ghnd_owners.owner_type,
	ghnd_owners.owner_processed_yn
	from
	ghnd_owner_repos_v
	group by
	ghnd_owners.owner_id,
	ghnd_owners.source_owner_id,
	ghnd_owners.login,
	ghnd_owners.owner_html_url,
	ghnd_owners.owner_type,
	ghnd_owners.owner_processed_yn
	order by ghnd_owners.owner_type, ghnd_owners.login;

*/
/*
how do we summarize the parent/child repos without using subqueries?


*/

/*

--get the summary information for the repos and then just join it to the owner data
--get a summary count of each 	
	--parent-child relationship


--if we get the count of the in-degree and the out-degree of each owner that is useful information
	--get this information at the repo-level and then summarize that 
	
	
	
*/
/*this view returns a summary of incoming and outgoing links for a given parent repo*/

create or replace view parent_repo_count_v
as 
select 
parent_owner_id,
parent_source_owner_id,
parent_login, 
parent_owner_html_url, 
parent_owner_type, 
parent_owner_processed_yn, 
parent_repo_id,
parent_source_repo_id,
parent_parent_repo_id,
parent_name, 
parent_full_name,
parent_repo_html_url, 
parent_topics, 
parent_created_at,
parent_updated_at, 
parent_repo_processed_yn,
count(*) child_repo_count,
SUM(CASE WHEN child_repo_processed_yn = 1 THEN 1 ELSE 0 END) child_repo_processed_count,
SUM(CASE WHEN child_repo_processed_yn = 0 THEN 1 ELSE 0 END) child_repo_unprocessed_count

from
ghnd_parent_child_owner_repos_v

group by 
parent_owner_id,
parent_source_owner_id,
parent_login, 
parent_owner_html_url, 
parent_owner_type, 
parent_owner_processed_yn, 
parent_repo_id,
parent_source_repo_id,
parent_parent_repo_id,
parent_name, 
parent_full_name,
parent_repo_html_url, 
parent_topics, 
parent_created_at,
parent_updated_at, 
parent_repo_processed_yn

order by parent_owner_type, parent_login
;	





create or replace view repo_summ_v

as


select
ghnd_owner_repos_v.owner_id,
ghnd_owner_repos_v.source_owner_id,
ghnd_owner_repos_v.login, 
ghnd_owner_repos_v.owner_html_url, 
ghnd_owner_repos_v.owner_type, 
ghnd_owner_repos_v.owner_processed_yn, 
ghnd_owner_repos_v.repo_id,
ghnd_owner_repos_v.source_repo_id,
ghnd_owner_repos_v.parent_repo_id,
ghnd_owner_repos_v.name, 
ghnd_owner_repos_v.full_name,
ghnd_owner_repos_v.repo_html_url, 
ghnd_owner_repos_v.topics, 
ghnd_owner_repos_v.created_at,
ghnd_owner_repos_v.updated_at, 
ghnd_owner_repos_v.repo_processed_yn,
parent_repo_count_v.child_repo_count in_degree,
parent_repo_count_v.child_repo_processed_count,
parent_repo_count_v.child_repo_unprocessed_count,
(CASE WHEN ghnd_owner_repos_v.parent_repo_id IS NOT NULL THEN 1 ELSE 0 END) out_degree

from
ghnd_owner_repos_v left join 
parent_repo_count_v on ghnd_owner_repos_v.repo_id = parent_repo_count_v.parent_repo_id
	
;	




/*owner summary*/

create or replace view owner_summ_v
as 

select

repo_summ_v.owner_id,
repo_summ_v.source_owner_id,
repo_summ_v.login, 
repo_summ_v.owner_html_url, 
repo_summ_v.owner_type, 
repo_summ_v.owner_processed_yn, 
repo_summ_v.repo_id,
repo_summ_v.source_repo_id,
repo_summ_v.parent_repo_id,
repo_summ_v.name, 
repo_summ_v.full_name,
repo_summ_v.repo_html_url, 
repo_summ_v.topics, 
repo_summ_v.created_at,
repo_summ_v.updated_at, 
repo_summ_v.repo_processed_yn,

SUM(repo_summ_v.in_degree) owner_in_degree,
repo_summ_v.child_repo_processed_count,
repo_summ_v.child_repo_unprocessed_count,
SUM(repo_summ_v.out_degree) owner_out_degree
from 


repo_summ_v
group by 

repo_summ_v.owner_id,
repo_summ_v.source_owner_id,
repo_summ_v.login, 
repo_summ_v.owner_html_url, 
repo_summ_v.owner_type, 
repo_summ_v.owner_processed_yn, 
repo_summ_v.repo_id,
repo_summ_v.source_repo_id,
repo_summ_v.parent_repo_id,
repo_summ_v.name, 
repo_summ_v.full_name,
repo_summ_v.repo_html_url, 
repo_summ_v.topics, 
repo_summ_v.created_at,
repo_summ_v.updated_at, 
repo_summ_v.repo_processed_yn
order by 
repo_summ_v.owner_type, 
repo_summ_v.login
;

	

	/*
	
	left join 
	(select 
	parent_parent_repo_id repo_id,
	count(*) num_child_repos,
	SUM(case when child_repo_processed_yn = 1 THEN 1 ELSE 0 END) child_repos_processed,
	
	
	from 
	
	ghnd_parent_child_owner_repos_v
	where parent_parent_repo_id IS NOT NULL
	group by ghnd_parent_child_owner_repos_v.parent_parent_repo_id
	
	
	
	
	group by ghnd_parent_child_owner_repos_v.parent_repo_id
	) parent_repo_summ on ghnd_owner_repos_v.parent_repo_id = parent_repo_summ.repo_id
	
	left join
	(select ghnd_parent_child_owner_repos_v) child_repo_summ on ghnd_owner_repos_v.repo_id = child_repo_summ.repo_id
	
	



;

*/

/*
--get the total number of forked child repos

create or replace view 
parent_child_owner_repo_summ_v
as 
	select 
	count(*) total_repos,
	SUM(case when ghnd_owners.parent_repo_id is null then 0 else 1 END) forked_child_repos,
	ghnd_owners.owner_id,
	ghnd_owners.source_owner_id,
	ghnd_owners.login,
	ghnd_owners.owner_html_url,
	ghnd_owners.owner_type,
	ghnd_owners.owner_processed_yn
	from
	ghnd_parent_child_owner_repos_v
	group by
	ghnd_owners.owner_id,
	ghnd_owners.source_owner_id,
	ghnd_owners.login,
	ghnd_owners.owner_html_url,
	ghnd_owners.owner_type,
	ghnd_owners.owner_processed_yn
	order by ghnd_owners.owner_type, ghnd_owners.login;

*/

