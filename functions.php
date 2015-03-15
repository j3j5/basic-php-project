<?php

function twitter($username) {
	global $twitter_settings, $folder_path;

	$api = new \j3j5\TwitterApio($twitter_settings);

	$tweets = array();

	$i = 1;
	$count = 200;

	foreach($api->get_timeline('statuses/user_timeline', array('screen_name' => $username, 'count' => $count)) as $page) {
// 		echo "Retrieving page $i" . PHP_EOL;
		if(is_array($page) ) {
			$tweets = array_merge($tweets, $page);
		}
		$i++;
	}

	if(empty($tweets) ) {
		exit;
	}

	$data = extract_data($username, $tweets);
	write_tweets_into_files($username, $data);
// 	echo "Done!" . PHP_EOL;

	return array('result' => 200, 'msg' => 'Congrats! All files have been downloaded.');
}

function extract_data($username, $tweets) {
	$user = array();
	// 	echo "Sorting tweets" . PHP_EOL;
	foreach($tweets AS $tweet) {
		$date_file = date("Y_m", strtotime($tweet['created_at']));
		// 		echo "Adding tweet {$tweet['id']}" . PHP_EOL;
		if(empty($user) && strtolower($tweet['user']['screen_name']) == strtolower($username)) {
			$user = $tweet['user'];
		}
		if(!isset($date_groups[$date_file])) {
			$date_groups[$date_file]['date'] = date("Y-m-d", strtotime($tweet['created_at']));
		}
		$date_groups[$date_file]['tweets'][] = $tweet;
	}
	return array('date_groups' => $date_groups, 'user' => $user);
}

function write_tweets_into_files($username, $data) {
	global $folder_path;

	$date_groups = $data['date_groups'];
	$user = $data['user'];

	// Create the folder structure
	$folder_segments = array($folder_path, 'twitter', $username, 'data', 'js');
	$path = '';
	foreach($folder_segments AS $tmp_path) {
		// Remove last '/'
		if(strpos($tmp_path, DIRECTORY_SEPARATOR, mb_strlen($tmp_path)-1) !== FALSE) {
			$tmp_path = mb_substr($tmp_path, 0, -1);
		}
		$path .= $tmp_path . DIRECTORY_SEPARATOR;
		if(!is_dir($path) && !mkdir($path) && !is_writable($path)) {
			echo "Error creating the folder at $path." . PHP_EOL;
			exit;
		}
	}
	// Remove the trailing DIRECTORY_SEPARATOR
	$path = mb_substr($path, 0, -1);

	$index_filename = $path . DIRECTORY_SEPARATOR . "tweet_index.js";
	$index_text = 'var tweet_index = ';
	$index_array = array();

	$tweet_count = 0;
	foreach($date_groups AS $date => $data) {
		$tweets_filename = $path . DIRECTORY_SEPARATOR . "tweets" . DIRECTORY_SEPARATOR . "$date.js";
		$folder=  "$path" . DIRECTORY_SEPARATOR . "tweets";
		if(!is_dir($folder) && !mkdir($folder) && !is_writable($folder)) {
			echo "Error creating the folder at $folder." . PHP_EOL;
			exit;
		}

		if(is_file($tweets_filename)) {
			unlink($tweets_filename);
		}

		$tweets_text = "Grailbird.data.tweets_$date = ". PHP_EOL;
		$tweets_text .= json_encode($data['tweets']);
		file_put_contents($tweets_filename, $tweets_text);
// 		echo "Done with $tweets_filename. Adding to the index." . PHP_EOL;

		// Add to index
		$index_array[] = array(
			"file_name" => "data". DIRECTORY_SEPARATOR ."js". DIRECTORY_SEPARATOR ."tweets". DIRECTORY_SEPARATOR ."$date.js",
			"year" => date("Y", strtotime($data['date'])),
			"var_name" => "tweets_$date",
			"tweet_count" => count($data['tweets']),
			"month" =>  date("n", strtotime($data['date'])),
		);
		$tweet_count += count($data['tweets']);
	}
	// Write the index
	$index_text .= json_encode($index_array, JSON_UNESCAPED_SLASHES);
	file_put_contents($index_filename, $index_text);

	// Write the user_details
	$user_filename = $path . DIRECTORY_SEPARATOR . "user_details.js";
	$user_text = "var user_details =  ";
	$user_array = array(
		"screen_name" => $username,
		"full_name" => $user['name'],
		"bio" => $user['description'],
		"id" => $user['id'],
		"created_at" => $user['created_at'],
	);
	$user_text .= json_encode($user_array, JSON_UNESCAPED_SLASHES);
	file_put_contents($user_filename, $user_text);

	// Write the payload_details
	$payload_filename = $path . DIRECTORY_SEPARATOR . "payload_details.js";
	$payload_text = "var payload_details =  ";
	$payload_array = array(
		"tweets" => $tweet_count,
		"created_at" => date("Y-m-d H:i:s O"),
		"lang" => isset($user['lang']) ? $user['lang'] : '',
	);

	$payload_text .= json_encode($payload_array, JSON_UNESCAPED_SLASHES);
	file_put_contents($payload_filename, $payload_text);
}

