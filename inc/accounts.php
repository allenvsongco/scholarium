<?php
$x = '';

$data = authAPI('admin/users/list');

$x .= '<tr>';
$x .= '<th>Username</th>';
$x .= '<th>Name</th>';
$x .= '<th>Status</th>';
$x .= '</tr>';

if ($data) {
    foreach ($data as $user) {
        foreach ($user as $k => $v) {
            $$k = $v;
        }

        $x .= '<tr>';
        $x .= '<td><a href="?' . $id . '">' . $username . '</a></td>';
        $x .= '<td>' . ucwords($first_name . ' ' . $middle_name . ' ' . $last_name) . '</td>';
        $x .= '<td>' . ($status ? '<ion-icon name="checkmark-outline"></ion-icon>' : '') . '</td>';
        $x .= '</tr>';
    }

} else {
    echo '<div id="session-expired" class="ct">api error</div>';
}

unset($_SESSION['account_id']);
?>

<main>
    <ul>
        <li>
        </li>

        <li>
            <div class="account">
                <h3>Learner Accounts</h3>
                <hr>
                <table>

                    <?php echo $x; ?>

                </table>
            </div>
        </li>
    </ul>
</main>