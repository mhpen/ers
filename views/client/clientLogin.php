<?php 
session_start(); 
if (isset($_SESSION['client'])) {
    header('Location: dashboard.php');
    exit();
}
include_once '../shared/header.php';
?>

<body class="bg-background min-h-screen font-sans antialiased">
    <div class="container relative min-h-screen flex-col items-center justify-center grid lg:max-w-none lg:grid-cols-2 lg:px-0">
        <!-- Left side with image -->
        <div class="relative hidden h-full flex-col bg-muted p-10 text-white lg:flex dark:border-r">
            <div class="absolute inset-0">
                <!-- Background Image -->
                <img 
                    src="../../public/assets/pexels-adrien-olichon-1257089-2387532.jpg" 
                    alt="Background" 
                    class="h-full w-full object-cover"
                />
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-black/50"></div>
                <!-- Additional Gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-black/30"></div>
            </div>
            <!-- Content -->
            <div class="relative z-20 flex items-center text-lg font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" class="mr-2 h-6 w-6">
                    <path d="M15 6v12a3 3 0 1 0 3-3H6a3 3 0 1 0 3 3V6a3 3 0 1 0-3 3h12a3 3 0 1 0-3-3" />
                </svg>
                Event Registration System
            </div>
            <!-- Quote Section -->
            <div class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <p class="text-lg">
                        "Create and manage your events with ease. Connect with your audience and track registrations seamlessly."
                    </p>
                    <footer class="text-sm text-white/60">Client Portal</footer>
                </blockquote>
            </div>
        </div>

        <!-- Right side with login form -->
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-2xl font-semibold tracking-tight">Client Login</h1>
                    <p class="text-sm text-muted-foreground">Enter your credentials to access your event dashboard</p>
                </div>

                <div class="grid gap-6">
                    <form action="../../controllers/client/AuthController.php" method="POST">
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <label class="text-sm font-medium leading-none" for="email">
                                    Email
                                </label>
                                <input
                                    type="email"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    id="email"
                                    name="email"
                                    placeholder="name@example.com"
                                    required
                                >
                            </div>
                            
                            <div class="grid gap-2">
                                <label class="text-sm font-medium leading-none" for="password">
                                    Password
                                </label>
                                <div class="relative">
                                    <input
                                        type="password"
                                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                        id="password"
                                        name="password"
                                        required
                                    >
                                    <button 
                                        type="button"
                                        id="togglePassword"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="eyeIcon">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <?php if (isset($_GET['error'])): ?>
                                <div class="rounded-md bg-destructive/15 text-destructive px-4 py-3 text-sm">
                                    <?php 
                                    $error = $_GET['error'];
                                    if ($error == 1) {
                                        echo "Invalid email or password";
                                    } else if ($error == 2) {
                                        echo "An error occurred. Please try again.";
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_GET['pending'])): ?>
                                <div class="rounded-md bg-yellow-100 text-yellow-800 px-4 py-3 text-sm">
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Your account is pending approval. Please wait for admin verification.
                                    </div>
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                                Sign in
                            </button>
                        </div>
                    </form>
                    <p class="text-sm text-center text-muted-foreground">
                        Don't have an account? 
                        <a href="register.php" class="text-primary hover:underline">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                password.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        });
    </script>
</body>
</html> 