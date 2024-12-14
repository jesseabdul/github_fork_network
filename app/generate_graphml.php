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
file_put_contents("../graphml_output/github_forked_repo_network.".date("Ymd").".graphml", $graphml_content);




//generate the XML in graphml format for the vertices
function generate_vertex_data ()
{
	//intialize the vertex data string:
	$vertex_data_string = '';
	
	//query the database for the repos so the vertex data can be generated
	$query = "select * from 
	(select ghnd_parent_child_owner_repos_v.child_source_owner_id,
	ghnd_parent_child_owner_repos_v.child_login login,
	ghnd_parent_child_owner_repos_v.child_owner_html_url owner_html_url,
	ghnd_parent_child_owner_repos_v.child_owner_type owner_type,
	ghnd_parent_child_owner_repos_v.child_source_repo_id source_repo_id,
	ghnd_parent_child_owner_repos_v.child_name repo_name,
	ghnd_parent_child_owner_repos_v.child_full_name repo_full_name,
	ghnd_parent_child_owner_repos_v.child_repo_html_url repo_html_url,
	ghnd_parent_child_owner_repos_v.child_topics topics,
	DATE_FORMAT(ghnd_parent_child_owner_repos_v.child_created_at, '%m/%d/%Y') created_at,
	DATE_FORMAT(ghnd_parent_child_owner_repos_v.child_updated_at, '%m/%d/%Y') updated_at
	from ghnd_parent_child_owner_repos_v
	
	
	WHERE 
	/*only include repos that have been completely processed*/
	ghnd_parent_child_owner_repos_v.child_repo_processed_yn = 1 
	AND ghnd_parent_child_owner_repos_v.parent_repo_processed_yn = 1 

	/*only include repos that have a connection to a parent repo*/
	AND ghnd_parent_child_owner_repos_v.child_parent_repo_id IS NOT NULL
	
	/*AND ghnd_parent_child_owner_repos_v.owner_processed_yn = 1 */


	UNION

	/*include only distinct parent repos that have at least one child repo*/
	select DISTINCT
	ghnd_parent_child_owner_repos_v.parent_source_owner_id source_owner_id,
	ghnd_parent_child_owner_repos_v.parent_login login,
	ghnd_parent_child_owner_repos_v.parent_owner_html_url owner_html_url,
	ghnd_parent_child_owner_repos_v.parent_owner_type owner_type,
	ghnd_parent_child_owner_repos_v.parent_source_repo_id source_repo_id,
	ghnd_parent_child_owner_repos_v.parent_name repo_name,
	ghnd_parent_child_owner_repos_v.parent_full_name repo_full_name,
	ghnd_parent_child_owner_repos_v.parent_repo_html_url repo_html_url,
	ghnd_parent_child_owner_repos_v.parent_topics topics,
	DATE_FORMAT(ghnd_parent_child_owner_repos_v.parent_created_at, '%m/%d/%Y') created_at,
	DATE_FORMAT(ghnd_parent_child_owner_repos_v.parent_updated_at, '%m/%d/%Y') updated_at
	from
	ghnd_parent_child_owner_repos_v 
	
	where 
	ghnd_parent_child_owner_repos_v.child_repo_processed_yn = 1
	AND ghnd_parent_child_owner_repos_v.parent_repo_processed_yn = 1
	) child_parent_repos

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