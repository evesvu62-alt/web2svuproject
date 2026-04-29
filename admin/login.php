<?php

declare(strict_types=1);

require_once __DIR__ . '/../session_bootstrap.php';
startAppSession();

// redirect to dashboard if already logged in
if (
    isset($_SESSION['admin_logged_in']) &&
    $_SESSION['admin_logged_in'] === true
) {
    $sessionUsername = htmlspecialchars(
        (string) ($_SESSION['admin_username'] ?? 'Admin'),
    );
    echo <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirecting...</title>
    <script src="../assets/js/main.js" defer></script>
</head>
<body
    data-admin-auth-action="set"
    data-admin-auth-username="{$sessionUsername}"
    data-admin-auth-redirect="dashboard.php"
>
</body>
</html>
HTML;
    exit;
}

require_once __DIR__ . '/../db.php';

$error = '';
$success = '';
$activeForm = 'login';
$fieldErrors = [
    'username' => '',
    'password' => '',
    'signup_username' => '',
    'signup_password' => '',
    'signup_confirm_password' => '',
];

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? 'login';
    $activeForm = $formType === 'signup' ? 'signup' : 'login';

    if ($activeForm === 'signup') {
        $username = trim($_POST['signup_username'] ?? '');
        $password = trim($_POST['signup_password'] ?? '');
        $confirmPassword = trim($_POST['signup_confirm_password'] ?? '');
        $normalizedUsername = strtolower($username);

        // k if username, password, and confirm password are empty.
        if ($username === '' || $password === '' || $confirmPassword === '') {
            $error = 'Please fill in username, password, and confirm password';
            if ($username === '') {
                $fieldErrors['signup_username'] = 'Username is required';
            }
            if ($password === '') {
                $fieldErrors['signup_password'] = 'Password is required';
            }
            if ($confirmPassword === '') {
                $fieldErrors['signup_confirm_password'] =
                    'Confirm password is required';
            }
            // check if username is at least 3 characters.
        } elseif (strlen($username) < 3) {
            $error = 'Username must be at least 3 characters';
            $fieldErrors['signup_username'] =
                'Username must be at least 3 characters';
            // check if password is at least 4 characters.
        } elseif (strlen($password) < 4) {
            $error = 'Password must be at least 4 characters';
            $fieldErrors['signup_password'] =
                'Password must be at least 4 characters';
            // check if password and confirm password match.
        } elseif ($password !== $confirmPassword) {
            $error = 'Password and confirm password do not match';
            $fieldErrors['signup_confirm_password'] = 'Passwords do not match';
        } else {
            // check if username already exists.
            try {
                $existingStmt = $pdo->prepare(
                    'SELECT id FROM users WHERE LOWER(username) = :username LIMIT 1',
                );
                $existingStmt->execute(['username' => $normalizedUsername]);
                $existingUser = $existingStmt->fetch();

                if ($existingUser) {
                    $error = 'This username is already registered';
                    $fieldErrors['signup_username'] =
                        'This username is already taken';
                } else {
                    // insert user into database.
                    $insertStmt = $pdo->prepare(
                        'INSERT INTO users (username, password) VALUES (:username, :password)',
                    );
                    $insertStmt->execute([
                        'username' => $username,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                    ]);

                    $success = 'Sign up successful! You can now sign in.';
                    $activeForm = 'login';
                }
            } catch (PDOException $e) {
                $error = 'Database error. Please try again.';
            }
        }
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $normalizedUsername = strtolower($username);

        // Check if username and password are empty.
        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password';
            if ($username === '') {
                $fieldErrors['username'] = 'Username is required';
            }
            if ($password === '') {
                $fieldErrors['password'] = 'Password is required';
            }
        } else {
            try {
                $stmt = $pdo->prepare(
                    'SELECT username, password FROM users WHERE LOWER(username) = :username LIMIT 1',
                );
                $stmt->execute(['username' => $normalizedUsername]);
                $user = $stmt->fetch();

                $isValidPassword = false;
                if ($user) {
                    $storedPassword = (string) $user['password'];
                    // support legacy plain-text users and new hashed passwords.
                    $isValidPassword =
                        password_verify($password, $storedPassword) ||
                        $storedPassword === $password;
                }

                if ($user && $isValidPassword) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = (string) $user['username'];
                    $escapedUsername = htmlspecialchars((string) $user['username']);
                    echo <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirecting...</title>
    <script src="../assets/js/main.js" defer></script>
</head>
<body
    data-admin-auth-action="set"
    data-admin-auth-username="{$escapedUsername}"
    data-admin-auth-redirect="dashboard.php"
>
</body>
</html>
HTML;
                    exit;
                }

                // if username or password is invalid, set field errors.
                $error = 'Invalid username or password';
                $fieldErrors['username'] = 'Check your username';
                $fieldErrors['password'] = 'Check your password';
            } catch (PDOException $e) {
                $error = 'Database error. Please try again.';
            }
        }
    }
}

