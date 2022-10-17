<?php
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
public function getform()
{
    return $this->sets("xuser","admin",sha1("1"),bin2hex(random_bytes(18)),sha1("mypass"));
}
private $name;
private $id;
private $user;
private $hash;
private $password;
}
$um=new info();
$um->getform();
echo"<pre>";
print_r($um) ;
echo"</pre>";







