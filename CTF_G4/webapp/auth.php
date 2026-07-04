<?php
// GrandMaster Analysis Portal - authentication helper.
// Member credentials are stored as md5crypt ($1$) hashes, mirroring the
// portal_db export. Login verification uses crypt() against the stored hash.

session_start();

$PORTAL_USERS = [
    // username => md5crypt hash   (password set during the 2024 migration)
    "admin"      => '$1$grandmst$cg6Occ8dI24JR1.qq.V07/',
    "capablanca" => '$1$capablan$nn8AwYrrX4v.bH3N05sKJ0',
    "alekhine"   => '$1$alekhine$qNZ0Y2GBPSFLoxbU.n2Wl1',
    "tal"        => '$1$tal$LhpWRqTke1u9gY1CHRuY//',
    "fischer"    => '$1$fischer$ZstFrQjGJWpgRa6hNoZdD/',
    "karpov"     => '$1$karpov$Vrf7vusRu.ws/E1hy3Dk/0',
    "kasparov"   => '$1$kasparov$F.S9X7./H9FzI1av6vB/g1',
    "kramnik"    => '$1$kramnik$4HNdMu0ks2a/PiOcVy3Cn1',
    "anand"      => '$1$anand$95PpKoNCUQ7UAT55Ir6ZG1',
];

function verify_login($user, $pass) {
    global $PORTAL_USERS;
    if (!isset($PORTAL_USERS[$user])) {
        return false;
    }
    $stored = $PORTAL_USERS[$user];
    return hash_equals($stored, crypt($pass, $stored));
}

function require_login() {
    if (empty($_SESSION["user"])) {
        header("Location: login.php");
        exit;
    }
}
?>
