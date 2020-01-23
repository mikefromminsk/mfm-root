<?php
include_once "../db.php";
query("delete from coins");
query("delete from domains");
query("delete from domain_keys");