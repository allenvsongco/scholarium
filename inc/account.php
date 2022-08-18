<?php
$x = '';

if (isset($new) && $new) {
    $hdr = 'Create an account';

    $arrNew = array(
        'username' => '',
        'first_name' => '',
        'middle_name' => '',
        'last_name' => '',
        'email' => ''
    );

    foreach ($arrNew as $k => $v) $$k = $v;

    $x .= populateForm($arrNew, 1);

} else {

    $con = SQL('edxapp');
    $rs  = $con->query("SELECT * FROM learner_profile WHERE id=" . $user);
    $r   = $rs->fetch_assoc();

    $x .= populateForm($r);

    $hdr = $r['username'];
}

function populateForm($arr, $new = 0) {
    $x = '';

    $arrCheckbox = array(
        'first_timer',
        'is_active',
        'is_employed',
        'is_admin',
        'status',
    );

    $arrTextarea= array(
        'home_address',
        'school_address',
        'work_address',
    );

    $arrAdmin = array(
        'id',
        'user_id',
        'created_on',
        'modified_on',
        'first_timer',
        'is_active',
        'is_admin',
        'status',
    );

    foreach ($arr as $k => $v) {
        $$k = $v;
        $readonly = ($k == 'id' ? READ_ONLY : '');

        switch ($k) {
            case 'modified_on':
                $x .= '<tr><input type="hidden" name="' . $k . '" value="' . date(TMDSET) . '" /></tr>';
                break;

            case 'email':
                $in = '<input type="email" name="' . $k . '" value="' . $v . '" ' . ($new ? 'required' : '') . ' />';
                break;

            case in_array($k, $arrCheckbox, true):
                $in = '<input type="hidden" name="' . $k . '" value=0 /><input type="checkbox" name="' . $k . '" value=1 ' . ($v ? CHECKED : '') . ' />';
                break;

            case in_array($k, $arrTextarea, true):
                $in = '<textarea name="' . $k . '" >' . $v . '</textarea>';
                break;

            default:
                $in = '<input type="text" name="' . $k . '" value="' . $v . '" ' . ($new ? 'required' : '') . ' ' . $readonly . ' />';
                break;
        }

        if ($k != 'password' && (USER_ISADMIN || (!in_array($k, $arrAdmin, true) && !USER_ISADMIN))) {
            $x .= '
                <tr>
                    <td><label>' . strtoupper(str_replace('_', ' ', $k)) . '</label></td>
                    <td>' . $in . '</td>
                </tr>
            ';
        }
    }

    return $x;
}
?>

<form method="post" class="account">
    <h3><?php echo $hdr; ?></h3>
    <hr>
    <table>

        <?php echo $x; ?>

        <tr>
            <td colspan=2>
                <hr>
            </td>
        </tr>
        <tr>
            <td><span class="bad"><?php echo $errmsg; ?></span></td>
            <td class="rt"><a href="<?php echo SCLR_ROOT . '/' . $_SESSION['login_type'] ?>" class="btn">Cancel</a> <input type="submit" value="Submit" /></td>
        </tr>
    </table>
</form>