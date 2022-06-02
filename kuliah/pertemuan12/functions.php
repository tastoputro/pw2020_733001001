<?php
function koneksi()
{
    return mysqli_connect('localhost', 'root', '', 'laravel');
}

function query($query)
{
    $conn = koneksi();
    $result = mysqli_query($conn, $query);

    // jika hasilnya hanya 1
    if (mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function tambah($data)
{
    $conn = koneksi();

    $nama = htmlspecialchars($data['nama']);
    $nrp = htmlspecialchars($data['nrp']);
    $email = htmlspecialchars($data['email']);
    $jurusan = htmlspecialchars($data['jurusan']);
    $gambar = htmlspecialchars($data['gambar']);

    $query = "INSERT INTO mahasiswa VALUES (null,'$nama', '$nrp','$email','$jurusan','$gambar')";
    mysqli_query($conn, $query) or die(mysqli_error($conn));
    echo mysqli_error($conn);
    return mysqli_affected_Rows($conn);
}

function hapus($id)
{
    $conn = koneksi();
    mysqli_query($conn, "DELETE FROM mahasiswa WHERE ID=$id") or die(mysqli_error($conn));
    return mysqli_affected_Rows($conn);
}

function ubah($data)
{
    $conn = koneksi();

    $id = $data['id'];
    $nama = htmlspecialchars($data['nama']);
    $nrp = htmlspecialchars($data['nrp']);
    $email = htmlspecialchars($data['email']);
    $jurusan = htmlspecialchars($data['jurusan']);
    $gambar = htmlspecialchars($data['gambar']);

    $query = "UPDATE mahasiswa SET
    NAMA='$nama',
    NRP='$nrp',
    EMAIL='$email',
    JURUSAN='$jurusan',
    GAMBAR='$gambar' WHERE ID=$id";
    mysqli_query($conn, $query) or die(mysqli_error($conn));
    echo mysqli_error($conn);
    return mysqli_affected_Rows($conn);
}
function cari($keyword)
{
    $conn = koneksi();
    $query = "SELECT * FROM mahasiswa WHERE NAMA LIKE '%$keyword%'";
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}
function login($data)
{
    $username = htmlspecialchars($data['username']);
    $password = htmlspecialchars($data['password']);
    // cek dulu user nya
    if ($user = query("SELECT * FROM user WHERE username = '$username'")) {
        //cek password
        if (password_verify($password, $user['password'])) {
            // set session
            $_SESSION['login'] = true;

            header("Location: index.php");
            exit;
        }
    }
    return [
        'error' => true,
        'pesan' => 'Username / Password Salah!',
    ];

}
function registrasi($data)
{
    $conn = koneksi();
    $username = htmlspecialchars(strtolower($data['username']));
    $password1 = mysqli_real_escape_string($conn, $data['password1']);
    $password2 = mysqli_real_escape_string($conn, $data['password2']);

    // jika username / password kosong
    if (empty($username) || empty($password1) || empty($password2)) {
        echo "<script>
            alert('Username / Password tidak boleh kosong!');
            document.location.href = 'registrasi.php';
            </script>";
        return false;
    }
    // jika user sudah ada dalam database
    if (query("SELECT * FROM user WHERE username = '$username'")) {
        echo "<script>
            alert('Username sudah terdaftar!');
            document.location.href = 'registrasi.php';
            </script>";
        return false;
    }
    // jika konfirmasi tidak sesuai
    if ($password1 !== $password2) {
        echo "<script>
            alert('Konfirmasi password tidak sesuai!');
            document.location.href = 'registrasi.php';
            </script>";
        return false;
    }
    // jika password < 5 digit
    if (strlen($password1) < 5) {
        echo "<script>
            alert('Password terlalu pendek!');
            document.location.href = 'registrasi.php';
            </script>";
        return false;
    }
    //jika username dan password sesuai
    $password_baru = password_hash($password1, PASSWORD_DEFAULT);
    $query = "INSERT INTO user VALUES (null,'$username','$password_baru')";
    mysqli_query($conn, $query) or die(mysqli_error($conn));
    return mysqli_affected_rows($conn);

}
