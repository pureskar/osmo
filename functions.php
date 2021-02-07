//Compress HTML Content
function compressor($html){
return ob_html_compress($html);
ob_start("ob_html_compress");
ob_end_flush();	
}

//Generate Unique Secured Key
function genkey(){
$token = openssl_random_pseudo_bytes(16);
return $token = bin2hex($token);
}

//Calculate Distance Between Co-ordinates (GMAPS Auth Key Reqd.) 
function gmapdistance($origin, $destination, $mkey){
$origin      = urlencode($origin);
$destination = urlencode($destination);
$mkey = $mkey;
$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$origin&destinations=$destination&key=".$mkey;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($ch);
curl_close($ch);
$response_a = json_decode($response, true);
$GLOBALS['gmapdistance'] = $response_a;
}

//Number Data To Character Data (Encrypted)
function toAlpha($data){
    $alphabet =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    $alpha_flip = array_flip($alphabet);
    if($data <= 25){
      $GLOBALS['toAlpha'] = $alphabet[$data];
    }
    elseif($data > 25){
      $dividend = ($data + 1);
      $alpha = '';
      $modulo;
      while ($dividend > 0){
        $modulo = ($dividend - 1) % 26;
        $alpha = $alphabet[$modulo] . $alpha;
        $dividend = floor((($dividend - $modulo) / 26));
      } 
      $GLOBALS['toAlpha'] =  $alpha;
    }
}

//Character Data To Number Data (Decrypted)
function toNum($data) {
    $alphabet = array( 'a', 'b', 'c', 'd', 'e',
                       'f', 'g', 'h', 'i', 'j',
                       'k', 'l', 'm', 'n', 'o',
                       'p', 'q', 'r', 's', 't',
                       'u', 'v', 'w', 'x', 'y',
                       'z'
                       );
    $alpha_flip = array_flip($alphabet);
    $return_value = -1;
    $length = strlen($data);
    for ($i = 0; $i < $length; $i++) {
        $return_value +=
            ($alpha_flip[$data[$i]] + 1) * pow(26, ($length - $i - 1));
    }
    $GLOBALS['toNum'] =  $return_value;
}




//Validate Email ID (Third Party Dependency - Cost Involved After Threshold Limit - APILayer)
function validate_mail($mail){
// set API Access Key
$access_key = 'fd402asdfsdhfgsdgf';

// set email address
$email_address = $mail;

// Initialize CURL:
$ch = curl_init('http://apilayer.net/api/check?access_key='.$access_key.'&email='.$email_address.'');  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Store the data:
$json = curl_exec($ch);
curl_close($ch);

// Decode JSON response:
$validationResult = json_decode($json, true);

// Access and use your preferred validation result objects
$GLOBALS['validate_mail']['format_valid'] = $validationResult['format_valid'];
$GLOBALS['validate_mail']['smtp_check'] = $validationResult['smtp_check'];
$GLOBALS['validate_mail']['score'] = $validationResult['score'];
$GLOBALS['validate_mail']['smtp_check'] = $validationResult['smtp_check'];
}


//Fetch The OG TAG Image from an URL.
function fetch_image($url){
	 $main_url= $url;
   @$str = file_get_contents($main_url);


   // This Code Block is used to extract title
   if(strlen($str)>0)
   {
     $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
     preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title);
   }
  

   // This Code block is used to extract description 
   $b =$main_url;
   @$url = parse_url( $b ) ;
   @$tags = get_meta_tags( $url['scheme'].'://'.$url['host'] );


   // This Code Block is used to extract any image 1st image of the webpage
   $dom = new domDocument;
   @$dom->loadHTML($str);
   $images = $dom->getElementsByTagName('img');
   foreach ($images as $image)
   {
     $l1=@parse_url($image->getAttribute('src'));
     if($l1['scheme'])
     {
	   $img[]=$image->getAttribute('src');
     }
     else
     {
	
     }
   }


   // This Code Block is used to extract og:image which facebook extracts from webpage it is also considered 
   // the default image of the webpage
   $d = new DomDocument();
   @$d->loadHTML($str);
   $xp = new domxpath($d);
   foreach ($xp->query("//meta[@property='og:image']") as $el)
   {
     $l2=parse_url($el->getAttribute("content"));
     if($l2['scheme'])
     {
	   $img2[]=$el->getAttribute("content");
     }
     else
     {
	
     }
   }

   if($img2)
   {
      $GLOBALS['fetch_image'] =  $img2[0];
   }
   else
   {
      $GLOBALS['fetch_image'] =  $img[0];
   }
   

   
}




