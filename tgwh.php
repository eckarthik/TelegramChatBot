<?php 

$method = $_SERVER['REQUEST_METHOD'];

// Process only when method is POST
if($method == 'POST'){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);
	$text = $json->result->resolvedQuery;

	/*switch ($text) {
		case 'hi':
			$speech = "Hi, Nice to meet you";
			break;

		case 'bye':
			$speech = "Bye, good night";
			break;

		case 'anything':
			$speech = "Yes, you can type anything here.";
			break;
		case 'tellajoke':
            $speech = "I don't know any jokes";
            break;
		default:
			$speech = "Sorry, I didnt get that. Please ask me something else.";
			break;
	}*/
    if($json->result->action=="joke")
    {
        $speech = "Here's a Joke: ";
        $jokes = array("I asked my North Korean friend how it was there, he said he couldn't complain.","My email password has been hacked. That's the third time I've had to rename the cat.",
        "A computer once beat me at chess, but it was no match for me at kick boxing.",
        "It's ok computer, I go to sleep after 20 minutes of inactivity too.","I tried to escape the Apple store. I couldn't because there were no Windows.",
        "What does a baby computer call its father? Data.",
        "How many programmers does it take to change a light bulb? None. It's a hardware problem.",
        "I needed a password eight characters long so I picked Snow White and the Seven Dwarfs.",
        "My New Years resolution is 1080p.",
        "Yesterday I decided to change my WiFi name to 'Hack me if you can' and when I woke up this morning I saw the name changed to 'Challenge accepted' somebody help.",
        "Set your wifi password to 2444666668888888. So when someone asks for it, tell them it's 12345678.",
        "iPhone8 (X) has facial recognition. It looked at my face and told me that I can't afford it...");
        $randomnum = rand(0,count($jokes)-1);
        $speech .= $jokes[$randomnum];
    }
    elseif($json->result->action=="vtu")
    {
        $speech = "Last 3 Exam related information as found from the Official VTU Website \n \n";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://vtu.ac.in/exams-circulars-notifications/");
        curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_REFERER,"http://vtu.ac.in");
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        $data = curl_exec($ch);
    
        preg_match_all("/<p>(.*?)<\/p>/i",$data,$details);
        for($i=0;$i<3;$i++)
        {
            $speech.= strip_tags($details[0][$i])." - ";
            preg_match_all("/<a href=\"(.*?)\"/i",$details[0][$i],$link);
            $speech.= $link[1][0];
            $speech.= "\n \n";
        }
        
        //$speech = "Find it in the official website";
    }
    elseif($json->result->action=="questionForTheBot")
    {
        $term = strtolower($json->result->parameters->term);
        if(preg_match('/([\+\-\*\/])/', $term, $matches)){
            /*$operator = $matches[2];

            switch($operator){
                case '+':
                    $p = $matches[1] + $matches[3];
                    break;
                case '-':
                    $p = $matches[1] - $matches[3];
                    break;
                case '*':
                    $p = $matches[1] * $matches[3];
                    break;
                case '/':
                    $p = $matches[1] / $matches[3];
                    break;
            }*/
        
            $speech = eval("return $term;");

        }
        else
        {
            if(strlen($term)>0)
            {
                $givenTerm = str_replace(" ","",$term);
                $data = file_get_contents("https://pc.net/glossary/definition/$givenTerm");
                preg_match_all("/<p>(.*?)<\/p>/i",$data,$details);
                if(strlen(strip_tags($details[0][0]))>0)
                {
                    $speech = strip_tags($details[0][0])."\n\n Information from http://pc.net/glossary/definition/$givenTerm";
                }
                else
                {
                    $speech = "Sorry, I cannot find any related information for what you have asked. Please ask me something about";
                }
            }
            else
            {
                $speech = "Sorry, I didnt get that. Please ask me something else.";
            }
            
        }
    }
    else
    {
        $questionsToShoot = array("I didn't get that. Can you say it again?",
        "Can you say that again?",
        "One more time?",
        "What was that?",
        "Say that again?",
        "I missed that.",
        "I am not trained enough to answer this question. I'm like a new born. It will take sometime for me to understand what you say.",
        "Whom do you like the most? SRK or Salman Khan?",
        "I hate people who use shortcuts while chatting.",
        "Do you use shortcuts while texting? Please don't do that with me. I'm not a human like you to understand shortcuts.",
        "Wanna here a joke? Let me know.",
        "Do you like coding?",
        "Do you like watching movies",
        "Do you like to play video games",
        "Do you like to eat?",
        "What's your favorite food?",
        "What's your favorite movie?",
        "Where do you stay?",
        "Do you know about chatbots?",
        "I can solve mathematical equations. Shoot a mathematical expression and i will answer it.",
        "I know everything about computers. Ask me something",
        "Movie trailers were originally shown after the movie, which is why they were called trailers.  I was shocked seeing this",
        "Do you know that you cannot snore and dream at the same time? I'm a non-living creature, if not, I would have tried to do this ",
        "Sorry, I didnt get that. Please ask me something else.");
        $randomnum = rand(0,count($questionsToShoot)-1);
        $speech = $questionsToShoot[$randomnum];;
    }

	$response = new \stdClass();
	$response->speech = $speech;
	$response->displayText = $speech;
	$response->source = "webhook";
	echo json_encode($response);
}
else
{
	echo "Method not allowed";
}

?>