<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Register New User
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Admin access required
            </p>
        </div>
        
        <?php if (isset($error)) : ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="<?php echo APP_URL; ?>/register" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <!-- Username -->
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <input id="username" 
                           name="username" 
                           type="text" 
                           required 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>"
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Username">
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="sr-only">Email</label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           required 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>"
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Email address">
                </div>
                
                <!-- Full Name -->
                <div>
                    <label for="full_name" class="sr-only">Full Name</label>
                    <input id="full_name" 
                           name="full_name" 
                           type="text" 
                           required 
                           value="<?php echo htmlspecialchars($full_name ?? ''); ?>"
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Full Name">
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Password">
                </div>
                
                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="sr-only">Confirm Password</label>
                    <input id="confirm_password" 
                           name="confirm_password" 
                           type="password" 
                           required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Confirm Password">
                </div>
            </div>

            <!-- Role Selection -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">
                    User Role
                </label>
                <select id="role" 
                        name="role" 
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                    <option value="user" <?php echo (($role ?? 'user') == 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo (($role ?? '') == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    Register User
                </button>
            </div>

            <div class="text-center">
                <a href="<?php echo APP_URL; ?>/dashboard" class="font-medium text-blue-600 hover:text-blue-500">
                    Back to Dashboard
                </a>
            </div>
        </form>
    </div>
</div>
