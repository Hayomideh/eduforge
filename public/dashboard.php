<?php
require_once '../includes/auth.php';

// Require login
requireLogin();

$user = getCurrentUser();

// Get user's enrolled courses (for students)
$enrolledCourses = [];
if ($user['role'] === 'student') {
    $stmt = executeQuery($db, "
        SELECT c.*, u.name as tutor_name 
        FROM courses c 
        JOIN enrollments e ON c.id = e.course_id 
        JOIN users u ON c.tutor_id = u.id 
        WHERE e.user_id = ? AND c.status = 'active'
        ORDER BY e.enrolled_at DESC
    ", [$user['id']]);
    $enrolledCourses = $stmt ? $stmt->fetchAll() : [];
}

// Get user's courses (for tutors)
$myCourses = [];
if ($user['role'] === 'tutor') {
    $stmt = executeQuery($db, "
        SELECT c.*, COUNT(e.id) as student_count 
        FROM courses c 
        LEFT JOIN enrollments e ON c.id = e.course_id 
        WHERE c.tutor_id = ? 
        GROUP BY c.id 
        ORDER BY c.created_at DESC
    ", [$user['id']]);
    $myCourses = $stmt ? $stmt->fetchAll() : [];
}

// Get available courses (for all users)
$availableCourses = [];
$stmt = executeQuery($db, "
    SELECT c.*, u.name as tutor_name 
    FROM courses c 
    JOIN users u ON c.tutor_id = u.id 
    WHERE c.status = 'active' 
    ORDER BY c.created_at DESC 
    LIMIT 6
");
$availableCourses = $stmt ? $stmt->fetchAll() : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EduForge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="dashboard.php" class="text-2xl font-bold text-blue-600">
                        <i class="fas fa-graduation-cap mr-2"></i>EduForge
                    </a>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="dashboard.php" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-home mr-1"></i>Dashboard
                    </a>
                    <?php if ($user['role'] === 'tutor'): ?>
                        <a href="#" class="text-gray-600 hover:text-blue-600">
                            <i class="fas fa-plus mr-1"></i>Create Course
                        </a>
                    <?php endif; ?>
                    <div class="relative group">
                        <button class="flex items-center text-gray-600 hover:text-blue-600">
                            <i class="fas fa-user-circle mr-2"></i>
                            <?= htmlspecialchars($user['name']) ?>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 hidden group-hover:block">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <hr class="my-1">
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">
                Welcome back, <?= htmlspecialchars($user['name']) ?>! 
                <span class="text-sm font-normal text-gray-500 capitalize">
                    (<?= htmlspecialchars($user['role']) ?>)
                </span>
            </h1>
            <p class="text-gray-600 mt-2">
                <?php if ($user['role'] === 'student'): ?>
                    Continue your learning journey
                <?php elseif ($user['role'] === 'tutor'): ?>
                    Manage your courses and students
                <?php else: ?>
                    System administration dashboard
                <?php endif; ?>
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <?php if ($user['role'] === 'student'): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-book-open text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold"><?= count($enrolledCourses) ?></h3>
                            <p class="text-gray-600">Enrolled Courses</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-chart-line text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold">0</h3>
                            <p class="text-gray-600">Completed</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-certificate text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold">0</h3>
                            <p class="text-gray-600">Certificates</p>
                        </div>
                    </div>
                </div>
            <?php elseif ($user['role'] === 'tutor'): ?>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold"><?= count($myCourses) ?></h3>
                            <p class="text-gray-600">My Courses</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-users text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold"><?= array_sum(array_column($myCourses, 'student_count')) ?></h3>
                            <p class="text-gray-600">Total Students</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-star text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold">4.8</h3>
                            <p class="text-gray-600">Avg Rating</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div>
                <?php if ($user['role'] === 'student' && !empty($enrolledCourses)): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4">My Courses</h2>
                        <div class="space-y-4">
                            <?php foreach ($enrolledCourses as $course): ?>
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <h3 class="font-semibold"><?= htmlspecialchars($course['title']) ?></h3>
                                    <p class="text-sm text-gray-600">by <?= htmlspecialchars($course['tutor_name']) ?></p>
                                    <div class="mt-2">
                                        <div class="bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: 30%"></div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">30% complete</p>
                                    </div>
                                    <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                                        Continue Learning
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php elseif ($user['role'] === 'tutor'): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold">My Courses</h2>
                            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                                <i class="fas fa-plus mr-2"></i>New Course
                            </button>
                        </div>
                        <?php if (!empty($myCourses)): ?>
                            <div class="space-y-4">
                                <?php foreach ($myCourses as $course): ?>
                                    <div class="border rounded-lg p-4">
                                        <h3 class="font-semibold"><?= htmlspecialchars($course['title']) ?></h3>
                                        <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...</p>
                                        <div class="flex justify-between items-center mt-3">
                                            <span class="text-sm text-gray-500">
                                                <i class="fas fa-users mr-1"></i><?= $course['student_count'] ?> students
                                            </span>
                                            <div class="space-x-2">
                                                <button class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                                                <button class="text-green-600 hover:text-green-800 text-sm">View</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-gray-500 py-8">
                                <i class="fas fa-chalkboard-teacher text-4xl mb-4"></i>
                                <p>You haven't created any courses yet.</p>
                                <button class="mt-4 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                    Create Your First Course
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">
                        <?= $user['role'] === 'student' ? 'Discover Courses' : 'Platform Courses' ?>
                    </h2>
                    <?php if (!empty($availableCourses)): ?>
                        <div class="space-y-4">
                            <?php foreach (array_slice($availableCourses, 0, 4) as $course): ?>
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <h3 class="font-semibold"><?= htmlspecialchars($course['title']) ?></h3>
                                    <p class="text-sm text-gray-600">by <?= htmlspecialchars($course['tutor_name']) ?></p>
                                    <div class="flex justify-between items-center mt-3">
                                        <span class="text-blue-600 font-semibold">
                                            <?= $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free' ?>
                                        </span>
                                        <?php if ($user['role'] === 'student'): ?>
                                            <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">
                                                Enroll Now
                                            </button>
                                        <?php else: ?>
                                            <button class="text-blue-600 hover:text-blue-800 text-sm">View Details</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-book text-4xl mb-4"></i>
                            <p>No courses available yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>