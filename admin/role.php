<?php 
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Ensure only admin can access this page
redirectIfNotAdmin();
// UPDATE USER ROLE

if (isset($_POST['update_role'])) {

    $userId = $_POST['user_id'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("
        UPDATE users
        SET is_admin = ?
        WHERE user_id = ?
    ");

    $stmt->execute([$role, $userId]);

    echo '

<!DOCTYPE html>
<html>
<head>

<title>Success</title>

<style>

body{
    margin:0;
    padding:0;
    font-family:Arial,sans-serif;
    background:rgba(15,23,42,0.6);

    display:flex;
    justify-content:center;
    align-items:center;

    height:100vh;
}

.success-popup{

    width:420px;

    background:#fff;

    border-radius:24px;

    padding:40px;

    text-align:center;

    box-shadow:
    0 20px 50px rgba(0,0,0,0.2);

    animation:popup 0.4s ease;
}

.icon{

    width:90px;
    height:90px;

    margin:auto;

    border-radius:50%;

    background:linear-gradient(135deg,#22c55e,#16a34a);

    display:flex;
    justify-content:center;
    align-items:center;

    font-size:42px;
    color:white;
}

.success-popup h2{

    margin-top:25px;

    font-size:32px;

    color:#0f172a;
}

.success-popup p{

    color:#64748b;

    margin-top:12px;

    font-size:16px;

    line-height:1.6;
}

.loader{

    width:45px;
    height:45px;

    margin:30px auto 0;

    border:4px solid #e2e8f0;

    border-top:4px solid #4f46e5;

    border-radius:50%;

    animation:spin 1s linear infinite;
}

@keyframes spin{

    100%{
        transform:rotate(360deg);
    }

}

@keyframes popup{

    from{
        transform:scale(0.7);
        opacity:0;
    }

    to{
        transform:scale(1);
        opacity:1;
    }

}

</style>

</head>

<body>

<div class="success-popup">

    <div class="icon">
        ✓
    </div>

    <h2>Role Updated!</h2>

    <p>
        User role has been updated successfully.<br>
        Redirecting back...
    </p>

    <div class="loader"></div>

</div>

<script>

setTimeout(function(){

    window.location.href="role.php";

}, 2500);

</script>

</body>
</html>

';

exit();

    
}
try {
    // Fetch all users with their current role (admin or user) with pagination
    $perPage = 10;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $perPage;
    $totalCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalPages = ceil($totalCount / $perPage);
    $stmt = $pdo->prepare("SELECT user_id, full_name, email, is_admin FROM users ORDER BY user_id LIMIT :perPage OFFSET :offset");
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching users: " . $e->getMessage());
    die("An error occurred while fetching user data. Please try again later.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles - Nexus Bank Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-main.css">
    <link rel="stylesheet" href="../assets/css/admin-role.css">

    <script src="../assets/js/sidebar.js"></script>
</head>

<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="Logos-cont">
                <img src="../assets/images/Logo-color.png" alt="SecureBank Logo" class="logo-container">
            </div>

            <nav class="dashboard-nav">
                <a href="dashboard.php" class="btn">Dashboard</a>
                <a href="manage-users.php" class="btn">Manage Users</a>
                <a href="manage-loans.php" class="btn">Manage Loans</a>
                <a href="manage-investments.php" class="btn">Manage Investments</a>
                <a href="track-investments.php" class="btn">Users Investments</a>
                <a href="role.php" class="btn dash-text">Roles</a>
                <a href="recent_transactions.php" class="btn">Transactions</a>
                <a href="loan-history.php" class="btn">Loan History</a>
                <a href="login-records.php" class="btn">Login Records</a>
                <a href="manage-messages.php" class="btn">Contact Messages</a>
            </nav>

            <div class="logout-cont">
                <a href="../logout.php" class="logout">Logout</a>
            </div>
        </aside>

        <main class="container">
            <header>
                <h1>Manage User Roles</h1>
                <button class="hamburger">&#9776;</button>
            </header>

            <div class="content">
                <h2>User Roles</h2>

                <?php if (empty($users)): ?>
                <p>No users found.</p>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td data-label="User ID"><?= htmlspecialchars($user['user_id']) ?></td>
                            <td data-label="Full Name"><?= htmlspecialchars($user['full_name']) ?></td>
                            <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                            <td data-label="Role">

                                <form method="POST" style="display:flex; gap:10px; align-items:center;">

                                    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

                                    <select name="role" style="
            padding:8px 12px;
            border-radius:8px;
            border:1px solid #ccc;
        ">

                                        <option value="0" <?= $user['is_admin'] == 0 ? 'selected' : '' ?>>
                                            User
                                        </option>

                                        <option value="1" <?= $user['is_admin'] == 1 ? 'selected' : '' ?>>
                                            Admin
                                        </option>

                                    </select>

                                    <button type="submit" name="update_role" style="
        background:#4f46e5;
        color:white;
        border:none;
        padding:8px 14px;
        border-radius:8px;
        cursor:pointer;
        ">

                                        Update

                                    </button>

                                </form>

                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
                <!-- Pagination Controls -->
                <?php if ($totalPages > 1): ?>
                <style>
                .pagination {
                    text-align: center;
                    margin: 20px 0;
                }

                .pagination a {
                    display: inline-block;
                    margin: 0 4px;
                    padding: 6px 12px;
                    color: #007bff;
                    background: #fff;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    text-decoration: none;
                    transition: background 0.2s, color 0.2s;
                }

                .pagination a.btn-primary,
                .pagination a.active {
                    background: #007bff;
                    color: #fff;
                    border-color: #007bff;
                    pointer-events: none;
                }

                .pagination a:hover:not(.btn-primary):not(.active) {
                    background: #f0f0f0;
                }
                </style>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'btn-primary active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

</html>