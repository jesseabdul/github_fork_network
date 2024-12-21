	<?php

//https://github.com/php-curl-class/php-curl-class
require_once 'c:/php/vendor/autoload.php';
use Curl\Curl;

//define a global variable to track all of the owners that have been processed so far in this iteration, it will contain the version control system's unique id so it's easy to compare them during processing.  If an owner has been processed or if the processing has started do not process the owner again during this execution
$processed_owner_ids = array();

//define a global variable to track all of the repos that have been processed so far in this iteration, it will contain the version control system's unique id so it's easy to compare them during processing.  If an repo has been processed or if the processing has started do not process the repo again during this execution
$processed_repo_ids = array();

$api_request_counter = 0;

function curl_request($url, &$json_object, &$next_link_url, &$http_code, $request_id = null)
{
	$curl = new Curl();

	$curl->setOpt(CURLOPT_SSL_VERIFYPEER , false);
	$curl->setOpt(CURLOPT_HEADER, true);
	$curl->setHeader('Authorization', 'Bearer '.GH_PAT);

	$curl->get($url);


	//initialize the reference variable values:
	$json_object = null;
	$next_link_found = null;



	
	echo "the value of HTTP status code is: ".$curl->getHttpStatusCode()."\n";
	//set the http_code to the HTTP code returned by the curl response
	$http_code = $curl->getHttpStatusCode();

	if ($return_value = (!$curl->error)) 
	{
		//the curl request was successful
		$curl_response = $curl->response;
		
//		echo "The value of \$curl_response is: ".$curl_response."\n";

//		file_put_contents($GLOBALS['debug_path'].$request_id.".txt", $curl_response);
		
		if ($return_value = parse_json_from_api ($curl_response, $json_object, $next_link_url))
		{
			//the JSON was successfully parsed
			echo "the JSON was successfully parsed\n";
		}
		else
		{
			//the JSON was not successfully parsed
			echo "The JSON was not successfully parsed\n";
		}
		
	} 
	else 
	{
		//this is an error

		$curl->diagnose();
		echo 'Error: ' . $curl->errorMessage . "\n";
	}
		
//		echo "response headers are: ".var_export($curl->responseHeaders, true)."\n";

	
	echo "This was the ".($GLOBALS['api_request_counter']++)."th api request so far\n";
	
	
	unset($curl_response);
	
	unset($curl);
	
	return $return_value;
}


function parse_json_from_api ($json_content, &$json_object, &$next_link_url)
{
	echo "running parse_json_from_api (\$json_content, &\$json_object, &\$next_link_url)\n";
	
	$return_value = true;

//	if (preg_match($pattern='/link\:.+\<(.+)\>; rel="next",.+(\[.+\])/s', $json_content, $matches))	//regexp to get both the next link and the json content
	
	//for very long json strings the regular expression was unable to find the next link so the first 3000 characters are used instead
	if (!($next_link_found = preg_match($pattern='/link\:.+\<(.+)\>; rel="next",/s', substr($json_content, 0, 3000), $matches)))
	{
		//the next link was not found
		echo "The next link was not found\n";
		$next_link_url = null;
	}
	else	//the next link was found
		$next_link_url = $matches[1];


	echo "The value of \$next_link_url is: ".$next_link_url."\n";

	
	
	
	//parse the content part of the data:
	if (preg_match('/\R\R(.+)/s', $json_content, $matches))
	{
//			echo "the value of matches is: ".var_export($matches, true)."\n";

		//convert the json content to a json object:
		$json_object = json_decode($matches[1], true);
	}
	else
	{
		echo "The json object content parsing was unsuccessful\n";
		$return_value = false;
		
	}
	return $return_value;
}




function connect_mysql (&$pdo)
{
	// connect to PDO
	$pdo = new PDO("mysql:host=localhost;dbname=github_network", "github_dev", "myadm1n");

	// the following tells PDO we want it to throw Exceptions for every error.
	// this is far more useful than the default mode of throwing php errors
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	
}



?>