<?php

use Phalcon\Mvc\Controller;
use Phalcon\Cache\Backend\Memcache;
use Phalcon\Cache\Frontend\Data as FrontData;



class SignupController extends Controller
{
    public function indexAction()
    {

    }

    public function registerAction()
    {
        $user = new Users();
        // Store and check for errors
        $success = $user->save(
            $this->request->getPost(),
            [
                "name",
                "email",
            ]
        );

        $name = $this->request->getPost()[name];
        $email = $this->request->getPost()[email];

        if ($success) {
            echo "Thanks for registering!";
        } else {
            echo "Sorry, the following problems were generated: ";

            $messages = $user->getMessages();

            foreach ($messages as $message) {
                echo $message->getMessage(), "<br/>";
            }
        }

        
        // Cache data for 900s
        $frontCache = new FrontData(
            [
              "lifetime" => 900,
            ]
        );
        
        // Create the Cache setting memcached connection options
        $cache = new Memcache(
            $frontCache,
                [
                    "host"       => "localhost",
                    "port"       => 11211,
                    "persistent" => false,
                ]
            );
    
        $cache->save("name", $name);
        $cache->save("email", $email);
    
        $data = $cache->get("name");
        $data = $cache->get("email");

        if($data == null) {
            echo "data not available in memcache";
        } else {
            echo "<br>";
            echo "<br>";
            echo "name: ";
            echo $name;
            echo "<br>";
            echo "email: ";
            echo $email;
        }

        $this->view->disable();
    }
}