/**
 * Check the folder where it'll download the images and download the URLs from
 * Instagram's endpoint.
 */
function instagram($username) {
	global $folder_path;
	global $image_counter, $max_number_images;
	// Check whether the folder is writable
		if(!is_dir($pic_folder)) {
			$result = mkdir($pic_folder);
	if(is_writable($folder_path .'/instagram/')) {
		$pic_folder = "$folder_path/instagram/$username";
			if(!$result) {
				$error = 'The folder ' . $pic_folder . ' could not be created.';
				return array('result' => 403, 'error' => $error);
			}
			// The folder is set with 777 because, unless it was already created
			chmod($pic_folder, 0777);
		}
	} else {
		$error = 'The folder ' . $folder_path . ' is not writable.';
		return array('result' => 403, 'error' => $error);
	}
	$pic_folder .= '/';

	$last_id = '';
	$stop = FALSE;
	while($stop === FALSE) {
		$url = "http://instagram.com/$username/media?max_id=$last_id";
		// Fetch the JSON from Instagram, TODO: add error handling in case the endpoint doesn't return proper data
		$json = file_get_contents($url);
		$data = json_decode($json, TRUE);
		$last_id = retrieve_images($data, $pic_folder);

		// If $last_id is an array it means that there's an error.
		if(is_array($last_id)) {
			return $last_id;
		}

		if(!isset($data['more_available']) OR !$data['more_available'] OR empty($last_id) OR $image_counter >= $max_number_images) {
			$stop = TRUE;
		}
	}
	return array('result' => 200, 'msg' => 'Congrats! All files have been downloaded.');
}

/**
 * Retrieve the actual pics from the URLs provided by Instagram's endpoint.
 */
function retrieve_images($data, $pic_folder) {
	global $image_counter, $max_number_images;

	$last_id = '';
	if(!empty($data) && isset($data['items']) && is_array($data['items'])) {
		foreach($data['items'] AS $item) {
			if(isset($item['images']['standard_resolution']['url'])) {
				$image_counter++;
				$last_id = $item['id'];
				$image_url = $item['images']['standard_resolution']['url'];
				$pic_path = $pic_folder.$last_id.mb_substr($image_url, -4);

				// Download the image
				$pic = file_get_contents($image_url);
				$result = file_put_contents($pic_path, $pic);
				if($result === FALSE) {
					$error = "There was a problem while downloading and image. Please, check the permissions.";
					return array('result' => 500, 'error' => $error);
				}
				// Set permissions
				chmod($pic_path, 0666);
			}
			if($image_counter >= $max_number_images) {
				return $last_id;
			}
		}
	}
	return $last_id;
}

/**
 * Check whether the provided username is a valid Instagram one
 */
function is_valid_username($username) {
	$valid_username_pattern = "/^[a-zA-Z0-9._]{1,30}$/";
	return (bool) preg_match($valid_username_pattern, $username);
}
