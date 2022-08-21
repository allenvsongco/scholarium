<?php
define('BASE', $base);
define('TAB', isset($_GET['tab']) ? $_GET['tab'] : null);

switch (BASE) {
    case 'profile':
        $user = USER_ID;
        $hdr  = $_SESSION['login']['username'];
        break;

    case 'accounts':
        if (TAB == '') {
            $_SESSION['account_id'] = URI;
        }

        $user = $_SESSION['account_id'];
        $hdr  = 'Account: ' . $_SESSION['account_id'];
        break;
}

define('USER', $user);

$x = '';
$extra = '<input type="hidden" name="table" value=' . (TAB == '' ? 'profile' : TAB) . ' />';

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

    $ret  = (TAB != '' && TAB != 'profile') ? "j.*" : "p.*";
    $join = (TAB != '' && TAB != 'profile') ? "LEFT JOIN " . TAB . " j ON j.id=p.id" : '';

    $qry = "SELECT $ret
        FROM profile p
        $join
        WHERE p.id=" . USER;

    $con = SQL('scholarium');
    $rs  = $con->query($qry);

    if ($rs->num_rows > 0) {
        $r  = $rs->fetch_assoc();
        $x .= populateForm($r);

    } else {
        $r  = $rs->fetch_fields();
        $x .= populateForm($r, 0, 1);
    }
}

function populateForm($arr, $new = 0, $empty = 0) {
    $x = '';

    $arrAdmin = array(
        'created_on',
        'first_timer',
        'is_active',
        'partner_admin',
        'is_admin',
        'status',
    );

    foreach ($arr as $k => $v) {

        if ($empty) {
            foreach ($v as $a => $b) {
                $$a = $b;
                $in = prepInput($name, null, $new);
                $k  = $name;
            }

        } else {
            $$k = $v;
            $in = '';

            if ($k == 'id') {
                $x .= '<tr><input type="hidden" name="' . $k . '" value=' . USER . ' /></tr>';

            } elseif ($k == 'last_modified') {
                $x .= '<tr><input type="hidden" name="' . $k . '" value="' . $v . '" /></tr>';

            } else {
                $in = prepInput($k, $v, $new);
            }
        }

        if (!preg_match("/\bid\b|password|last_modified/i", $k) && (USER_ISADMIN || (!in_array($k, $arrAdmin, true) && !USER_ISADMIN))) {
            $x .= '
                <tr>
                    <td><label>' . ucwords(str_replace('_', ' ', $k)) . '</label></td>
                    <td>' . $in . '</td>
                </tr>
            ';
        }
    }

    return $x;
}

function prepInput($k, $v, $new)
{
    $in = '';
    $readonly = ($k == 'id' ? READ_ONLY : '');

    $arrCheckbox = array(
        'first_timer',
        'is_active',
        'is_employed',
        'partner_admin',
        'is_admin',
        'status',
    );

    $arrTextarea = array(
        'home_address',
        'school_address',
        'work_address',
    );

    switch ($k) {
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

    return $in;
}

?>

<main>
    <ul>
        <li><?php if (!isset($new)) { ?>
                <ul>
                    <li><a href="?tab=profile">Profile</a></li>
                    <li><a href="?tab=education">Education</a></li>
                    <li><a href="?tab=employment">Employment</a></li>
                    <li><a href="?tab=sparta_profile">SPARTA</a></li>
                </ul>
            <?php } ?>
        </li>

        <li>
            <form method="post" class="account">
                <h3><?php echo $hdr; ?></h3>
                <hr><?php echo $extra; ?>
                <table>

                    <?php echo $x; ?>

                    <tr>
                        <td colspan=2>
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="rt"><span class="bad"><?php echo $errmsg; ?></span> <a href="<?php echo SCLR_ROOT . '/' . $_SESSION['login_type'] ?>" class="btn">Cancel</a> <input type="submit" value="Submit" /></td>
                    </tr>
                </table>
            </form>
        </li>
    </ul>
</main>