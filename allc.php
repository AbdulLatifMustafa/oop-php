<style>
img {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 5px;
  width: 150px;
}
</style>
<?php
spl_autoload_register(

    function ($c) {



        require 'classes/' .$c.'.php';
    }




);







$r = new c();
$r->printc();
$r->admin("body");
$r->mail="@S$";
$r->pass(":");
$r->photo="<img src='https://cdn-icons-png.flaticon.com/512/2206/2206368.png'>";
$r->hash=bin2hex(random_bytes(18));


$r1 = new c1();
$r1->printc1();
$r1->user="ahmed";
$r1->pass("f'f");
$r1->mail="sss$@@";
$r1->hash=bin2hex(random_bytes(18));
$r1->photo="<img src='https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/480px-User_icon_2.svg.png'>";



$r2 = new c2();
$r2->printc2();
$r2->user="nader";
$r2->pass("fsdf");
$r2->mail="ffsss$@@";
$r2->hash=bin2hex(random_bytes(18));
$r2->photo="<img src='https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/480px-User_icon_2.svg.png'>";


echo "<pre>";
print_r($r);

echo "</pre>";
echo "<pre>";
print_r($r1);

echo "</pre>";

echo "<pre>";
print_r($r2);

echo "</pre>";



