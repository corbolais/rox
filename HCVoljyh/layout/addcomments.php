<?php
require_once("Menus_micha.php") ;
function DisplayAddComments($TCom,$Username,$IdMember) {
  global $title ;
	global $_SYSHCVOL ;
  $title=ww('AddComments') ;

  include "header_micha.php" ;
	
	Menu1("",ww('MainPage')) ; // Displays the top menu

	Menu2("addcomments.php.php",ww('AddComments')) ; // Displays the second menu


echo "\n<div id=\"maincontent\">\n" ;
echo "  <div id=\"topcontent\">" ;
echo "					<h3>",ww('commentsfor',$Username),"</h3>\n" ;
echo "\n  </div>\n" ;
echo "</div>\n" ;

echo "\n  <div id=\"columns\">\n" ;
echo "		<div id=\"columns-low\">\n" ;

ShowActions() ; // Show the Actions
ShowAds() ; // Show the Ads

echo "		<div id=\"columns-middle\">\n" ;
echo "			<div id=\"content\">\n" ;
echo "				<div class=\"info\">\n" ;

  echo "\n<center>\n" ;


  $ttLenght=array() ;
	if (isset($TCom->Quality)) { // if there allready a comment display it
	  echo "<table valign=center style=\"font-size:12;\">" ;
	  echo "<tr><th colspan=3>",$UserName,"</th>" ;
	  $color="black" ;
	  if ($TCom->Quality=="Good") {
		  $color="#808000" ;
		}
	  if ($TCom->Quality=="Bad") {
		  $color="red" ;
		}
    echo "<tr><td>" ;
    echo "<b>",$TCom->Commenter,"</b><br>" ;
    echo "<i>",$TCom->TextWhere,"</i>" ;
    echo "<br><font color=$color>",$TCom->TextFree,"</font>" ;
    echo "</td>" ;
		$ttLenght=explode(",",$TCom->Lenght) ;
		echo "<td width=\"30%\">" ;
		for ($jj=0;$jj<count($ttLenght);$jj++) {
		  echo ww("Comment_".$ttLenght[$jj]),"<br>" ;
		} 
		
		echo "</td>" ;
    echo "</table>\n" ;
	}
  
	
	
	echo "<br><br><form method=post>\n" ;
	echo "<table valign=center style=\"font-size:12;\">" ;
	$tt=$_SYSHCVOL['LenghtComments'] ;
	$max=count($tt) ;
	echo "<tr><td>",ww("CommentLength"),"</td><td>" ;
  echo "<table valign=center style=\"font-size:12;\">" ;
	for ($ii=0;$ii<$max;$ii++) {
	  echo "<tr><td>",ww("Comment_".$tt[$ii]),"</td>" ;
		echo "<td><input type=checkbox name=\"Comment_".$tt[$ii]."\"" ;
		if (in_array ($tt[$ii],$ttLenght)) echo " checked " ;
		echo "></td>" ;

	}
	echo "</table></td>" ;

	echo "<td>",ww("CommentLengthDescription",$Username,$Username,$Username),"</td>" ;
	echo "<tr><td colspan=3></td>" ;
  echo "<tr><td>",ww("CommentsWhere"),"</td><td><textarea name=TextWhere cols=70 rows=3></textarea></td><td>",ww("CommentsWhereDescription",$Username),"</td>" ;
  echo "<tr><td>",ww("CommentsCommenter"),"</td><td><textarea name=Commenter cols=70 rows=8></textarea></td><td>",ww("CommentsCommenterDescription",$Username),"</td>" ;

	echo "<tr><td>",ww("CommentQuality"),"</td><td>" ;
	echo "<select name=Quality>\n" ;
	echo "<option value=\"Neutral\" selected >" ; // by default
	echo ww("CommentQuality_Neutral"),"</option>\n" ;

	echo "<option value=\"Good\"" ;
	if ($TCom->Quality=="Good") echo " selected " ;
	echo ">",ww("CommentQuality_Good"),"</option>\n" ;

	echo "<option value=\"Bad\"" ;
	if ($TCom->Quality=="Bad") echo " selected " ;
	echo ">",ww("CommentQuality_Bad"),"</option>\n" ;
	echo "</selected>" ;
	echo "</td>" ;
	echo "<td>",ww("CommentQualityDescription",$Username,$Username,$Username),"</td>" ;
	echo "<tr><td align=center colspan=3><input type=hidden value=".$IdMember." name=cid><input type=hidden name=action value=add><input type=submit name=submit value=submit></td>" ;


	mysql_get_set("comments","Lenght") ;
	
// set('OnlyChatMail', 'OnlyOnce', 'hewasmyguest', 'hehostedme', 'Itrusthim', 'MoreThanAMonth', 'MoreThanAYear', 'IIntroduceHimToHospitality', 'HeIntroducemeToHospitality', 'HeIsMyFamily', 'HeHisMyOldCloseFriend', 'HeIsMyNeigbour')

	echo "\n</table>" ;
	echo "\n</form>" ;

  echo "</center>\n" ;

echo "\n         </div>\n"; // Class info 
echo "       </div>\n";  // content
echo "     </div>\n";  // columns-midle
	

echo "   </div>\n";  // columns-low
echo " </div>\n";  // columns

	
  include "footer.php" ;
}

?>
