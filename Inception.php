<?





class info {






public function sets($usm,$names,$ids,$hashs,$pas)
{

 $this->user=$usm;
 $this->name=$names;
 $this->id=$ids;
 $this->hash=$hashs;
 $this->password=$pas;
 return $this;
}



private $name;
private $id;

private $user;

private $hash;

private $password;


}


$um=new info();


$um->sets("bodyboy","admin",sha1("1"),bin2hex(random_bytes(18)),sha1("mypass"));


echo"<pre>";
print_r($um);
echo"</pre>";

