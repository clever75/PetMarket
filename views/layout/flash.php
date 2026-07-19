<?php
if (!empty($_SESSION['flash_succes'])) {
    echo '<div class="flash flash-succes">' . htmlspecialchars($_SESSION['flash_succes']) . '</div>';
    unset($_SESSION['flash_succes']);
}
if (!empty($_SESSION['flash_erreur'])) {
    echo '<div class="flash flash-erreur">' . htmlspecialchars($_SESSION['flash_erreur']) . '</div>';
    unset($_SESSION['flash_erreur']);
}
?>