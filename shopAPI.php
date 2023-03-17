<?php
require_once("vendor/autoload.php");


$MySQLHandler = new MySQLHandler("products");
$MySQLHandler->connect();
   // $response = $MySQLHandler->get_record_by_id(8);
   // var_dump($response);
   // echo json_encode($response);

   if(!$MySQLHandler){
      $response=["error","internal server error"];
   }else{
      $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
      $url = explode('/', $url);
      if (isset($url[5])) {
        if($url[5]!=='items'){
            http_response_code(404);
            exit();
         }
    }

      $itemId  = null;
    if (isset($url[6])) {
        $itemId = (int)$url[6];
    }
      switch ($_SERVER["REQUEST_METHOD"]) {
        //#region GET
          case 'GET':
            if ($itemId) {
                if (!$MySQLHandler->search('id', $itemId)) {
                    http_response_code(404);
                    $response = ["error" => "Resource doesn't exist"];
                } else {
                    $MySQLHandler->connect();
                    $response = $MySQLHandler->get_record_by_id($itemId);
                }
            } else {
                $response = $MySQLHandler->get_data();
            };
           
              break;
              //#endregion
              //#region POST
          case 'POST':
            $data = file_get_contents('php://input');
            if ($data) {
                $post = json_decode($data, true);
                $response = $MySQLHandler->save($post);
            }else{
                $response = ["error" => "item doesn't added"];
            }
            break;
            //#endregion
            //#region PUT
          case 'PUT':
              if ($itemId) {
                     $put = json_decode(file_get_contents('php://input'), true);
                      $response = $MySQLHandler->update($put, $itemId);
                      $MySQLHandler->connect();
                  if (!$MySQLHandler->search('id', $itemId)) {
                    http_response_code(404);
                      $response = ["error" => "Resource doesn't exist"];
                  }
              }
              break;
              //#endregion
              //  #region DELETE
          case 'DELETE':
              if ($itemId) {
                  if (!$MySQLHandler->search('id', $itemId)) {
                    http_response_code(404);
                      $response = ["error" => "Resource doesn't exist"];
                  } else {
                    $MySQLHandler->connect();
                      $response = $MySQLHandler->delete($itemId);
                  }
              }
              break;
              //#endregion
              default:
              http_response_code(405);
              $response = ["error" => "method not allowed!"];
              break;
      }
   echo json_encode($response);
   }

?>