// Include nav_footer for consistent styling
function renderLoginHeader(string $pageTitle = 'Admin Login'): void
{
    echo <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$pageTitle}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-page">
HTML;
}

function renderLoginFooter(): void
{
    echo <<<HTML
<script src="../assets/js/main.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
HTML;
}

renderLoginHeader('Admin Login - City Events');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="login-card rounded-4 p-4 p-lg-5">
                <div class="text-center mb-4">
                    <div class="login-brand mx-auto">UE</div>
                    <h2 class="h3 mb-2" id="authTitle">
                        <?= $activeForm === 'signup' ? 'Create Account' : 'Welcome Back' ?>
                    </h2>
                    <p class="text-body-secondary small" id="authSubtitle">
                        <?= $activeForm === 'signup' ? 'Sign up to manage your events' : 'Sign in to manage your events' ?>
                    </p>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="error-message mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success !== ''): ?>
                    <div class="alert alert-success py-2 mb-4" role="alert">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <div class="auth-toggle mb-4">
                    <button
                        class="auth-toggle-btn <?= $activeForm === 'login' ? 'active' : '' ?>"
                        type="button"
                        data-auth-target="login"
                    >
                        Sign In
                    </button>
                    <button
                        class="auth-toggle-btn <?= $activeForm === 'signup' ? 'active' : '' ?>"
                        type="button"
                        data-auth-target="signup"
                    >
                        Sign Up
                    </button>
                </div>

                <form
                    method="POST"
                    action="login.php"
                    class="login-form auth-form <?= $activeForm === 'login' ? '' : 'd-none' ?>"
                    data-auth-form="login"
                >
                    <input type="hidden" name="form_type" value="login">
                    <div class="mb-3">
                        <label for="username" class="form-label small fw-semibold">Username</label>
                        <input type="text"
                            class="form-control login-input <?= $fieldErrors['username'] !== '' ? 'is-invalid' : '' ?>"
                            id="username"
                            name="username"
                            placeholder="Enter your username"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                            required>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                $fieldErrors['username'] !== ''
                                    ? $fieldErrors['username']
                                    : 'Please enter your username',
                            ) ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label small fw-semibold">Password</label>
                        <input type="password"
                            class="form-control login-input <?= $fieldErrors['password'] !== '' ? 'is-invalid' : '' ?>"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                $fieldErrors['password'] !== ''
                                    ? $fieldErrors['password']
                                    : 'Please enter your password',
                            ) ?>
                        </div>
                    </div>

                    <button type="submit" class="btn login-btn w-100 mb-3">
                        Sign In
                    </button>
                </form>

                <form
                    method="POST"
                    action="login.php"
                    class="login-form auth-form <?= $activeForm === 'signup' ? '' : 'd-none' ?>"
                    data-auth-form="signup"
                >
                    <input type="hidden" name="form_type" value="signup">
                    <div class="mb-3">
                        <label for="signup_username" class="form-label small fw-semibold">Username</label>
                        <input type="text"
                            class="form-control login-input <?= $fieldErrors['signup_username'] !== '' ? 'is-invalid' : '' ?>"
                            id="signup_username"
                            name="signup_username"
                            placeholder="Choose a username"
                            value="<?= htmlspecialchars($_POST['signup_username'] ?? '') ?>"
                            required>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                $fieldErrors['signup_username'] !== ''
                                    ? $fieldErrors['signup_username']
                                    : 'Please choose a username',
                            ) ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="signup_password" class="form-label small fw-semibold">Password</label>
                        <input type="password"
                            class="form-control login-input <?= $fieldErrors['signup_password'] !== '' ? 'is-invalid' : '' ?>"
                            id="signup_password"
                            name="signup_password"
                            placeholder="Create a password"
                            required>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                $fieldErrors['signup_password'] !== ''
                                    ? $fieldErrors['signup_password']
                                    : 'Please create a password',
                            ) ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label
                            for="signup_confirm_password"
                            class="form-label small fw-semibold"
                        >
                            Confirm Password
                        </label>
                        <input type="password"
                            class="form-control login-input <?= $fieldErrors['signup_confirm_password'] !== '' ? 'is-invalid' : '' ?>"
                            id="signup_confirm_password"
                            name="signup_confirm_password"
                            placeholder="Confirm your password"
                            required>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars(
                                $fieldErrors['signup_confirm_password'] !== ''
                                    ? $fieldErrors['signup_confirm_password']
                                    : 'Please confirm your password',
                            ) ?>
                        </div>
                    </div>

                    <button type="submit" class="btn login-btn w-100 mb-3">
                        Sign Up
                    </button>
                </form>

                <div class="text-center">
                    <small class="text-body-secondary">
                        Default: admin / admin
                    </small>
                </div>

                <div class="text-center mt-4">
                    <a href="../index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Back to Site
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php renderLoginFooter(); ?>