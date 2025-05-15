<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration System</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        border: "hsl(var(--border))",
                        input: "hsl(var(--input))",
                        ring: "hsl(var(--ring))",
                        background: "hsl(var(--background))",
                        foreground: "hsl(var(--foreground))",
                        primary: {
                            DEFAULT: "hsl(var(--primary))",
                            foreground: "hsl(var(--primary-foreground))",
                        },
                        muted: {
                            DEFAULT: "hsl(var(--muted))",
                            foreground: "hsl(var(--muted-foreground))",
                        },
                        accent: {
                            DEFAULT: "hsl(var(--accent))",
                            foreground: "hsl(var(--accent-foreground))",
                        },
                    },
                },
            },
        }
    </script>
    <style>
        :root {
            --background: 0 0% 100%;  /* White */
            --foreground: 240 10% 3.9%;
            --muted: 240 4.8% 95.9%;
            --muted-foreground: 240 3.8% 46.1%;
            --primary: 240 5.9% 10%;
            --primary-foreground: 0 0% 98%;
            --accent: 240 4.8% 95.9%;
            --accent-foreground: 240 5.9% 10%;
            --border: 240 5.9% 90%;
            --input: 240 5.9% 90%;
            --ring: 240 5.9% 10%;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: hsl(var(--background));  /* White background */
        }

        /* Update component backgrounds */
        .bg-card {
            background-color: hsl(var(--background));
        }

        /* Hover effects */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Smooth transitions */
        .nav-link {
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: hsl(var(--accent));
            color: hsl(var(--accent-foreground));
        }

        /* Card hover effects */
        .stat-card {
            transition: all 0.2s ease;
            background-color: hsl(var(--background));
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: hsl(var(--border));
        }

        /* Button hover effects */
        .btn-hover {
            transition: all 0.2s ease;
        }

        .btn-hover:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Main content area */
        .main-content {
            background-color: hsl(var(--background));
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .btn-primary {
            @apply bg-blue-600 text-white hover:bg-blue-700 transition-colors;
        }

        .btn-secondary {
            @apply bg-gray-100 text-gray-800 hover:bg-gray-200 transition-colors;
        }

        .event-card {
            @apply bg-white;
        }

        .event-card:hover {
            transform: translateY(-2px);
        }

        /* Add to your existing styles */
        .aspect-video {
            aspect-ratio: 16 / 9;
        }

        .event-card:hover .aspect-video img {
            transform: scale(1.05);
        }

        .event-card .aspect-video img {
            transition: transform 0.3s ease;
        }

        .bg-white\/90 {
            background-color: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>
