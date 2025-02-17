<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theatre System Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-slide-up { animation: slideUp 0.5s ease-out; }

        body {
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('/uploads/moview-1024x672.webp'); /* Replace with your image URL */
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            opacity: 0.7; /* Adjust opacity here */
            z-index: 0; /* Ensure it stays behind other content */
        }

        .container {
            position: relative;
            z-index: 10; /* Ensure the container is above the background */
        }
    </style>
</head>
<body class="to-teal-400 min-h-screen flex items-center justify-center">
    <div class="container bg-white rounded-lg shadow-2xl p-8 w-96 animate-slide-up">
        <div class="text-center mb-8">
            <i class="fas fa-ticket-alt text-6xl text-red-500 mb-4"></i>
            <h1 class="text-3xl font-bold text-gray-800">Theatre System Sign Up</h1>
        </div>
        
        <form id="signupForm" class="space-y-6">
            <div>
                <label class="block text-gray-700 mb-2">Username</label>
                <div class="relative">
                    <input type="text" name="username" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500 transition-all"
                        placeholder="Enter your username">
                    <i class="fas fa-user absolute right-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Email</label>
                <div class="relative">
                    <input type="email" name="email" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500 transition-all"
                        placeholder="Enter your email">
                    <i class="fas fa-envelope absolute right-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Password</label>
                <div class="relative">
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-red-500 transition-all"
                        placeholder="Enter your password">
                    <i class="fas fa-lock absolute right-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg transition-all duration-300 transform hover:scale-105">
                Sign Up
            </button>
        </form>

        <p class="text-center mt-4">
            Already have an account? 
            <a href="login.php" class="text-red-500 hover:text-red-600 font-semibold">Login</a>
        </p>

        <div id="message" class="mt-4 text-center hidden"></div>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                action: 'signup',
                username: e.target.username.value,
                email: e.target.email.value,
                password: e.target.password.value
            };

            try {
                const response = await fetch('/api/api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                const messageDiv = document.getElementById('message');
                messageDiv.classList.remove('hidden', 'text-red-500', 'text-green-500');
                
                if (result.success) {
                    messageDiv.classList.add('text-green-500');
                    messageDiv.textContent = result.message;
                    setTimeout(() => window.location.href = 'login.php', 1500);
                } else {
                    messageDiv.classList.add('text-red-500');
                    messageDiv.textContent = result.message;
                }
                messageDiv.classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
            }
        });
    </script>
</body>
</html>