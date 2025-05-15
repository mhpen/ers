<?php 
session_start(); 
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
                        "Join exciting events and manage your registrations all in one place. Your gateway to memorable experiences."
                    </p>
                    <footer class="text-sm text-white/60">Participant Portal</footer>
                </blockquote>
            </div>
        </div>

        <!-- Right side with registration form -->
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-2xl font-semibold tracking-tight">Create an Account</h1>
                    <p class="text-sm text-muted-foreground">Register to join events</p>
                </div>

                <div class="grid gap-6">
                    <form action="../../controllers/participant/authController.php" method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <label class="text-sm font-medium leading-none" for="name">Full Name</label>
                                <input
                                    type="text"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    id="name"
                                    name="name"
                                    required
                                >
                            </div>
                            <div class="grid gap-2">
                                <label class="text-sm font-medium leading-none" for="email">Email</label>
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
                                <label class="text-sm font-medium leading-none" for="password">Password</label>
                                <input
                                    type="password"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    id="password"
                                    name="password"
                                    required
                                >
                            </div>
                            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-9 px-4 py-2">
                                Register
                            </button>
                        </div>
                    </form>
                    <p class="text-sm text-center text-muted-foreground">
                        Already have an account? 
                        <a href="participantLogin.php" class="text-primary hover:underline">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 