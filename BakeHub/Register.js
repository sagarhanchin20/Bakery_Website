// register.js

document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Corrected: Get the form by its actual ID from Register.html
    const signupForm = document.getElementById("signupForm"); 
    
    // 2. Added: A new element to show messages. 
    // You MUST add <div id="signupMessage"></div> to your Register.html inside the .signup-container
    const messageDiv = document.getElementById("signupMessage"); 

    if (signupForm) {
        signupForm.addEventListener("submit", function(event) {
            // Prevent the form from submitting the traditional way
            event.preventDefault(); 

            // Get form data
            const formData = new FormData(signupForm);
            
            // 3. Corrected: Get the correct fields: 'name', 'email', 'password'
            const data = {
                name: formData.get("name"),
                email: formData.get("email"), 
                password: formData.get("password")
            };

            // Send data to Register.php
            fetch('Register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Success!
                    messageDiv.textContent = result.message;
                    messageDiv.style.color = 'green';
                    
                    // Redirect to login page after a short delay
                    setTimeout(() => {
                        window.location.href = 'Login.html'; 
                    }, 2000); // 2-second delay

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