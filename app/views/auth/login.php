<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME ?? 'H&S Inventory'; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body>
<?php
//echo password_hash("Password123", PASSWORD_DEFAULT);
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <!-- Login Card -->
    <div class="bg-white shadow-2xl rounded-2xl p-8 space-y-6 max-w-md w-full">
        <!-- Logo and Title -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full shadow-lg mb-4 float-animation">
                <i class="fas fa-warehouse text-white text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                H&S Inventory
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                Sign in to manage your inventory
            </p>
        </div>
        
        <!-- Error Message -->
        <?php if (isset($error)) : ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg animate-pulse">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <span class="text-red-700 text-sm font-medium"><?php echo $error; ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- LDAP Warning -->
        <?php if (isset($ldap_status) && !$ldap_status['enabled']) : ?>
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400 p-4 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-yellow-500 mr-3 mt-0.5"></i>
                <div>
                    <p class="font-semibold text-yellow-800 text-sm">LDAP Authentication Unavailable</p>
                    <p class="text-xs text-yellow-700 mt-1">Using local authentication only</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <form action="<?php echo APP_URL; ?>/login<?php echo isset($_GET['returnTo']) ? '?returnTo=' . urlencode($_GET['returnTo']) : ''; ?>" method="POST" class="space-y-5">
            <?php if (isset($_GET['returnTo'])) : ?>
            <input type="hidden" name="returnTo" value="<?php echo htmlspecialchars($_GET['returnTo']); ?>">
            <?php endif; ?>
            <!-- Username Field -->
            <div class="group">
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user text-indigo-500 mr-1"></i> Username
                </label>
                <div class="relative">
                    <input id="username" 
                           name="username" 
                           type="text" 
                           required 
                           class="block w-full px-4 py-3 pl-12 rounded-lg border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm font-medium hover:border-indigo-300" 
                           placeholder="Enter your username" 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <i class="fas fa-user text-gray-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                </div>
            </div>
            
            <!-- Password Field -->
            <div class="group">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-lock text-purple-500 mr-1"></i> Password
                </label>
                <div class="relative">
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           class="block w-full px-4 py-3 pl-12 rounded-lg border-2 border-gray-200 shadow-sm focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition-all duration-200 text-sm font-medium hover:border-purple-300" 
                           placeholder="Enter your password">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <i class="fas fa-lock text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                    </div>
                    <button type="button" 
                            onclick="togglePassword()" 
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-purple-500 transition-colors">
                        <i id="toggleIcon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" 
                           name="remember" 
                           type="checkbox" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                    Forgot password?
                </a>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center group">
                <i class="fas fa-sign-in-alt mr-2 group-hover:translate-x-1 transition-transform"></i>
                Sign In
            </button>
        </form>
        
        <!-- Copyright -->
        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-gray-500 text-xs">
                &copy; <?php echo date('Y'); ?> H&S Inventory Management System
            </p>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Auto-focus username field
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('username').focus();
});

// Add enter key support for form submission
document.getElementById('password').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.querySelector('form').submit();
    }
});
</script>
</body>
</html>
