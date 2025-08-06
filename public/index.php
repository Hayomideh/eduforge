<?php
require_once '../includes/auth.php';

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

// Get available courses for homepage
$stmt = executeQuery($db, "
    SELECT c.*, u.name as tutor_name 
    FROM courses c 
    JOIN users u ON c.tutor_id = u.id 
    WHERE c.status = 'active' 
    ORDER BY c.created_at DESC 
    LIMIT 6
");
$courses = $stmt ? $stmt->fetchAll() : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduForge - Online Learning Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-blue-600">
                        <i class="fas fa-graduation-cap mr-2"></i>EduForge
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="login.php" class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded">Login</a>
                    <a href="register.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-700 text-white">
        <div class="max-w-7xl mx-auto px-4 py-20">
            <div class="text-center">
                <h2 class="text-5xl font-bold mb-6">Learn Without Limits</h2>
                <p class="text-xl mb-8 max-w-2xl mx-auto">
                    Join thousands of students learning from expert tutors. Create courses, track progress, and earn certificates.
                </p>
                <div class="space-x-4">
                    <a href="register.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 inline-block">
                        Start Learning
                    </a>
                    <a href="#courses" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 inline-block">
                        Browse Courses
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold mb-4">Why Choose EduForge?</h3>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Everything you need to create, manage, and learn from online courses
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-video text-2xl text-blue-600"></i>
                    </div>
                    <h4 class="text-xl font-semibold mb-2">Rich Content</h4>
                    <p class="text-gray-600">Upload videos, PDFs, and interactive content to create engaging lessons</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-line text-2xl text-green-600"></i>
                    </div>
                    <h4 class="text-xl font-semibold mb-2">Progress Tracking</h4>
                    <p class="text-gray-600">Monitor student progress and completion rates in real-time</p>
                </div>
                
                <div class="text-center p-6">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-certificate text-2xl text-purple-600"></i>
                    </div>
                    <h4 class="text-xl font-semibold mb-2">Certificates</h4>
                    <p class="text-gray-600">Automatic certificate generation upon course completion</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section id="courses" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h3 class="text-3xl font-bold mb-4">Featured Courses</h3>
                <p class="text-gray-600">Start your learning journey with our top-rated courses</p>
            </div>
            
            <?php if (!empty($courses)): ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($courses as $course): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500"></div>
                    <div class="p-6">
                        <h4 class="text-xl font-semibold mb-2"><?= htmlspecialchars($course['title']) ?></h4>
                        <p class="text-gray-600 mb-4"><?= htmlspecialchars(substr($course['description'], 0, 100)) ?>...</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">by <?= htmlspecialchars($course['tutor_name']) ?></span>
                            <span class="text-blue-600 font-semibold">
                                <?= $course['price'] > 0 ? '$' . number_format($course['price'], 2) : 'Free' ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center text-gray-500">
                <i class="fas fa-book text-4xl mb-4"></i>
                <p>No courses available yet. Check back soon!</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-blue-600 text-white py-16">
        <div class="max-w-4xl mx-auto text-center px-4">
            <h3 class="text-3xl font-bold mb-4">Ready to Start Your Learning Journey?</h3>
            <p class="text-xl mb-8">Join our community of learners and instructors today</p>
            <a href="register.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 inline-block">
                Get Started Free
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4">
                        <i class="fas fa-graduation-cap mr-2"></i>EduForge
                    </h4>
                    <p class="text-gray-400">Empowering education through technology</p>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Platform</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Courses</a></li>
                        <li><a href="#" class="hover:text-white">Tutors</a></li>
                        <li><a href="#" class="hover:text-white">Certificates</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Support</h5>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white">Help Center</a></li>
                        <li><a href="#" class="hover:text-white">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Connect</h5>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook text-xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin text-xl"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 EduForge. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>