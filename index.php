
<!DOCTYPE html>
<html>
<head>
  <!-- Data table and Bootstrap -->
  <meta charset="UTF-8">
  <title>ORGANISED</title>
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  <style type="text/css">
    .bs-example{
      margin: 20px;
    }
  </style>

  
  <script type="text/javascript" language="javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
  <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css">
  <script type="text/javascript" class="init">
    $(document).ready(function() {
    $('#contactTable').dataTable();
  } );
</script>
</head>

<body>
  <table id="contactTable" class = "display" cellspacing ="0" width="100%">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone Number</th>
        
      </tr>
    </thead>
    <tfoot>
       <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone Number</th>
        
      </tr>
    </tfoot>
    <tbody>

<?php
  require_once 'Google/Client.php';
  require_once 'Google/Service/Calendar.php';
  require_once 'appcred.php'; //appcredential class
  // require_once 'Google/Auth/AppIdentity.php';
  // require_once 'Google/Service/Storage.php';
  // require_once 'Google/Http/Request.php';

    session_start();
    error_log("+++++++++++++ Done initilizing client: 1", 0);

      $obj = new appCred(); 
      $client = new Google_Client();
      $client->setClientId($obj->getClientId());
      $client->setClientSecret($obj->getClientSecret());
      $client->setRedirectUri($obj->getRedirectUrl());
      $client->setApplicationName('organise');
      $client->setScopes("http://www.google.com/m8/feeds/ https://www.googleapis.com/auth/calendar.readonly");
      //$authUrl = $client->createAuthUrl();
      
       
      error_log("+++++++++++++ Done initilizing client: ", 0);

        if (isset($_GET['code'])) {
          $client->authenticate($_GET['code']);
          $_SESSION['token'] = $client->getAccessToken();
          $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
          header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
          error_log("+++++++++++++ Got code: ", 0);
         
        }

      if (isset($_SESSION['token'])) {
        $client->setAccessToken($_SESSION['token']);
        error_log("+++++++++++++ Set session: ", 0);
      }

      if (isset($_REQUEST['logout'])) {
        unset($_SESSION['token']);
        $client->revokeToken();
        error_log("+++++++++++++ logging out: ", 0);
      } 

      if ($client->getAccessToken()) {
        /*$calendarService = new Google_Service_Calendar($client);
        $calendar = $calendarService->calendars->get('primary');
        $events = $calendarService->events->listEvents('primary');
        foreach ($events->getItems() as $event) {
           echo $event->getSummary();
        }*/
        //echo $calendar->getSUmmary();
        $request = new Google_Http_Request("https://www.google.com/m8/feeds/contacts/default/full?max-results=10000&alt=json");
        error_log("+++++++++++++ created google http request: ", 0);
        $val = $client->getAuth()->authenticatedRequest($request);
        $tempString = $val->getResponseBody();
        $contacts = json_decode($tempString,true);
        error_log("+++++++++++++ got response: ", 0);
        //var_dump($contacts);
  
          $name ='';
          $email='';
          $phNo='';
          $country = '';
          foreach($contacts['feed']['entry'] as $cnt) {
            if(isset($cnt['title']['$t'])){
              $name = $cnt['title']['$t'];
            }
            if(isset($cnt['gd$email']['0']['address'])){
              $email = $cnt['gd$email']['0']['address'];
            }
            if(isset($cnt['gd$phoneNumber'])) {
              $phNo = $cnt['gd$phoneNumber'][0]['$t'];
            }
            
          print "<tr>
                  <td>'$name'/</td>
                  <td>'$email'</td> 
                  <td>'$phNo'</td>
                </tr>";
       }
  
        
        $_SESSION['token'] = $client->getAccessToken();
      } 
      else {
        $auth = $client->createAuthUrl();
      }

if (isset($auth)) {
  print "<br><br><br><br>
         <div align =\"center\">
              <a href='$auth' class=\"btn btn-success\" >Connect Me</a>
          </div>
        <br><br><br><br><br><br>";
  } 
else {
  print "<div align =\"center\"><a class=\"btn btn-success\" href='?logout'>Logout</a></div>";
  //print "<div align = \"center\"><a class=\"btn btn-info\" href=\"http://organise-inmyway.rhcloud.com/event.php\" >EVENTS</a></div>";
}

?>

</tbody>
</table>
</body>
</html>
