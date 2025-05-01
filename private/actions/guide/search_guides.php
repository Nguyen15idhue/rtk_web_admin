<?php
// chỉ redirect về fetch với param search
header('Location: fetch_guides.php?search=' . urlencode($_GET['q'] ?? ''));
