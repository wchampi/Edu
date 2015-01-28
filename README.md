# Edu #

## Db Manager: ##

Examples:

#### Ini Connection: ####

<pre>
$db = Edu_Db_Manager::getInstance('default', 'host', 'dbname', 'user', 'password');
</pre>

#### Get Connection: ####

<pre>
$db = Edu_Db_Manager::getInstance('default');
</pre>

#### Insert: ####
<pre>
$idMovie = $db->insert('movie', array(
    'link' => "http://example.com",
    'name' => "Avengers",
    'date_create' => date('Y-m-d H:i:s')
));
</pre>
#### Update: ####
<pre>
$db->update('movie', array(
    'link' => "http://example2.com",
    'name' => "Avengers 2"
), 'id = ?', $idMovie);
</pre>
#### FetchAll: ####
<pre>
$query = "SELECT * "
    . " FROM movie a";
$movies = $db->fetchAll($query, $idMovie); // array(array('id' => 1, 'name' => 'Avenger'), array('id' => 2, 'name' => 'Hobbit'));
</pre>
#### FetchOneBy: ####
<pre>
$movie = $db->fetchOneBy('movie', 'id = ?', $idMovie); // array('id' => 1, 'name' => 'Avenger');
</pre>
#### queryKeyVal: ####
<pre>
$query = "SELECT id, name"
    . " FROM movie a";
$movie = $db->queryKeyVal($query);  // array('1' => 'Avenger', '2' => 'Hobbit');
</pre>
