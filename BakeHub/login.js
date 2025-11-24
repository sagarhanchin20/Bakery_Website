// login.js

document.addEventListener("DOMContentLoaded", function() {
    
    const loginForm = document.getElementById("loginForm"); // This was correct
    
    // 1. Added: A new element to show messages. 
    // You MUST add <div id="loginMessage"></div> to your Login.html inside the .login-form-section
    const messageDiv = document.getElementById("loginMessage"); 

    if (loginForm) {
        loginForm.addEventListener("submit", function(event) {
            event.preventDefault(); 

            const formData = new FormData(loginForm);
            
            // 2. Corrected: Get 'email', not 'username'
            const data = {
                email: formData.get("email"), 
                password: formData.get("password")
            };

            fetch('Login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Success! Redirect to the home page
                    messageDiv.textContent = result.message;
                    messageDiv.style.color = 'green';
                    
                    // Redirect!
                    window.location.href = "_Home.html"; 
                    
                } else {
                    // Show error message
                    messageDiv.textContent = result.message;
                    messageDiv.style.color = 'red';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.textContent = 'An error occurred. Please try again.';
                messageDiv.style.color = 'red';
            });
        });
    }
});