//Google Image Scrapper
function goimg($key){
// We'll process this feed with all of the default options.
$feed = new SimplePie();
       //Curate From The internet
       // Set which feed URL to process.
$feed->set_feed_url(array(
"http://news.google.com/news?q=$key&output=rss",
));
 

// Run SimplePie.
$feed->init();
 
// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
$feed->handle_content_type(); 
 
$count = 0; //First we set the counter to be zero
$output = 10; //This is the desired number of content to display



foreach ($feed->get_items() as $item) {
	$count++;
	$imag = $item->get_permalink();
	$whatIWant = substr($imag, strpos($imag, ";url=") + 1); 
	$wiw = str_replace("url=","",$whatIWant);
	$headline = $item->get_title();
	fetch_image($wiw);
	$news_image =  $GLOBALS['fetch_image'];
	$news_date = $item->get_date('j F Y | g:i a');
	//Dettol News Date And Send It TO Unixtime
	$GLOBALS['dettol'] = str_replace( " | ", " ", $news_date);
$unixtime = strtotime($GLOBALS['dettol']);
$time = date("m/d/Y h:i:s A",$unixtime);
	dettol($headline);
	$dettol = $GLOBALS['dettol'];

$authh = 	substr($headline, strrpos($headline, ' - ') + 1);


// echo $news_image."<br>";
// echo $wiw."<br>";
$u1 = str_replace('https://','', $wiw);
$u2 = str_replace('http://','', $u1);
$u3 = str_replace('ttps://','', $u2);
$u4 = str_replace('ttp://','', $u3);


$url = "http://".$u4;
fetch_image($url);
$rtg =  $GLOBALS['fetch_image'];

if (file_exists($rtg)) {
 
	break;
	
	$GLOBALS['goimg']  = $rtg;
}
}
}



//Encryption Algorithm (Convert Number To Encrypted Code)
function enc($a){
	$b = bin2hex($a);
	$c = $array  = array_map('intval', str_split($b));
	
	$got = NULL;
	foreach($c as $go){
	switch ($go) {
    case 1:
        $d = "A";
        
        break;
    case 2:
        $d = "B";
        // $got = $got.$d;
        break;
    case 3:
        $d = "C";
        // $got = $got.$d;
        break;
    case 4:
        $d = "D";
        // $got = $got.$d;
        break;
    case 5:
        $d = "E";
        // $got = $got.$d;
        break;
    case 6:
        $d = "F";
        // $got = $got.$d;
        break;
	case 7:
        $d = "G";
        // $got = $got.$d;
        break;
    case 8:
        $d = "H";
        // $got = $got.$d;
        break;
    case 9:
        $d = "I";
        // $got = $got.$d;
        break;
    case 0:
        $d = "J";
        // $got = $got.$d;
        break;
            
   
}
$got = $got.$d;
}
$e = $got;
print_r($e);
$GLOBALS['enc'] = $e;
}

//Decryption Algorithm (Decrypt Encrypted Code To Number)
function dec($a){
	 $d = str_split($a, 1);
	
	$got = NULL;
	foreach($d as $go){
	switch ($go) {
    case "A":
        $d = 1;
        
        break;
    case "B":
        $d = 2;
        break;
    case "C":
        $d = 3;
        break;
    case "D":
        $d = 4;
        break;
    case "E":
        $d = 5;
        break;
    case "F":
        $d = 6;
        break;
	case "T":
        $d = 7;
        break;
    case "G":
        $d = 8;
        break;
    case "H":
        $d = 9;
        break;
    case "I":
        $d = 0;
        break;
      
}
$got = $got.$d;
}
$b = $got;
	
	$c = hex2bin($b);

	
$GLOBALS['dec'] = $c;
}




// Custom Information Fetch For Any Query (Single Row Record)
function custom_query($query){

	connect_db();
	
$servername = $GLOBALS['servername'];
$username = $GLOBALS['username'];
$password = $GLOBALS['password'];
$dbname = $GLOBALS['dbname'];

$con = mysqli_connect($servername,$username,$password,$dbname);
if (mysqli_connect_errno()){echo "Failed to connect to MySQL: " . mysqli_connect_error();}
$a1=mysqli_query($con,$query);
$b1=mysqli_fetch_array($a1,MYSQLI_ASSOC);
$num_rows_a1 = mysqli_num_rows($a1);
$GLOBALS['custom_query_records'] = $num_rows_a1;
$GLOBALS['bypass'] = $b1;

mysqli_close($con);


}

// Custom Insert Query
function custom_insert_query($insert_query){
	connect_db();
	
$servername = $GLOBALS['servername'];
$username = $GLOBALS['username'];
$password = $GLOBALS['password'];
$dbname = $GLOBALS['dbname'];
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = $insert_query;

if (mysqli_query($conn, $sql)) {
   $GLOBALS['custom_insert_status'] = 1;
} else {
    $GLOBALS['custom_insert_status'] = 0;
    $GLOBALS['custom_insert_error_report'] = "Error: ".mysqli_error($conn);
   
}

mysqli_close($conn);

}

//Connect To DB Credentials
function connect_db(){
$GLOBALS['servername'] = "localhost";
$GLOBALS['username'] = "osmottyt_xxx";
$GLOBALS['password'] = "zzzzzzzzz";
$GLOBALS['dbname'] = "osmottyt_yyy";
}







//Fetch Multiple Row Records From DB
function dua($query){
	connect_db();
	
$servername = $GLOBALS['servername'];
$username = $GLOBALS['username'];
$password = $GLOBALS['password'];
$dbname = $GLOBALS['dbname'];
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = $query;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    $dua = array();
    $duacount = 0;
    while($row = $result->fetch_assoc()) {
    $dua[$duacount] = $row;
    $duacount = $duacount + 1;
    	
    }
} else {
    // echo "0 results";
}
$conn->close();

$GLOBALS['dua'] = $dua;
$GLOBALS['dua_count'] = $duacount;
}



