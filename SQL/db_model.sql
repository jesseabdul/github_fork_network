DROP TABLE `github_network`.`ghnd_repos`;
drop table `github_network`.`ghnd_owners`;

CREATE TABLE `github_network`.`ghnd_owners` (
  `owner_id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique numeric primary key',
  `source_owner_id` INT NOT NULL COMMENT 'The id defined in the source system (e.g. GitHub) for the given owner',
  `login` VARCHAR(100) NOT NULL COMMENT 'The organization or user name in the source data system',
  `html_url` VARCHAR(500) NOT NULL COMMENT 'The URL for the owner in the source system',
  `owner_type` ENUM('Organization', 'User') NOT NULL COMMENT 'The owner type defined in the source system, User or Organization',
  PRIMARY KEY (`owner_id`),
  UNIQUE INDEX `source_owner_id_UNIQUE` (`source_owner_id` ASC) VISIBLE,
  INDEX `owner_type_indx` (`owner_type` ASC) VISIBLE,
  INDEX `login_indx` (`login` ASC) VISIBLE)
COMMENT = 'GitHub Network Data - Repository Owners';



CREATE TABLE `github_network`.`ghnd_repos` (
  `repo_id` INT NOT NULL AUTO_INCREMENT COMMENT 'Unique numeric primary key',
  `source_repo_id` INT NOT NULL COMMENT 'The id defined in the source system (e.g. GitHub) for the given repo',
  `parent_repo_id` INT NULL COMMENT 'foreign key that references the ghnd_repo_owners table that defines the fork dependency for the given repo (if any).  This foreign key points to a repo_id of a ghnd_repos record that the given repo is a fork from.  This field is null when the given repo is not a fork from any repository',
  `repo_name` VARCHAR(100) NOT NULL COMMENT 'The repository name',
  `full_name` VARCHAR(400) NOT NULL COMMENT 'The full name for the repository',
  `repo_url` VARCHAR(500) NOT NULL COMMENT 'The URL for the owner in the source system',
  `topics` VARCHAR(1000) NULL COMMENT 'The comma-delimited list of topics defined for the repo in the source system',
  `created_at` DATETIME NULL COMMENT 'The repository\'s created_at value',
  `updated_at` DATETIME NULL COMMENT 'The repository\'s updated_at value',
  `owner_id` INT NOT NULL COMMENT 'Foreign key references to the ghnd_owners record that owns the repository',
  PRIMARY KEY (`repo_id`),
  UNIQUE INDEX `source_repo_id_UNIQUE` (`source_repo_id` ASC) VISIBLE,
  INDEX `parent_repo_id` (`parent_repo_id` ASC) VISIBLE,
  INDEX `owner_id` (`owner_id` ASC) VISIBLE)
COMMENT = 'GitHub Network Data - Repositories';



create view ghnd_owner_repos as select ghnd_owners.owner_id, source_owner_id, login, html_url, owner_type, repo_id, source_repo_id, parent_repo_id, repo_name, full_name, topics, created_at, updated_at from ghnd_owners inner join ghnd_repos on ghnd_owners.owner_id = ghnd_repos.owner_id order by owner_type, ghnd_owners.owner_id, repo_name; 

--create another view with one more level of the associated child repos

--create another view summarizing the associated child repos (count, max created, max updated, etc.)
