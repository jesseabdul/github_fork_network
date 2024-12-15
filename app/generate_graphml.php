<?php

//generate the graph by querying the vertices and edges

include_once (($current_dir = dirname(__FILE__))."/functions/config.php");
include_once ($current_dir."/functions/git_functions.php");

//connect to mysql with PDO
connect_mysql ($pdo);

//open the template graph file
$graphml_content = file_get_contents("./template.graphml");

//echo "the value of \$graphml_content is: " . $graphml_content."\n";

echo "generate the graphml content for the vertex and edge data\n";

//generate the graphml content for the vertex and edge data
$graphml_content = str_replace("[EDGE_DATA]", generate_edge_data(), str_replace("[VERTEX_DATA]", generate_vertex_data(), str_replace("[GEN_DATE]", date("m/d/Y"), $graphml_content)));


//echo "the value of \$graphml_content is: " . $graphml_content."\n";


//$vertex_data_string = generate_vertex_data();

//$edge_data_string = generate_edge_data();


echo "save the generated graphml file\n";

//save the generated graphml file:
file_put_contents("../Analysis/Networks/github_forked_repo_network.".date("Ymd").".graphml", $graphml_content);




//generate the XML in graphml format for the vertices
function generate_vertex_data ()
{
	//intialize the vertex data string:
	$vertex_data_string = '';
	
	//query the database for the repos so the vertex data can be generated
	$query = "select * from 
	(select child_repos.child_source_owner_id,
	child_repos.child_login login,
	child_repos.child_owner_html_url owner_html_url,
	child_repos.child_owner_type owner_type,
	child_repos.child_source_repo_id source_repo_id,
	child_repos.child_name repo_name,
	child_repos.child_full_name repo_full_name,
	child_repos.child_repo_html_url repo_html_url,
	child_repos.child_topics topics,
	DATE_FORMAT(child_repos.child_created_at, '%m/%d/%Y') created_at,
	DATE_FORMAT(child_repos.child_updated_at, '%m/%d/%Y') updated_at
	from ghnd_parent_child_owner_repos_v child_repos
	
	
	WHERE 
	/*only include repos where both parent and child have been completely processed*/
	child_repos.child_repo_processed_yn = 1 
	AND child_repos.parent_repo_processed_yn = 1 
	

	UNION

	/*include only distinct parent repos that have at least one child repo*/
	select DISTINCT
	parent_repos.parent_source_owner_id source_owner_id,
	parent_repos.parent_login login,
	parent_repos.parent_owner_html_url owner_html_url,
	parent_repos.parent_owner_type owner_type,
	parent_repos.parent_source_repo_id source_repo_id,
	parent_repos.parent_name repo_name,
	parent_repos.parent_full_name repo_full_name,
	parent_repos.parent_repo_html_url repo_html_url,
	parent_repos.parent_topics topics,
	DATE_FORMAT(parent_repos.parent_created_at, '%m/%d/%Y') created_at,
	DATE_FORMAT(parent_repos.parent_updated_at, '%m/%d/%Y') updated_at
	from
	ghnd_parent_child_owner_repos_v parent_repos 
	
	where 
	parent_repos.child_repo_processed_yn = 1
	AND parent_repos.parent_repo_processed_yn = 1
	) processed_parent_child_repos

	ORDER BY login,
	repo_name";
	

	// prepare the statement. the placeholders allow PDO to handle substituting
	// the values, which also prevents SQL injection
	$stmt = $GLOBALS['pdo']->prepare($query);


	//execute the query
	if ($stmt->execute())
	{
		//the query was successfully executed
		
		//loop through the repo/owner records:
		while ($db_info = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			//append the current vertex information
			$vertex_data_string .= "    <node id=\"".$db_info['source_repo_id']."\">
      <data key=\"v_label\">".$db_info['repo_full_name']."</data>
      <data key=\"v_id\">".$db_info['source_repo_id']."</data>
      <data key=\"v_repo_html\">".$db_info['repo_html_url']."</data>
      <data key=\"v_repo_name\">".$db_info['repo_name']."</data>
      <data key=\"v_repo_full_name\">".$db_info['repo_full_name']."</data>
      <data key=\"v_repo_created_at\">".$db_info['created_at']."</data>
      <data key=\"v_repo_updated_at\">".$db_info['updated_at']."</data>
      <data key=\"v_owner_login\">".$db_info['login']."</data>
      <data key=\"v_owner_html\">".$db_info['owner_html_url']."</data>
      <data key=\"v_owner_type\">".$db_info['owner_type']."</data>
    </node>\n";
		}

	}		
	
	return $vertex_data_string;
}


function generate_edge_data()
{
	//intialize the vertex data string:
	$edge_data_string = '';
	
	//query the database for the repos so the vertex data can be generated
	$query = "select
	child_source_repo_id, 
	parent_source_repo_id
	from ghnd_parent_child_owner_repos_v 
	where parent_repo_processed_yn = 1
	AND child_repo_processed_yn = 1";
	

	// prepare the statement. the placeholders allow PDO to handle substituting
	// the values, which also prevents SQL injection
	$stmt = $GLOBALS['pdo']->prepare($query);


	//execute the query
	if ($stmt->execute())
	{
		//the query was successfully executed
		
		//loop through the repo/owner records:
		while ($db_info = $stmt->fetch(PDO::FETCH_ASSOC)) 
		{
			//append the current vertex information
			$edge_data_string .= "    <edge source=\"".$db_info['child_source_repo_id']."\" target=\"".$db_info['parent_source_repo_id']."\"></edge>\n";
		}
	}		
	
	return $edge_data_string;	
	
	
	
}



?>