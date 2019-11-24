<?php
    require_once("../../../config.php");

    require_once("../../../php/database.php");
    require_once("../../../php/user.php");

    session_start();

    if(!User::existsInstance() || !in_array(User::getInstance()->getType(), array(UserType::GM, UserType::Admin)))
        die(json_encode(array("error" => "Nejspíše vypršela session, přihlašte se znovu.", "success" => 0)));

    $type = $_POST['type'];

    if(is_null($type) || !is_numeric($type))
        die(json_encode(array("error" => "Typ je nevalidní", "success" => 0)));

    $webDB = new Database(DB_WEB);

    switch($type)
    {
        case 0: // delete
            {
                if(User::getInstance()->getType() != UserType::Admin)
                    die(json_encode(array("error" => "Nemáte oprávnění.", "success" => 0)));
                
                $changelog = $_POST["changelog"];
                
                if(is_null($changelog) || !is_numeric($changelog))
                    die(json_encode(array("error" => "Changelog je nevalidní", "success" => 0)));
                
                $webDB->delete("changelog", array("id" => $changelog));
            }
            break;
            
        case 1: // add
            {
                $content = $_POST["content"];
                $date    = $_POST["date"];
                
                if(is_null($date) || !is_string($date))
                    die(json_encode(array("error" => "Datum je nevalidní", "success" => 0)));
                
                if(is_null($content) || !is_string($content))
                    die(json_encode(array("error" => "Obsah je nevalidní", "success" => 0)));
                
                $webDB->insert("changelog", array("author" => User::getInstance()->getID(),
                                                  "content" => $content,
                                                  "date" => $date));
            }
            break;
            
        case 2: // edit
            {
                $content   = $_POST["content"];
                $date      = $_POST["date"];
                $changelog = $_POST["changelog"];
                
                if(is_null($changelog) || !is_numeric($changelog))
                    die(json_encode(array("error" => "Changelog je nevalidní", "success" => 0)));
                
                if(is_null($date) || !is_string($date))
                    die(json_encode(array("error" => "Datum je nevalidní", "success" => 0)));
                
                if(is_null($content) || !is_string($content))
                    die(json_encode(array("error" => "Obsah je nevalidní", "success" => 0)));
                
                $data = array("date" => $date,
                              "content" => $content);
                
                $webDB->update("changelog", $data, array("id" => $changelog));
            }
            break;
            
        case 3: // load
            {
                $id = $_POST["id"];
                
                if(is_null($id) || !is_numeric($id))
                    die(json_encode(array("error" => "Changelog je nevalidní", "success" => 0)));
                
                $query = $webDB->toArray($webDB->select("SELECT * FROM changelog WHERE id = :id", array(":id" => $id)))[0];
                
                echo json_encode(array("error" => "", "success" => 1, "changelog" => $query));
                
                return;
            }
            break;
            
        default:
            die(json_encode(array("error" => "Typ je mimo index", "success" => 0)));
            break;
    }

    echo json_encode(array("error" => "", "success" => 1));
?>