<!DOCTYPYE html>
<html>
<?php
require_once('OftIDGen.php');
session_start();
$action = null;  // like: login logout add_instr edit_dateRange
extract($_POST);

if(isset($_SESSION['CID'])){ // authenticated
  if("Logout" == $action){
    session_unset();
    setcookie("CID","",time()-3600);
    $_COOKIE = [];
    $_SERVER['HTTP_COOKIE'] = [];
    header("Location: index.php");
  }
  echo "hello ".$_SESSION['CID']."<br />".PHP_EOL;
?>
<form method="post">
  <input type="submit" name="action" value="Logout" />
</form>
<?php

}  //END if(isset($_SESSION['CID'])){ // authenticated
else{  // not authenticated yet
  if('Proceed' == $action ){  // login form submitted
    if(!empty($cid)){ // try login with the cid provided
      if(!authenticate($cid)){ // failed
        show_login_form($tries+1,$remember_me);
      } // END       if(!authenticate($cid)){ // failed
      else{ // login with the submitted id 
        login($cid);
      } // END else{ // login with the submitted id 
    } // END     if(!empty($cid)){ // try login with the cid provided
    else{ // 
      login(null);
    } 
  } // END  if('Proceed' == $action ){  // login form submitted
  else{ //first page
    //try to authenticate with cookie
    if(isset($_COOKIE['CID']) && authenticate($_COOKIE['CID'])){
      login( $_COOKIE['CID'] );
    }
    else{  //show login form
      show_login_form(0, true);
    } //END else{  //show login form
  } //END   else{ //first page
} // END else{ // not authenticated yet
function int_val($str){
  return (int)$str;
}
function authenticate($cid){
  $sids = array_map("int_val",file("cids.txt",FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
  return in_array($cid,$sids);     
}
function login($id){
  if(empty($id)){
    $id = OftIDGen::get_new_id();
  }
  $_SESSION['CID'] = $id;
  if( $remember_me == true){
    setcookie('CID',$id);
  }
  header("Location: index.php");
}
function show_login_form($trial,$use_cookie){
  if($trial > 2){
    login(null);
  }
  echo "tries = ".$trial."<br />";
?>
      <form method="post">
      
        <input type="hidden" name="tries" value="<?php echo $trial; ?>"/><br />
        If you have your 6 digit Client ID: <input type="text" name="cid" /><br />
        Or leave it blank to generate a new id.<br />
<?php
        if($trial > 0 ){
          echo "Sorry incorrect client id <br />";
        }
?>
        Remember me on this computer(using cookie):
        <input type="checkbox" name="remember_me" <?php echo ($use_cookie ? "'checked'" : ""); ?>/><br />
        <input type="submit" name="action" value="Proceed"/>
      </form>
     </html>
<?php
}

?>
