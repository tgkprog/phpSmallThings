<?php
session_start();

// Load credentials from .cred file
$credFile = __DIR__ . '/.cred';
if (!file_exists($credFile)) {
    http_response_code(500);
    exit('Credentials file not found.');
}

$creds = file($credFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (count($creds) < 2) {
    http_response_code(500);
    exit('Invalid credentials file format.');
}

$USERNAME = trim($creds[0]);
$PASSWORD = trim($creds[1]);

// Directory for storing clipboard entries
define("CLIPBOARD_DIR", "clipboard_data");
if (!is_dir(CLIPBOARD_DIR)) {
    mkdir(CLIPBOARD_DIR, 0777, true);
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['username'] === $USERNAME && $_POST['password'] === $PASSWORD) {
        $_SESSION['loggedin'] = true;
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
    } else {
        $login_error = "Invalid username or password.";
    }
}

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    // Display login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 300px;
                margin: 20px auto;
                padding: 10px;
                background-color: #f9f9f9;
            }
            h2 {
                text-align: center;
            }
        </style>
    </head>
    <body>
        <h2>Login</h2>
        <?php if (isset($login_error)): ?>
            <p style="color:red;"><?= htmlspecialchars($login_error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </body>
    </html>
    <?php
    exit;
}

// Handle clipboard entries submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_FILES['image']) || isset($_POST['text']))) {
    $entry = [];
    if (!empty($_FILES['image']['tmp_name'])) {
        $imagePath = CLIPBOARD_DIR . '/' . time() . '.png';
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        file_put_contents(CLIPBOARD_DIR . '/' . time() . '_image.json', json_encode(['image' => $imagePath]));
    }
    if (!empty($_POST['text'])) {
        file_put_contents(CLIPBOARD_DIR . '/' . time() . '_text.json', json_encode(['text' => $_POST['text']]));
    }
    
    // Keep only last 8 entries
    $files = glob(CLIPBOARD_DIR . '/*.json');
    if (count($files) > 8) {
        array_map('unlink', array_slice($files, 0, count($files) - 8));
    }
    
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Fetch clipboard entries
$entries = array_reverse(array_map('json_decode', array_map('file_get_contents', glob(CLIPBOARD_DIR . '/*.json'))));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clipboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 20px auto;
            padding: 10px;
            background-color: #f9f9f9;
        }
        h2 {
            text-align: center;
        }
        .entry {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
            background-color: white;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        .entry img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 5px;
            cursor: pointer;
        }
        .entry p {
            cursor: pointer;
            white-space: pre-wrap;
        }
    </style>
    <script>
        async function copyImage(imageSrc, element) {
            try {
                const response = await fetch(imageSrc);
                const blob = await response.blob();
                await navigator.clipboard.write([
                    new ClipboardItem({ [blob.type]: blob })
                ]);
                element.style.borderColor = 'green';
            } catch (err) {
                console.error(err.name, err.message);
                element.style.borderColor = 'gray';
            }
            setTimeout(() => element.style.borderColor = '#ccc', 2000);
        }
        function copyText(element) {
            navigator.clipboard.writeText(element.innerText).then(() => {
                element.parentElement.style.borderColor = 'green';
            }).catch(err => {
                console.error(err.name, err.message);
                element.parentElement.style.borderColor = 'gray';
            });
            setTimeout(() => element.parentElement.style.borderColor = '#ccc', 2000);
        }
        async function pasteFromClipboard() {
            try {
                const text = await navigator.clipboard.readText();
                document.getElementById('text-input').value = text;
                document.getElementById('clipboard-form').submit();
            } catch (err) {
                console.error('Failed to read clipboard', err);
            }
        }
    </script>
</head>
<body>
    <h2>Clipboard</h2>
    <a href="?logout">Logout</a>
    <div>
        <?php foreach ($entries as $index => $entry): ?>
            <div class="entry">
                <?php if (isset($entry->image)): ?>
                    <img src="<?= htmlspecialchars($entry->image) ?>" onclick="copyImage('<?= htmlspecialchars($entry->image) ?>', this.parentElement)">
                <?php endif; ?>
                <?php if (isset($entry->text)): ?>
                    <p onclick="copyText(this)">
                        <?= nl2br(htmlspecialchars($entry->text)) ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <form id="clipboard-form" method="POST" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*">
        <textarea id="text-input" name="text" placeholder="Enter text"></textarea>
        <button type="submit">Save</button>
        <button type="button" onclick="pasteFromClipboard()">Paste from Clipboard</button>
    </form>
    
    <br>
    v21
</body>
</html>
