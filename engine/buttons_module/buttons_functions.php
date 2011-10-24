<?php
	function WriteAdmBox($curr, $count)
	{
		echo "<div class=\"adm_button_box\">";
		echo 	"<div class=\"func_updown_holder\">";
		echo		"<div class=\"up_func\"><a  href=\"engine/buttons_module/buttons_adm.php?curr=".$curr."&action=moveup\"></a></div>";
		echo 		"<div class=\"down_func\"><a href=\"engine/buttons_module/buttons_adm.php?curr=".$curr."&action=movedown\"></a></div>";
		echo 	"</div>";
		echo 	"<div class=\"remove_func\"><a href=\"engine/buttons_module/buttons_adm.php?curr=".$curr."&action=remove\"></a></div>";
		echo "</div>";
	}
	function PutHtmlButtons($ButtonData, $loggedIn, $ServiceID, $OnlyButtons)
	{
		$ServiceContent=$ButtonData[0]["button_content"];
		echo "<div id=\"buttons_holder\"><ul>";
		$counter=0;
		$currButton=0;
		foreach ($ButtonData as &$curr_button) 
		{
			if (!loggedIn)
				echo "<li><a href=\"?page=services&service_name=".$curr_button["name"]."\">".$curr_button["caption"]."</a>";
			else
				echo "<li><a href=\"?page=services&service_name=".$curr_button["name"]."&adm_mode=on\">".$curr_button["caption"]."</a>";
			if ($loggedIn && !$OnlyButtons)
			{
				WriteAdmBox($counter,count($ButtonData));
			}
			echo "</li>";
			if ($curr_button["name"]==$ServiceID)
			{
				$ServiceContent=$curr_button["button_content"];
				$ServiceCaption=$curr_button["caption"];
				$currButton=$counter;
			}
			$counter++;
		}unset($curr_button);unset($ServiceID);
		if ($loggedIn && !$OnlyButtons)
		{
			echo "<script type=\"text/javascript\" src=\"engine/tinymce/jscripts/tiny_mce/tiny_mce.js\"></script>";
			echo "<script type=\"text/javascript\">";
			echo 	"InitNewMce();";
			echo "</script>";
			echo "<li><a href=\"engine/buttons_module/buttons_adm.php?action=add\">Добавити нову</a></li>";
		}
		echo "</ul></div>";
		if (!$OnlyButtons)
		{
			echo "<div id=\"button_content\">";
			
			
			if ($loggedIn)
			{
				//echo "<div id=\"editable_header\">";
				echo "Введіть назву послуги, вона також буде назвою кнопочки:";
				echo "<input id=\"editable_header\" type=\"text\" value=\"";
			}
			else
			{
				echo "<div id=\"button_content_header\"><h2>";
			}
			echo 	$ServiceCaption;
			if ($loggedIn)
			{
				echo "\"></input>";
			}
			else
			{
				echo "</h2></div>";
			}
			echo "<div id=\"button_content_text\">";
			if ($loggedIn)
			{
				echo "Тут редагуйте текст послуги:";
				echo "<div id=\"editable_content\">";
			}
			echo 	$ServiceContent;
			if ($loggedIn)
			{
				echo "</div>";
			}
			echo "</div>";
			if ($loggedIn)
			{
				echo "<div id=\"buttons_savebtn\" onclick=\"SaveButtonContent('#editable',$currButton, tinyMCE);\">Зберегти зміни</div>";
			}
			echo "</div>";
		}
	}
	function MoveUpItem($mas, $n)
	{
		if($n!=0)
		{
			$buf = $mas[$n];
			$mas[$n]=$mas[$n-1];
			$mas[$n-1]=$buf;
		}
		else
		{
			echo "Sorry error occured.";
		}
		return $mas;
	}
	function MoveDownItem($mas, $n)
	{
		if($n!=count($mas)-1)
		{
			$buf = $mas[$n];
			$mas[$n]=$mas[$n+1];
			$mas[$n+1]=$buf;
		}
		else
		{
			echo "Sorry error occured.";
		}
		return $mas;
	}
	function SetContent($mas, $n, $content, $caption, $mysqlConnectionLinkID, $tableName)
	{
		$mas[$n]["button_content"]=$content;
		$mas[$n]["caption"]=$caption;
		mysql_query("DELETE FROM $tableName", $mysqlConnectionLinkID);
		for($i=0;$i<count($mas);$i++)
		{
			mysql_query("INSERT INTO $tableName (name, caption, button_content) VALUES('button".$i."', '".$mas[$i]["caption"]."', '".$mas[$i]["button_content"]."' ) ",$mysqlConnectionLinkID);
		}
	}
	function CreateNewElement($mas, $mysqlConnectionLinkID, $mysqlDBTableToStoreButtons, $mainURL, $tableName)
	{
		$N=count($mas);
		mysql_query("INSERT INTO $tableName (name, caption, button_content) VALUES('button".$N."', 'Замініть це на назву послуги.', 'Замініть це на опис послуги.' ) ",$mysqlConnectionLinkID);
		redirect($mainURL."?page=services&service_name=button".$N);
	}
	function RemoveElement($mas, $n, $mysqlConnectionLinkID, $mysqlDBTableToStoreButtons, $mainURL, $tableName)
	{
		mysql_query("DELETE FROM $tableName", $mysqlConnectionLinkID);
		for($i=0;$i<count($mas);$i++)
		{
			if ($i!=$n)
				mysql_query("INSERT INTO $tableName (name, caption, button_content) VALUES('button".$i."', '".$mas[$i]["caption"]."', '".$mas[$i]["button_content"]."' ) ",$mysqlConnectionLinkID);
		}
		redirect($mainURL."?page=services");
	}
	function UpdateDatabase($mas, $mysqlConnectionLinkID, $mysqlDBTableToStoreButtons, $mainURL, $tableName)
	{
		mysql_query("DELETE FROM $tableName", $mysqlConnectionLinkID);
		for($i=0;$i<count($mas);$i++)
		{
			mysql_query("INSERT INTO $tableName (name, caption, button_content) VALUES('button".$i."', '".$mas[$i]["caption"]."', '".$mas[$i]["button_content"]."' ) ",$mysqlConnectionLinkID);
		}
		redirect($mainURL."?page=services");
	}
?>