<?php 

$data = $_POST["RWSRequest"];
if ($data == "")
{
   $data = "<RWSRequest><Header><TimeStamp>2010-08-26 23:25:07</TimeStamp><Token>faw3!as</Token><TransactionID>12</TransactionID><Season>Winter2010</Season><Command>AddToMovieDatabase</Command></Header><Data><Movies><Movie><Title>Toy Story 3</Title><Director>d</Director><Year>y</Year><RunTime>r</RunTime><IMDBLink>i</IMDBLink><PosterLink>p</PosterLink><TrailerLink>t</TrailerLink><Freshness>f</Freshness><MetaScore>m</MetaScore><Genres><Genre>Animated</Genre><Genre>Adventure</Genre></Genres><Actors><Actor>Tom Hanks</Actor></Actors></Movie><Movie><Title>Iron Man 2</Title><Genres><Genre>Action</Genre><Genre>Superhero</Genre><Genre>Primetime</Genre></Genres><Actors><Actor>Robert Downey Jr.</Actor><Actor>Mickey Rourke</Actor><Actor>Gwyneth Paltrow</Actor></Actors></Movie></Movies><MovieLists><MovieList>Ballot</MovieList><MovieList>Miniclinathon</MovieList></MovieLists></Data></RWSRequest>";
}

$XmlNodes["root"]["RWSRequest"] = "node";
$XmlNodes["RWSRequest"]["Header"] = "node";
$XmlNodes["RWSRequest"]["Data"] = "node";
$XmlNodes["Header"]["TimeStamp"] = "leaf";
$XmlNodes["Header"]["Token"] = "leaf";
$XmlNodes["Header"]["TransactionID"] = "leaf";
$XmlNodes["Header"]["Season"] = "leaf";
$XmlNodes["Header"]["Command"] = "leaf";
$XmlNodes["Data"]["Movies"] = "list";
$XmlNodes["Data"]["MovieLists"] = "list";
$XmlNodes["Movies"]["Movie"] = "node";
$XmlNodes["MovieLists"]["MovieList"] = "leaf";
$XmlNodes["Movie"]["Title"] = "leaf";
$XmlNodes["Movie"]["Director"] = "leaf";
$XmlNodes["Movie"]["Year"] = "leaf";
$XmlNodes["Movie"]["RunTime"] = "leaf";
$XmlNodes["Movie"]["IMDBLink"] = "leaf";
$XmlNodes["Movie"]["PosterLink"] = "leaf";
$XmlNodes["Movie"]["TrailerLink"] = "leaf";
$XmlNodes["Movie"]["Freshness"] = "leaf";
$XmlNodes["Movie"]["MetaScore"] = "leaf";
$XmlNodes["Movie"]["Genres"] = "list";
$XmlNodes["Movie"]["Actors"] = "list";
$XmlNodes["Genres"]["Genre"] = "leaf";
$XmlNodes["Actors"]["Actor"] = "leaf";

$tree = array();
$tree["root"] = array();

$stack = array();
$stack[] = "root";

function startElement($parser, $name, $attrs) 
{
    global $stack;
    global $tree;
    global $XmlNodes;

    end($stack);
    $ActiveArray = &$tree;

    for ($i = 0; $i <= key($stack); $i++)
    {
        if ($XmlNodes[$stack[$i-2]][$stack[$i-1]] == "list")
        {
            end($ActiveArray);
            $ActiveArray = &$ActiveArray[key($ActiveArray)];
        }
        else
        {
            $ActiveArray = &$ActiveArray[$stack[$i]];
        }
    }

    $ListItem = ($XmlNodes[prev($stack)][end($stack)] == "list");
    $NodeType = $XmlNodes[end($stack)][$name];

    if ($NodeType == "")
    {
        echo "ERROR:  Node '" . $name . "' is not a valid child of '" . end($stack) . "'<BR>";
    }
    else if ($NodeType == "node")
    {
        if ($ListItem)
        {
            $ActiveArray[] = array();
        }
        else
        {
            $ActiveArray[$name] = array();
        }
    } 

    $stack[] = $name;        
}

function endElement($parser, $name) 
{
    global $stack;
    array_pop($stack);
}

function contents($parser, $data) 
{ 
    global $stack;
    global $tree;
    global $XmlNodes;
    
    end($stack);
    prev($stack);
    prev($stack);
    $ListItem = ($XmlNodes[current($stack)][next($stack)] == "list");
    end($stack);

    $ActiveArray = &$tree;

    for ($i = 0; $i < key($stack); $i++)
    {
        if ($XmlNodes[$stack[$i-2]][$stack[$i-1]] == "list")
        {
            end($ActiveArray);
            $ActiveArray = &$ActiveArray[key($ActiveArray)];
        }
        else
        {
            $ActiveArray = &$ActiveArray[$stack[$i]];
        }
    }
    
    if ($ListItem)
    {
        $ActiveArray[] = $data;
    }
    else
    {
        $ActiveArray[end($stack)] = $data;
    }
}

$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startElement", "endElement");
xml_set_character_data_handler($xml_parser, "contents");
xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);

if (!xml_parse($xml_parser, $data, true)) 
{ 
    die(sprintf("XML error: %s at line %d", 
                xml_error_string(xml_get_error_code($xml_parser)), 
                xml_get_current_line_number($xml_parser))); 
}
 
xml_parser_free($xml_parser);

echo $tree["root"]["RWSRequest"]["Data"]["Movies"][0]["Genres"][1] . "<BR><BR>";

echo "<pre>";
print_r($tree);
echo "</pre>";

?>