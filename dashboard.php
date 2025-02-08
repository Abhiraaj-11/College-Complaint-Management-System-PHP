<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = $_SESSION['role'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit_complaint'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $category = $_POST['category'];
        $anonymous = isset($_POST['anonymous']) ? 1 : 0;

        $sql = "INSERT INTO complaints (user_id, title, description, category, anonymous) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isssi", $user_id, $title, $description, $category, $anonymous);
        mysqli_stmt_execute($stmt);
    }

    if ($is_admin && isset($_POST['update_status'])) {
        $complaint_id = $_POST['complaint_id'];
        $status = $_POST['status'];
        $response = mysqli_real_escape_string($conn, $_POST['response']);

        $sql = "UPDATE complaints SET status = ?, response = ? WHERE complaint_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $status, $response, $complaint_id);
        mysqli_stmt_execute($stmt);
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode(['success' => true]);
            exit;
        }
    }
}

// Fetch complaints
if ($is_admin) {
    $sql = "SELECT c.*, u.username FROM complaints c 
            LEFT JOIN users u ON c.user_id = u.user_id 
            ORDER BY created_at DESC";
} else {
    $sql = "SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC";
}

$stmt = mysqli_prepare($conn, $sql);
if (!$is_admin) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
}
mysqli_stmt_execute($stmt);
$complaints = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - College Complaint System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
</div>

    <div class="container">
        <header>
            <h1><?php echo $is_admin ? 'Admin' : 'User' ?> Dashboard</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <?php if (!$is_admin): ?>
        <div class="complaint-form">
            <h2>Submit New Complaint/Appreciation</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <select name="category" required>
                        <option value="complaint">Complaint</option>
                        <option value="appreciation">Appreciation</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="anonymous">
                        Submit Anonymously
                    </label>
                </div>
                <button type="submit" name="submit_complaint">Submit</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="complaints-list">
            <h2>Complaints/Appreciations</h2>
            <?php while ($row = mysqli_fetch_assoc($complaints)): ?>
                <div class="complaint-card">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p class="category"><?php echo ucfirst($row['category']); ?></p>
                    <p class="status">Status: <?php echo ucfirst($row['status']); ?></p>
                    <?php if ($is_admin && !$row['anonymous']): ?>
                        <p>By: <?php echo htmlspecialchars($row['username']); ?></p>
                    <?php endif; ?>
                    <p class="submitted-on">Submitted on: <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></p>

                    <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                    
                    <?php if ($row['response']): ?>
                        <div class="response">
                            <h4>Admin Response:</h4>
                            <p><?php echo nl2br(htmlspecialchars($row['response'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($is_admin): ?>
                        <form method="POST" class="admin-response">
                            <input type="hidden" name="complaint_id" value="<?php echo $row['complaint_id']; ?>">
                            <div class="form-group">
                                <label>Update Status:</label>
                                <select name="status" required>
                                    <option value="open" <?php echo $row['status'] == 'open' ? 'selected' : ''; ?>>Open</option>
                                    <option value="in_progress" <?php echo $row['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="resolved" <?php echo $row['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Response:</label>
                                <textarea name="response"><?php echo htmlspecialchars($row['response']); ?></textarea>
                            </div>
                            <button type="submit" name="update_status">Update</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>
</html>