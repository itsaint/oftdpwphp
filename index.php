<!DOCTYPYE html>
<html>
<?php
require_once('OftIDGen.php');
session_start();
$attempt = 0;
if(isset($_POST['tries'])){
  $attempt = (int)$_POST['tries'];
}
$action = null;  // like: login logout add_instr edit_dateRange
if(isset($_POST['id'])){
  $action = $_POST['id']; 
}
if(isset($_SESSION['CID'])){ // authenticated
  if("Logout" == $action){
    session_unset();
    setcookie("CID","",time()-3600);
    $_COOKIE = [];
    $_SERVER['HTTP_COOKIE'] = [];
    header("Location: index.php");
  }
?>
<form method="post">
  <input type="submit" name="id" value="Logout" />
</form>
<?php
  echo "hello ".$_SESSION['CID']."<br />".PHP_EOL;
  echo "action = ".$action."<br />";

}  //END if(isset($_SESSION['CID'])){ // authenticated
else{  // not authenticated yet
  if('Proceed' == $action ){  // login form submitted
    if(!empty($_POST['CID'])){ // try login with the CID provided
      if(!authenticate($_POST['CID'])){ // failed
        show_login_form($attempt+1,$_POST['remember_me']);
      } // END       if(!authenticate($_POST['CID'])){ // failed
      else{ // login with the submitted id 
        login($_POST['CID']);
      } // END else{ // login with the submitted id 
    } // END     if(!empty($_POST['CID'])){ // try login with the CID provided
    else{ // 
      login(null);
    } 
  } // END  if('login' == $action ){  // login form submitted
  else{ //first page
    //try to authenticate with cookie
    if(isset($_COOKIE['CID']) && authenticate($_COOKIE['CID'])){
      //if(authenticate($_COOKIE['CID'])){
        login( $_COOKIE['CID'] );
    }
    else{  //show login form
      show_login_form(-1, true);
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
function login($cid){
  if(empty($cid)){
    $cid = OftIDGen::get_new_id();
  }
  $_SESSION['CID'] = $cid;
  if( $_POST['remember_me'] == true){
    setcookie('CID',$cid);
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
        If you have your 6 digit Client ID: <input type="text" name="CID" /><br />
        Or leave it blank to generate a new id.<br />
<?php
        if($trial > 0 ){
          echo "Sorry incorrect client id <br />";
        }
?>
        Remember me on this computer(using cookie):
        <input type="checkbox" name="remember_me" <?php echo ($use_cookie ? "checked" : ""); ?>/><br />
        <input type="submit" name="id" value="Proceed"/>
      </form>
     </html>
<?php
}

?>
