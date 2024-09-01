<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$domain = get_required(domain);

$ids = ["Ashwood", "Asksvin bladder", "Asksvin egg", "Asksvin hide", "Asksvin neck", "Asksvin pelvis", "Asksvin ribcage", "Asksvin skull", "Asksvin tail", "Barber kit", "Barley", "Barley flour", "Bell fragment", "Bell fragment", "Berries", "Berries", "Bilebag", "Black core", "Black core", "Black marble", "Black marble", "Blood clot", "Bloodbag", "Bloodstone", "Blue jute", "Blueberries", "Blueberries", "Boar meat", "Bone fragments", "Bonemaw meat", "Bonemaw tooth", "Branch", "Branch", "Bread", "Bronze nails", "Carapace", "Carrot", "Celestial feather", "Ceramic plate", "Chain", "Charcoal resin", "Charred bone", "Charred cogwheel", "Charred skull", "Chicken meat", "Chitin", "Cloudberries", "Coal", "Cooked chicken meat", "Cooked fish", "Cooked lox meat", "Cooked serpent meat", "Crystal", "Dandelion", "Deer hide", "Deer meat", "Deer trophy", "Dragon tear", "Drake trophy", "Draugr Elite trophy", "Dvergr extractor", "Egg", "Entrails", "Feathers", "Fenris claw", "Fenris hair", "Fish n' bread", "Flax", "Flint", "Freeze gland", "Category:Gems", "Grausten", "Greydwarf eye", "Guck", "Hard antler", "Hare meat", "Honey", "Honey glazed chicken", "Honey glazed chicken", "Iolite", "Iron nails", "Iron pit", "Jade", "Jotun puffs", "Jotun puffs", "Leather scraps", "Linen thread", "Lox meat", "Lox meat pie", "Lox meat pie", "Lox pelt", "Magecap", "Magecap", "Majestic carapace", "Mandible", "Meat platter", "Mechanical spring", "Misthare supreme", "Molten core", "Morgen heart", "Morgen sinew", "Mushroom", "Neck tail", "Needle", "Needle", "Obsidian", "Ooze", "Piquant pie", "Pot shard", "Proustite powder", "Queen bee", "Raspberries", "Raspberries", "Raw fish", "Red jute", "Refined eitr", "Resin", "Category:Resource nodes", "Roasted crust pie", "Root (item)", "Root (item)", "Royal jelly", "Sap", "Scale hide", "Sealbreaker fragment", "Sealbreaker fragment", "Seeker meat", "Serpent meat", "Serpent scale", "Sharpening stone", "Shield core", "Soft tissue", "Soft tissue", "Stone", "Stuffed mushroom", "Sulfur", "Surtling core", "Tar", "Tar", "Thistle", "Thunder stone", "Torn spirit", "Troll hide", "Turnip", "Ulv trophy", "Ulv trophy.png", "File:Ulv trophy.png", "Volture egg", "Volture meat", "Wisp", "Wolf fang", "Wolf meat", "Wolf pelt", "Wolf trophy", "Wood", "Yellow mushroom", "Ymir flesh"];

$dom = new DOMDocument;
libxml_use_internal_errors(true);
$dom->loadHTMLFile("https://valheim.fandom.com/wiki/$domain");
$props = $dom->getElementsByTagName('aside');

function norm($res)
{
    return str_replace(["\n", "\t"], "", $res);
}

function printNode($node)
{
    if ($node->hasChildNodes()) {
        $arr = [];
        foreach ($node->childNodes as $child) {
            $res = printNode($child);
            if (is_string($res)) {
                $res = str_replace(["\n", "\t"], "", $res);
                if ($res != "") {
                    $arr[] = $res;
                }
            } else if (sizeof($res) == 1) {
                $arr[] = $res[0];
            } else if (sizeof($res) == 2) {
                if ($res[0] == "Crafting Materials") {
                    $line = norm($child->textContent);
                    $line = str_replace("Crafting Materials", "", $line);
                    $arr[$res[0]] = $line;
                } else if ($res[0] == "Usage") {
                    $arr[$res[0]] = explode(", ", $res[1]);
                } else if ($res[0] == "Dropped by") {
                    $line = norm($child->textContent);
                    $arr[$res[0]] = explode(", ", $line);
                } else if (sizeof($res[0]) == 1 && sizeof($res[1]) == 1) {
                    $arr[$res[0]] = $res[1];
                } else if (norm($child->childNodes[1]->textContent) == WeightStack) {
                    $arr[norm($child->childNodes[1]->childNodes[1]->childNodes[1]->textContent)] =
                        norm($child->childNodes[3]->childNodes[1]->childNodes[1]->textContent);
                    $arr[norm($child->childNodes[1]->childNodes[1]->childNodes[3]->textContent)] =
                        norm($child->childNodes[3]->childNodes[1]->childNodes[3]->textContent);
                } else {
                    $arr[] = $res;
                }
            }  else {
                $arr[] = $res;
            }
        }
        return $arr;
    } else {
        return $node->textContent;
    }
}

$response = printNode($props[0]);


echo json_encode_readable($response);