<?php
$x = '';

$con = SQL('edxapp');
$rs  = $con->query("SELECT * FROM learner_profile");

$x .= '<tr>';
$x .= '<td>ID</td>';
$x .= '<td>Username</td>';
$x .= '<td>Name</td>';
$x .= '<td>Active</td>';

$x .= '</tr>';

while ($r = $rs->fetch_assoc()) {
    foreach ($r as $k => $v) {
        $$k = $v;
    }

    $x .= '<tr>';
    $x .= '<td><a href="?' . $id . '">' . $id . '</a></td>';
    $x .= '<td>' . $username . '</td>';
    $x .= '<td>' . $first_name . ' ' . $middle_name . ' ' . $last_name . '</td>';
    $x .= '<td>' . ($status ? '<ion-icon name="checkmark-outline"></ion-icon>' : '') . '</td>';

    $x .= '</tr>';
}

?>

<div class="account">
    <h3>Learner Accounts</h3>
    <hr>
    <table>

        <?php echo $x; ?>

    </table>
</div>