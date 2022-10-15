<?php



class c
{
  public function printc()
  {
    echo "from c"."<br>";;
  }


  public function admin($user)
  {

      $this->user=$user;
      if($user=="body")
      {
        echo"welcome admin $user". "<br>";
      }

  }

  public $user;
  public $mail;
  private $pass;

  public $photo;

  public $hash;

  



  public function pass($xpss)
  {

    $this->pass= sha1($xpss) ;
  }










}


