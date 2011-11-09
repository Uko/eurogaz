<?php
function itemThumbnailView($pathToThumbnail, $itemId, $itemTitle, $itemChildren, $urlBeginning = "?")
{//catalogue
	$result = 
			"
			<div class=\"catalogueItem\">
				<a class=\"catalogueItemPic\" href=\"{$urlBeginning}show=$itemId\">
					<img src=\"$pathToThumbnail\" alt=\"{$itemTitle}_thumbnail\"/>
				</a>
				<div class=\"linksContainer\">
				<a href=\"{$urlBeginning}show=$itemId\">$itemTitle</a>
				<ul>";
	foreach($itemChildren as $itemChild)
	{
		$result .= "<li><a href=\"{$urlBeginning}show={$itemChild["id"]}\">{$itemChild["name"]}</a></li>";
	}
	$result .= "
			</ul>
			</div>
			</div>";
	return $result;
}
function itemThumbnailInListView($pathToThumbnail, $itemId, $itemTitle, $itemPrice, $urlBeginning = "?")
{//catalogue
	$priceString = (($itemPrice == 0) ? "" : "<p>{$itemPrice} грн.</p>");
	$result = 
			"
			<td>
				<a href=\"{$urlBeginning}show=$itemId\">
					<img class=\"catalogueItemPic\" src=\"$pathToThumbnail\" alt=\"{$itemTitle}_thumbnail\"/>
				</a>
			</td>
			<td>
				<div class=\"catalogueItemTitle\"><a href=\"{$urlBeginning}show=$itemId\">$itemTitle</a></div>
			</td>
			<td>
				$priceString
				<a href=\"javascript:addItemToTheTrash('$itemId');\">Додати в кошик</a>
			</td>";
	return $result;
}
function itemView($itemId, $itemTitle, $itemImages, $itemFeatures, $itemDescription)
{//catalogue
	return "
		<div id=\"itemViewContainer\">
			<div id=\"itemImageContainer\">$itemImages</div>
			<div id=\"itemInfoContainer\">
				<div id=\"itemTitle\">$itemTitle</div>
				<div id=\"itemFeatures\">$itemFeatures</div>
				<a href=\"javascript:addItemToTheTrash('$itemId');\">Додати в кошик</a>
				<div id=\"itemDescription\">
						$itemDescription
				</div>
			</div>
		</div>";
}
function itemFeatureTemplate($featureTitle, $featureText)
{//catalogue
	return "<p><span>$featureTitle:</span> $featureText</p>";
}
function itemViewImage($itemTitle, $pathToImage, $pathToBigImage, $imageNumber)
{//catalogue
	return "<a rel=\"lightbox-$itemTitle\" href=\"$pathToBigImage\"><img src=\"$pathToImage\" alt=\"{$itemTitle}_image$imageNumber\"/></a>";
}
function groupView($items, $itemDescription)
{//catalogue
	return "
		<div id=\"groupViewContainer\">
			$items
		</div>
		<div id=\"itemDescriptionContainer\">
			<div id=\"itemDescription\">
				$itemDescription
			</div>
		</div>";
}
function breadcrumbsView($show, $title, $urlBeginning = "")
{//catalogue
	return "<a href=\"{$urlBeginning}show=$show\">$title</a> &gt;&gt; ";
}
function catalogueView($viewContainer, $pathToThisScript, $urlBeginning = "?")
{//catalogue
	return "
		<script type=\"text/javascript\" src=\"{$pathToThisScript}engine/catalogue.functions.js\"></script>
		<script type=\"text/javascript\" src=\"engine/functions.js\"></script>
		<script type=\"text/javascript\">
			$(document).ready
			(
				function()
				{
					insert_css('{$pathToThisScript}catalogue.css');
				}
			);
		</script>
		$viewContainer";
}
function addItemView($whereToSubmit, $itemData, $additionalInputsForTheItem = "",
		$runWhenDocumentIsLoaded = ";", $add_edit = "add")
{//admin
	$result =  "
		<form id=\"addItemForm\" action=\"$whereToSubmit\" method=\"post\" enctype=\"application/x-www-form-urlencoded\" accept-charset=\"UTF-8\">
			<div>
				<input type=\"hidden\" name=\"what\" value=\"$add_edit\"/>
				<input type=\"hidden\" name=\"type\" value=\"{$itemData["type"]}\" id=\"addItemForm_type\"/>
				";
	if($add_edit == "add" && isset($itemData["parent"]))
		$result .= "<input type=\"hidden\" name=\"parent\" value=\"{$itemData["parent"]}\"/>
				";
	if($add_edit == "edit" && isset($itemData["id"]))
		$result .= "<input type=\"hidden\" name=\"id\" value=\"{$itemData["id"]}\"/>
				";
				
	$result .=	"
				<br/>
				<table style=\"width: 80%; border: none; border-collapse:collapse; margin: 0px auto;\">
					<tr>
						<td style=\"width: 12%;\"><label for=\"name\">Ім'я:</label></td>
						<td style=\"width: 35%;\"><input type=\"text\" name=\"name\" value=\"{$itemData["name"]}\" style=\"width: 100%;\"/></td>
						<td style=\"width: 6%;\"></td>
						<td style=\"width: 12%;\"><label for=\"thumbnail\" id=\"addItemForm_thumbnail_label\">Іконка:</label></td>
						<td style=\"width: 35%;\"><input type=\"text\" name=\"thumbnail\" value=\"{$itemData["thumbnail"]}\" style=\"width: 100%;\" id=\"addItemForm_thumbnail\"/></td>
					</tr>
				</table>
				<table style=\"width: 80%; border: none; border-collapse:collapse; margin: 0px auto;\">
					<tr>
						<td style=\"width: 12%;\"><label for=\"description\">Опис:</label><td>
						<td style=\"width: 88%;\"><textarea name=\"description\" class=\"tinymceEditor\" style=\"width: 100%; height: 70px; margin: 0px; padding: 0px;\">{$itemData["description"]}</textarea></td>
					</tr>
				</table>
				$additionalInputsForTheItem
				<div style=\"width: 80%; clear: both; margin: 10px auto 0px auto;\">
					<input type=\"submit\" value=\"" . (($add_edit == "add") ? "Додати" : "Зберегти") . "\" style=\"width: 100%;\"/>
				</div>
			</div>
		</form>
		<script type=\"text/javascript\">
			$(document).ready
			(
				function()
				{
					$runWhenDocumentIsLoaded
					catalogueStartUpTinyMCE();
				}
			);
		</script>
		";
	return $result;
}
function groupSelectOptionView($val, $title, $html)
{//search
	if($title)
		$title = " title=\"" . $title . "\"";
	return "<option value=\"$val\"$title>$html</option>";
}
function groupSelectView($options)
{//search
	return "
	<select id=\"searchForm_groupList\" name=\"searchForm_groupList\" onchange=\"if(sval=\$('#searchForm_groupList').val())changeCollectionType(\$('#searchForm_groupList option[value=\''+sval+'\']').attr('title'));\">
		$options
	</select>";
}
function inputFeaturesBoxView($featViewList, $attrsTitle, $attrs)
{//search,admin
	return "
	<div id=\"features_vs_collections\" style=\"overflow: auto;\">
		<ul>
		$featViewList
		</ul>
		<div>
		<h3>$attrsTitle</h3>
		<ul>
		$attrs
		</ul>
		</div>
	</div>";
}
function inputFeatureView($id, $labelHtml, $value = "", $wrap = "")
{//search,admin
	global $PHP_EOL;
	if($value)
		$value = " value=\"" . $value . "\"";
	if($wrap)
		$wrap = array("<li class=\"" . $wrap . "\">", "</li>");
	else
		$wrap = array("<li>", "</li>");
	return $PHP_EOL . $wrap[0] . "<label for=\"$id\">" . $labelHtml . ":</label>
			<input type=\"text\" id=\"$id\" name=\"$id\"$value/><br/>" . $wrap[1];
}
function searchResultStringView($urlBeginningForResults, $id, $name, $thumbURL)
{//search
 return "<td><a href=\"{$urlBeginningForResults}show={$id}\" onmouseover=\"$(this).imgPopup({imgURL: '$thumbURL'});\">$name</a></td>";
}
function searchResultStringsBox($strings)
{
 return "<a href=\"#searchResults\"></a><table id=\"searchResults\">$strings</table>";
}
function attributeCheckBoxView($id, $val)
{//search,admin
	return "<li><input id=\"$id\" class=\"attributes\" type=\"checkbox\" name=\"attributes[]\" value=\"$val\" checked=\"checked\"/>" .
			"<label for=\"$id\">$val</label></li>";
}
function searchFormView($jsPath, $action, $groupSelect, $fillFeaturesBox)
{//search
	return "
	<script type=\"text/javascript\" src=\"$jsPath\"></script>
	<form id=\"searchForm\" action=\"$action#searchResults\" method=\"get\" enctype=\"application/x-www-form-urlencoded\" accept-charset=\"UTF-8\">
		<div>
			<input type=\"hidden\" name=\"page\" value=\"search\"/>
			<input type=\"hidden\" name=\"catalogue\" value=\"search\"/>
			$groupSelect
		</div>
		$fillFeaturesBox
		<div>
			<input type=\"submit\" value=\"Пошук\"/>
		</div>
	</form>";
}
function addPicturesBoxView($SAHC, $pathToUploader, $imagesFeatureView)
{//admin
	return "
		<br/>
		<div style=\"margin: 0px auto; width: 80%;\">
			<label for=\"imageInputType\">Як Ви бажаєте додати картинки:</label></td>
			<select id=\"imageInputType\" name=\"imageInputType\" onchange=\"changeImageInputType('$SAHC', '$pathToUploader');\">
				<option value=\"upload\">Завантажити</option>
				<option value=\"byHand\">Ввести самому</option>" .
				//<option value=\"chooseFromUploaded\">Вибрати з тих, що вже на сервері</option>
			"
			</select>
			<br/><br/>
			<div id=\"imagesInputContainer\">
					$imagesFeatureView
			</div>
			<div id=\"uploaderWorkContainer\">
				<div id=\"loading\">LOADING</div>
				<div id=\"uploaderWork\"></div>
			</div>
			<br/>
		</div>";
}
function collectionsChangerRadioBox($i, $val, $label, $checked = false)
{//admin
	if($checked)
		$checked = " checked=\"checked\"";
	return "
		<input id=\"collection_type_$i\" type=\"radio\" name=\"collection_type\" value=\"$val\" onchange=\"changeCollectionType()\"$checked>
		<label for=\"collection_type_$i\">$label</label>";
}
function collectionsTypesChangersBox($changers)
{//admin
	return "
		<p id=\"collectionsTypesChangers\">
			Тип колекції:
			$changers
		</p>";
}
function adminPanelView($url, $type, $whatTo, $allowGroupChildren = true, $allowItemChildren = true, $noRemove = false)
{//admin
	$out = "
		<div id=\"adminPanelContainer\">";
	if(!$noRemove)
		$out .= "
			<a href=\"javascript:removePageClick('$whatTo', '{$url}remove_item');\">Видалити $whatTo</a>";
	if($type != "item")
	{
		if($allowGroupChildren)
		{
			$out .= "
			<a href=\"{$url}add_group\">Додати сюди групу</a>";
		}
		if($allowItemChildren)
		{
			$out .= "
			<a href=\"{$url}add_item\">Додати сюди товар</a>";
		}
	}
	$out .= "
			<a href=\"{$url}edit_item\">Редагувати $whatTo</a>
		</div>";
	return $out;
}
?>
