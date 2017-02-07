<?php

// load some lib
require_once 'vendor/autoload.php';
require_once 'library/NotORM.php';

// instance of object
$app = new \Slim\app;

// db config
$dbhost = 'localhost';
$dbuser = 'admin';
$dbpass = 'admin';
$dbname = 'jualbeli';
$dbfunc = 'mysql:dbname=';
$dsn    = $dbfunc . $dbname;
$pdo    = new PDO($dsn, $dbuser, $dbpass);
$db     = new NotORM($pdo);

// simple route
$app->get('/', function () {
    echo "hello slim programmer";
});

// get all data
$app->get('/getallproduk', function () use ($app, $db) {
    foreach ($db->produk() as $value) {
        $produk['allproduk'][] = [
            'id'    => $value['id'],
            'nama'  => $value['nama'],
            'harga' => $value['harga'],
        ];
    }
    echo json_encode($produk);
});

// get by attr
$app->get('/getproduk/{id}', function ($request, $response, $args) use ($app, $db) {
    $produk = $db->produk()->where('id', $args['id']);
    if ($data = $produk->fetch()) {
        $resp = [
            'id'    => $data['id'],
            'nama'  => $data['nama'],
            'harga' => $data['harga'],
        ];
    } else {
        $resp = [
            'status'  => false,
            'message' => 'Data tidak ditemukan',
        ];
    }
    echo json_encode($resp);
});

// post to db
$app->post('/storeproduk', function ($request, $response, $args) use ($app, $db) {
    $produk = $request->getParams();
    $result = $db->produk->insert($produk);
    $resp   = [
        'status' => (bool) $result,
    ];
    echo json_encode($resp);
});

// put to db
$app->put('/updateproduk/{id}', function ($request, $response, $args) use ($app, $db) {
    $produk = $db->produk->where('id', $args);
    if ($produk->fetch()) {
        $post   = $request->getParams();
        $result = $produk->update($post);
        $respon = [
            'status'  => (bool) $result,
            'message' => 'berhasil update produk',
        ];
    } else {
        $respon = [
            'status'  => (bool) $result,
            'message' => 'gagal update produk',
        ];
    }
    echo json_encode($respon);
});

// delete db
$app->delete('/deleteproduk/{id}', function ($request, $response, $args) use ($app, $db) {
    $produk = $db->produk()->where('id', $args);
    if ($produk->fetch()) {
        $result = $produk->delete();
        $ans    = [
            'status'  => true,
            'message' => 'Produk dihapus',
        ];
    } else {
        $ans = [
            'status'  => false,
            'message' => 'Produk tidak dihapus',
        ];
    }
    echo json_encode($ans);
});

// run app
$app->run();
