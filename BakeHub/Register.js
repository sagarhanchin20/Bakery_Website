document.addEventListener("DOMContentLoaded", function() {
    
    const signupForm = document.getElementById("signupForm"); 
    
    const messageDiv = document.getElementById("signupMessage"); 

    if (signupForm) {
        signupForm.addEventListener("submit", function(event) {
            event.preventDefault(); 

            const formData = new FormData(signupForm);
            
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