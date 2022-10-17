<?php

trait message
{
    public function send_message($num)
    {
        $this->send_message += $num;
        return $this;
    }
}

trait message1
{
    public function send_message1($num)
    {
        $this->send_message *= $num;
        return $this;
    }
}
trait message2
{
    public function send_message2()
    {



        return $this->send_message;
    }
}

class allmessage
{
    use message, message1, message2;
    protected $send_message = 0;
}



class info extends allmessage
{
    public function sets($usm, $names, $ids, $hashs, $pas)
    {
        $this->user = $usm;
        $this->name = $names;
        $this->id = $ids;
        $this->hash = $hashs;
        $this->password = $pas;








        return $this;
    }

    public function getform()
    {
        return $this->sets("xuser", "admin", sha1("1"), bin2hex(random_bytes(18)), sha1("mypass"));
    }




    private $name;
    private $id;
    private $user;
    private $hash;
    private $password;
}
$um = new info();
$um->send_message(10)->send_message1(5)->send_message2();
$um->getform();

echo "<pre>";
print_r($um);
echo "</pre>";
