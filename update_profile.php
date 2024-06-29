<?php
include 'base.php';
include 'database.php';


// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$user_id = $_SESSION['id'];
$bio = $interests = $education = $work_experience = '';

// Fetch current profile information
$query = "SELECT * FROM authors WHERE user_id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$author = $result->fetch_assoc();
$stmt->close();

// If no author found for the user, handle accordingly
if (!$author) {
    echo "Author not found.";
    exit();
}

// Parse existing profile JSON if it exists
$profile = json_decode($author['profile_json_text'], true);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $bio = isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : '';
    $interests = isset($_POST['interests']) ? $_POST['interests'] : [];
    $education = isset($_POST['education']) ? htmlspecialchars($_POST['education']) : '';
    $work_experience = isset($_POST['work_experience']) ? htmlspecialchars($_POST['work_experience']) : '';

    // Prepare profile data as JSON
    $updated_profile = [
        'bio' => $bio,
        'interests' => $interests,
        'education' => $education,
        'work_experience' => $work_experience
    ];
    $profile_json_text = json_encode($updated_profile);

    // Update profile in database
    $update_query = "UPDATE authors SET profile_json_text = ? WHERE user_id = ?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("si", $profile_json_text, $user_id);

    if ($stmt->execute()) {
        // Redirect to profile page after successful update
        header("Location: profile.php?id=" . $user_id);
        exit();
    } else {
        echo "Lỗi cập nhật: " . $stmt->error;
    }

    $stmt->close();
}
?>

<div class="container">
    <h1>Cập nhật hồ sơ</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="mb-3">
            <label for="bio" class="form-label">Tiểu sử:</label>
            <textarea class="form-control" id="bio" name="bio"><?php echo isset($profile['bio']) ? htmlspecialchars($profile['bio']) : ''; ?></textarea>
        </div>
        <div class="mb-3">
            <label for="interests" class="form-label">Hướng nghiên cứu quan tâm (phân tách bằng dấu phẩy):</label>
            <input type="text" class="form-control" id="interests" name="interests" value="<?php echo isset($profile['interests']) ? implode(', ', $profile['interests']) : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="education" class="form-label">Đào tạo:</label>
            <input type="text" class="form-control" id="education" name="education" value="<?php echo isset($profile['education']) ? htmlspecialchars($profile['education']) : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="work_experience" class="form-label">Kinh nghiệm làm việc:</label>
            <input type="text" class="form-control" id="work_experience" name="work_experience" value="<?php echo isset($profile['work_experience']) ? htmlspecialchars($profile['work_experience']) : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
    </form>
    <a href="profile.php?id=<?php echo $user_id; ?>" class="btn btn-secondary" style="margin-top: 10px;">Quay lại hồ sơ</a>
</div>