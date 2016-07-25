<?php
namespace JoranBeaufort\Neo4jUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use JoranBeaufort\Neo4jUserBundle\Form\UserType;
use JoranBeaufort\Neo4jUserBundle\Entity\User;
use JoranBeaufort\Neo4jUserBundle\Entity\Role;
use AppBundle\Entity\Resources;
use AppBundle\Entity\UserResource;

class TestController extends Controller
{
    
     public function testAction()
    { 

        $usernameInput = 'test';
        $resourceInput = 'stone';
        
        $em = $this->get('neo4j.graph_manager')->getClient();
        
        echo 'Loading user with Username: '.$usernameInput.'<br>';        
        $user=$em->getRepository(User::class)->findOneBy('username', $usernameInput);
        if($user->getUsername() == $usernameInput){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Get roles of user:<br>';        
        if($user->getRoles()){
            print_r($user->getRoles());
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Loading resource with type: '.$resourceInput.'<br>';        
        $resource=$em->getRepository(Resources::class)->findOneBy('resourceType', $resourceInput);         
        if($resource->getResourceType() == $resourceInput){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Adding Resource to User<br>';        
        $user->addResource($resource, 15);
        echo '<p style="color:green">OK</p><br>';
        
            
        echo 'Persisting the User<br>';        
        $em->persist($user);
	$em->persist($resource);
        echo '<p style="color:green">OK</p><br>';
        
        echo 'Flush<br>';        
        $em->flush();
        echo '<p style="color:green">OK</p><br>';
        
        echo 'Clear EM<br>';        
        $em->clear();
        echo '<p style="color:green">OK</p><br>';
        
        
	echo 'Reloading user with Username: '.$usernameInput.'<br>';        
        $user=$em->getRepository(User::class)->findOneBy('username', $usernameInput);
        if($user->getUsername() == $usernameInput){
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        echo 'Checking is user <b>'.$usernameInput.'</b> has attached resource of type <b>'.$resourceInput.'</b><br>';        
	echo $usernameInput . PHP_EOL;
	foreach ($user->getUserResources() as $userResource) { echo $userResource->getResource()->getName() . PHP_EOL; }
        if($user->getUserResource($resourceInput)){
            print($user->getUserResource($resourceInput)->getResourceType);            
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        
        echo 'Check if role is connected and still has properties<br>'; 
        if($user->getRoles()){ 
            print_r($user->getRoles());
            echo '<p style="color:green">OK</p><br>';
        }else{
            echo '<p style="color:red">NOT OK</p><br>';
        }
        
        // Recreate original state in DB:
       // $em->getDatabaseDriver()->run("match (n)-[r]-() where not n:Resources and not n:Team and not n:User delete r,n");
       // $em->getDatabaseDriver()->run("match (n:User{username:'test'}) create (m:Role{roleType:'ROLE_USER'}), (n)-[r:HAS_ROLE]->(m)");
        
        die;
        return false;
    }

}
