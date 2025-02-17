<?php
session_start();
session_unset();
session_destroy();
header("Location: /");
echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
exit;
?>