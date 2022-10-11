<?php
define('BASE', $base);
define('TAB', isset($_GET['tab']) ? $_GET['tab'] : null);

if (isset($_SESSION['login'])) {
    switch (BASE) {
        case 'profile':
            $user = USER_ID;
            $hdr  = USER_NAME;
            break;

        case 'accounts':
            if (TAB == '') {
                $_SESSION['account_id'] = URI;
            }

            $user = $_SESSION['account_id'];
            $hdr  = 'Account: ' . sprintf("%016d", $_SESSION['account_id']);
            break;
    }
}

define('USER', isset($user) ? $user : null);

$x = $changepass = '';
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

    $data = authAPI('me/' . (TAB == '' ? 'profile' : TAB));

    if (isset($data[0])) {
        $x .= populateForm($data[0]);
        $changepass = (TAB == '' || TAB == 'profile' ? '<a href="password" class="btn">Change Password</a>' : '');
    }

}

function populateForm($arr, $new = 0, $empty = 0) {
    $x = '';

    $arrAdmin = array(
        'date_joined',
        'last_login',
        'first_timer',
        'is_active',
        'hash',
        'is_global',
        'is_admin',
        'is_partner',
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

            } elseif ($k == 'is_verified') {

                if ($v) {
                    $in = '<span>Yes</span>';

                } else {
                    if(USER_ISADMIN) {
                        $in = '<input type="hidden" name="' . $k . '" value=0 /><input type="checkbox" name="' . $k . '" value=1 ' . ($v ? CHECKED : '') . ' />';

                    } else {
                        $in = '<input type="hidden" name="' . $k . '" /><span>No</span>';
                    }
                }

            } elseif ($k == 'photo_verification') {
                if(isset($photo_verification)) {
                    $img = DOC_ROOT . IMG_PATH . "$photo_verification";
                    $photo_ok = file_exists($img);

                    if ($photo_ok) {
                        $in = '<a href="' . SCLR_ROOT . IMG_PATH . "$photo_verification" . '" target="_blank">View verification photo</a>';

                    } else {
                        $in = '<input type="file" name="photo_verification" accept="image/*" />';
                    }

                } else {
                    $in .= '<input type="file" name="photo_verification" accept="image/*" />';
                }

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

function prepInput($k, $v, $new) {
    $in = '';
    $readonly = ($k == 'id' ? READ_ONLY : '');

    $arrCheckbox = array(
        'first_timer',
        'is_active',
        'is_employed',
        'is_global',
        'is_admin',
        'is_partner',
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
                    <li><a href="?tab=scholarship">Scholarship</a></li>
                    <li><a href="?tab=sparta_profile">SPARTA</a></li>
                </ul>
            <?php } ?>
        </li>

        <li>
            <form method="post" enctype="multipart/form-data" class="account">
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
                        <td><?php echo $changepass; ?></td>
                        <td class=" rt"><span class="bad"><?php echo $errmsg; ?></span> <a href="<?php echo SCLR_ROOT . '/' . $_SESSION['login_type'] ?>" class="btn">Cancel</a> <input type="submit" value="Submit" /></td>
                    </tr>
                </table>
            </form>
        </li>
    </ul>
</main>