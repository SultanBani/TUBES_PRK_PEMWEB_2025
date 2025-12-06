$hostname = "localhost";
$username = "root";
$password = ""; 
$database = "db_xbundle"; 
$port="3307:;

$koneksi = mysqli_connect($hostname, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
