<?php

//https://github.com/php-curl-class/php-curl-class
require_once 'c:/php/vendor/autoload.php';
use Curl\Curl;



//send the curl request for the organizations:
if (curl_request('https://api.github.com/organizations?per_page=100', $curl_response))
{


	echo "The return value is: \n";

	echo $curl_response;

	if (preg_match($pattern='/link\:.+\<(.+)\>; rel="next",/', $curl_response, $matches))
	{
		echo "the next URL link was found\n";
		
		

	//	echo var_export($matches, true);

		$next_url = $matches[1];
		
		echo "The value of \$next_url is: ".$next_url."\n";
		


//		echo "the value of \$curl_response is: ".$curl_response."\n";
		
		//parse the content part of the data:
		
		if (preg_match('/(\[.+\])/s', $curl_response, $matches))
		{
			echo "the value of matches is: ".var_export($matches, true)."\n";



			//convert the json content to a json object:
			$json_data = json_decode($matches[1]);

			//loop through the json data and parse the 


			echo "print the json object data:\n";
			echo var_export($json_data, true)."\n";
		}
		else
		{
			echo "The json object content parsing was unsuccessful\n";
			
		}
		
		
		
		
		
		//request the next link value:
		
		

		
	}
	else
	{
		echo "The json header content parsing was unsuccessful\n";

		
	}
}
else
{
	echo "The curl request was unsuccessful\n";

	
}

//parse this:

	//link: <https://api.github.com/organizations?per_page=100&page=1&since=8335>; rel="next", <https://api.github.com/organizations{?since}>; rel="first"





//parse the object enclosed by [], substring the data and then convert to json object



//echo "page 2\n";
//curl_request('https://api.github.com/organizations?per_page=100&page=1');



function curl_request($url, &$curl_response)
{
	
	$curl = new Curl();

	$curl->setOpt(CURLOPT_SSL_VERIFYPEER , false);
	$curl->setOpt(CURLOPT_HEADER, true);
	$curl->get($url);

	if ($curl->error) {
		$curl->diagnose();
		echo 'Error: ' . $curl->errorMessage . "\n";
	} else {
		$curl_response = $curl->response;
	}
	return (!$curl->error);
}



//this function requests the organizations in the GitHub network
function org_request ()
{
	
	
	
	
	
}


function org_repo_request()
{
	
	
	
}


function user_request ()
{
	
	
	
}


function user_repo_request()
{
	
	
	
}



//algorithm:

//request organizations in a loop

	//parse header for subsequent pages link


	//parse json content for organizations
	
	
	//loop through each organization
	
		//insert the organization into the DB if it doesn't already exist
		
		
		//request organization repos in a loop
		
			//parse header for subsequent pages link


			//parse json content for repos

			//loop through each repo in the organization
				
				//insert the repo and associate with the org (node type is "org")
				
				//check if there are any forks, if so then loop through them and request each repo and link it to the source repo
				
					//if forks > 0 then use the fork URL to identify all forked urls and insert into repos with the appropriate fork_repo_id
					//check if owner already exists, if so then use the appropriate ID and if not then insert the new owner record with the appropriate type

				
				
				



//make the org loop a recursive function


//make the user loop a recursive function, stop when no/null "next" link


	//can make both org and user loops the same function with a different type argument 



//create a function for parsing/looping through the repos and inserting them using the "next" link



?>