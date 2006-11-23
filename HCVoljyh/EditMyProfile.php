<?php
include "lib/dbaccess.php" ;
require_once "lib/FunctionsTools.php" ;
require_once "lib/FunctionsLogin.php" ;
require_once "layout/Error.php" ;

	if (!isset($_SESSION['IdMember'])) {
	  $errcode="ErrorMustBeIndentified" ;
	  DisplayError(ww($errcode)) ;
		exit(0) ;
	}


// Find parameters
	$IdMember=$_SESSION['IdMember'] ;

	if (IsAdmin()) { // admin can alter other profiles
    if (isset($_POST['cid'])) {
      $IdMember=$_POST['cid'] ;
    }
    if (isset($_GET['cid'])) {
      $IdMember=$_GET['cid'] ;
    }
	}

// manage picture photorank (swithing from one picture to the other)
  $photorank=0 ;
  if (isset($_POST['photorank'])) {
    $photorank=$_POST['photorank'] ;
  }
  if (isset($_POST['action'])) {
    $action=$_POST['action'] ;
  }
	
  $TGroups=array() ;
// Try to load groups and caracteristics where the member belong to
  $str="select membersgroups.id as id,membersgroups.Comment as Comment,groups.Name as Name from groups,membersgroups where membersgroups.IdGroup=groups.id and membersgroups.Status='In' and membersgroups.IdMember=".$IdMember ;
	$qry=mysql_query($str) or dier("error=".$str) ;
	$TGroups=array() ;
	while ($rr=mysql_fetch_object($qry)) {
	  array_push($TGroups,$rr) ;
	}
	
	
  switch($action) {
	  case "update" :
		  
		  $rr=LoadRow("select * from members where id=".$IdMember) ;
		  $str="update members set ProfileSummary=".ReplaceInMTrad($_POST['ProfileSummary'],$m->ProfileSummary) ;
		  $str.=",AdditionalAccomodationInfo=".ReplaceInMTrad($_POST['AdditionalAccomodationInfo'],$m->AdditionalAccomodationInfo) ;
			$str.=",Accomodation='".$_POST['Accomodation']."'" ;
		  $str.=",Organizations=".ReplaceInMTrad($_POST['Organizations'],$m->Organizations) ;
			$str.=" where id=".$IdMember ;
	    mysql_query($str) or die("<br>".$str."<br>problem updating profile") ;
			
			// updates groups
			$max=count($TGroups) ;
			for ($ii=0;$ii<$max;$ii++) {
			  $ss=$_POST["Group_".$TGroups[$ii]->Name] ;
			  $IdTrad=ReplaceInMTrad($ss,$TGroups["$ii"]->Comment) ;
				if ($IdTrad!=$TGroups["$ii"]->Comment) {
				  mysql_query("update membersgroups set Comment=".$IdTrad." where id=".$TGroups["$ii"]->id) ;
				}
			}
			
			
			if ($IdMember==$_SESSION['IdMember']) LogStr("Profil update by member himself","Profil update") ;
			else LogStr("update of another profil","Profil update") ;
			break ;
	  case "logout" :
		  Logout("Main.php") ;
			exit(0) ;
	}
	

	$wherestatus=" and Status='Active'" ;
	if (HasRight("Accepter")) {  // accepter right allow for reading member who are not yet active
	  $wherestatus="" ;
	}
// Try to load the member
	if (is_numeric($IdMember)) {
	  $str="select * from members where id=".$IdMember.$wherestatus ;
	}
	else {
		$str="select * from members where Username='".$IdMember."'".$wherestatus ;
	}

	$m=LoadRow($str) ;

	if (!isset($m->id)) {
	  $errcode="ErrorNoSuchMember" ;
	  DisplayError(ww($errcode,$IdMember)) ;
//		die("ErrorMessage=".$ErrorMessage) ;
		exit(0) ;
	}

	$IdMember=$m->id ; // to be sure to have a numeric ID
	
	$profilewarning="" ;
	if ($m->Status!="Active") {
	  $profilewarning="WARNING the status of ".$m->Username." is set to ".$m->Status ;
	} 

	$photo="" ;
	$phototext="" ;
	$str="select * from membersphotos where IdMember=".$IdMember." and SortOrder=".$photorank ;
	$rr=LoadRow($str) ;
	if (!isset($rr->FilePath)and ($photorank>0)) {
	  $rr=LoadRow("select * from membersphotos where IdMember=".$IdMember." and SortOrder=0") ;
	}
	
	if ($m->IdCity>0) {
	   $rWhere=LoadRow("select cities.Name as cityname,regions.Name as regionname,countries.Name as countryname from cities,countries,regions where cities.IdRegion=regions.id and countries.id=regions.IdCountry and cities.id=".$m->IdCity) ;
	}
	
	
	if (isset($rr->FilePath)) {
	  $photo=$rr->FilePath ;
	  $phototext=FindTrad($rr->Comment) ;
		$photorank=$rr->SortOrder;
	} 

  include "layout/EditMyProfile.php" ;
  DisplayEditMyProfile($m,$photo,$phototext,$photorank,$rWhere->cityname,$rWhere->regionname,$rWhere->countryname,$profilewarning,$TGroups) ;

?>
