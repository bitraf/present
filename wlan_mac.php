<?
include 'get_macaddr.php';

$mac_addresses = get_macaddr();

if (false === pg_connect("dbname=p2k12 user=p2k12"))
  echo ('PostgreSQL connect failed');

pg_query("SET TIME ZONE 'CET'");

//echo "Connected to database";

$first = true;
$i = 0;
$users = "";
foreach ($mac_addresses as $t)
{
  foreach ($t as $tt)
  {
    if (IsValid($tt) &&  preg_match('/[^a-zA-Z\d]/', $tt))
    {
      //ignore bitmart tablet
      if (strcmp("7C:6D:62:CF:75:9A", $tt) != 0)
      {
        //echo strtolower($tt) . "\n";
        $tt = strtolower($tt);
        $res = pg_query_params("SELECT account FROM mac WHERE macaddr=$1", array($tt));
        if (false === $res)
        {
          echo "PostgreSQL query error [0]";
          exit;
        }

        $account = pg_fetch_assoc($res);

        if ($account['account'] != NULL)
        {
          $res = pg_query_params("SELECT name FROM accounts WHERE id=$1", array($account['account']));

          $n = pg_fetch_assoc($res);
          if ($first == false)
            $users .= ", ";
          $users .= $n['name'];
          $first = false;
        }
      }
    }
  }
}

echo $users;
?>
